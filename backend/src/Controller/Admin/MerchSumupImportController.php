<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\MerchSumupImportType;
use App\Service\AdminNavigationBuilder;
use App\Service\MerchPhysicalSaleManager;
use App\Service\MerchStockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/manage/rapprochement-sumup/merch')]
class MerchSumupImportController extends AbstractController
{
    public function __construct(
        private readonly AdminNavigationBuilder $adminNavigationBuilder,
        private readonly MerchStockManager $stockManager,
        private readonly MerchPhysicalSaleManager $physicalSaleManager,
    ) {
    }

    #[Route('', name: 'app_admin_merch_sumup_import', methods: ['GET', 'POST'])]
    public function import(Request $request): Response
    {
        $form = $this->createForm(MerchSumupImportType::class);
        $form->handleRequest($request);
        $report = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('csvFile')->getData();
            if ($file instanceof UploadedFile) {
                try {
                    $report = $this->importCsv($file);
                    $this->addFlash('success', sprintf(
                        '%d ligne(s) importee(s), %d erreur(s).',
                        $report['processed'],
                        count($report['errors'])
                    ));
                } catch (\Throwable $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }
        }

        return $this->render('admin/merch_sumup_import/new.html.twig', [
            'form' => $form->createView(),
            'report' => $report,
            'adminNavigation' => $this->adminNavigationBuilder->navigation(),
        ]);
    }

    /**
     * @return array{processed: int, skipped: int, errors: array<int, array{row: int, message: string}>}
     */
    private function importCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getPathname(), 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Impossible de lire le fichier CSV.');
        }

        $delimiter = $this->detectDelimiter($file->getPathname());
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!is_array($headers)) {
            fclose($handle);
            throw new \RuntimeException('Le CSV ne contient pas d\'en-tete.');
        }

        $headers = array_map([$this, 'normalizeHeader'], $headers);
        $processed = 0;
        $skipped = 0;
        $errors = [];
        $lineNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;
            if (!is_array($row) || $this->isRowEmpty($row)) {
                continue;
            }

            $data = $this->mapRow($headers, $row);
            $sku = $this->firstValue($data, ['sumup_sku', 'sku', 'item_sku', 'product_sku']);
            $quantity = $this->parsePositiveInt($this->firstValue($data, ['quantity', 'qty', 'sold_quantity', 'count']));
            $reference = $this->firstValue($data, ['sumup_reference', 'reference', 'transaction_id', 'transaction', 'checkout_reference']);
            $date = $this->parseDate($this->firstValue($data, ['date', 'created_at', 'sold_at', 'timestamp', 'sale_date']));
            $unitPrice = $this->parsePrice($data);

            if ($sku === '') {
                $errors[] = ['row' => $lineNumber, 'message' => 'SKU manquant.'];
                $skipped++;
                continue;
            }

            if ($quantity <= 0) {
                $errors[] = ['row' => $lineNumber, 'message' => 'Quantite invalide.'];
                $skipped++;
                continue;
            }

            $stock = $this->stockManager->findStockBySku($sku);
            if (!$stock) {
                $errors[] = ['row' => $lineNumber, 'message' => sprintf('Aucun stock local pour le SKU %s.', $sku)];
                $skipped++;
                continue;
            }

            try {
                $this->physicalSaleManager->recordSale(
                    merch: $stock->getMerch(),
                    size: $stock->getSize(),
                    quantity: $quantity,
                    paymentMethod: 'sumup_import',
                    useMemberPrice: false,
                    sumupReference: $reference !== '' ? $reference : $sku,
                    note: 'Import CSV SumUp',
                    confirmedBy: $this->getUser() instanceof User ? $this->getUser() : null,
                    occurredAt: $date,
                    unitPrice: $unitPrice,
                );
                $processed++;
            } catch (\Throwable $exception) {
                $errors[] = ['row' => $lineNumber, 'message' => $exception->getMessage()];
                $skipped++;
            }
        }

        fclose($handle);

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return ';';
        }

        $line = (string) fgets($handle) ?: '';
        fclose($handle);

        $semicolons = substr_count($line, ';');
        $commas = substr_count($line, ',');

        return $semicolons >= $commas ? ';' : ',';
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = trim(mb_strtolower($header));
        $header = str_replace([' ', '-', '.'], '_', $header);

        return preg_replace('/[^a-z0-9_]/', '', $header) ?? $header;
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string, string>
     */
    private function mapRow(array $headers, array $row): array
    {
        $data = [];
        foreach ($headers as $index => $header) {
            $data[$header] = trim((string) ($row[$index] ?? ''));
        }

        return $data;
    }

    /**
     * @param array<string, string> $data
     * @param string[] $keys
     */
    private function firstValue(array $data, array $keys): string
    {
        foreach ($keys as $key) {
            $value = trim((string) ($data[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function parsePositiveInt(string $value): int
    {
        if ($value === '') {
            return 0;
        }

        return max(0, (int) preg_replace('/[^0-9-]/', '', $value));
    }

    /**
     * @param array<string, string> $data
     */
    private function parsePrice(array $data): ?float
    {
        $unit = $this->firstValue($data, ['unit_price', 'price', 'amount']);
        if ($unit !== '') {
            return (float) str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', $unit) ?? $unit);
        }

        $total = $this->firstValue($data, ['total_price', 'total']);
        $quantity = $this->parsePositiveInt($this->firstValue($data, ['quantity', 'qty', 'sold_quantity', 'count']));

        if ($total !== '' && $quantity > 0) {
            return (float) str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', $total) ?? $total) / $quantity;
        }

        return null;
    }

    private function parseDate(string $value): ?\DateTimeInterface
    {
        if ($value === '') {
            return null;
        }

        $candidates = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y',
            \DateTimeInterface::ATOM,
        ];

        foreach ($candidates as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            if ($date instanceof \DateTimeImmutable) {
                return $date;
            }
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
