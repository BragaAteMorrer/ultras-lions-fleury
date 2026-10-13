<?php

namespace App\Service;

use App\Entity\Gadget;
use App\Entity\Media;
use App\Entity\Merch;
use App\Repository\GadgetRepository;
use Doctrine\ORM\EntityManagerInterface;

class MerchGadgetHistorySynchronizer
{
    public function __construct(private readonly GadgetRepository $gadgetRepository)
    {
    }

    public function sync(Merch $merch, EntityManagerInterface $entityManager): void
    {
        $gadget = $merch->getGadgetHistoryEntry();

        if (!$gadget instanceof Gadget && $merch->getId() !== null) {
            $gadget = $this->gadgetRepository->findOneBy(['sourceMerch' => $merch]);
        }

        if (!$merch->isDisplayInGadgetHistory()) {
            if ($gadget instanceof Gadget) {
                $gadget->setVisible(false);
                $entityManager->flush();
            }

            return;
        }

        if (!$gadget instanceof Gadget) {
            $gadget = (new Gadget())
                ->setSourceMerch($merch)
                ->setPosition(0);

            $entityManager->persist($gadget);
        }

        $gadget
            ->setTitle((string) $merch->getTitle())
            ->setDescription($this->normalizeDescription($merch->getDescription()))
            ->setProductionLabel($this->buildProductionLabel($merch))
            ->setImage($this->resolveImage($merch))
            ->setVisible(true);

        $merch->setGadgetHistoryEntry($gadget);
        $entityManager->flush();
    }

    private function normalizeDescription(?string $description): ?string
    {
        $description = trim(strip_tags((string) $description));

        return $description !== '' ? $description : null;
    }

    private function buildProductionLabel(Merch $merch): string
    {
        $category = trim((string) $merch->getCategory()?->getName());
        $year = (new \DateTimeImmutable())->format('Y');

        return $category !== '' ? sprintf('%s - %s', $category, $year) : sprintf('Boutique - %s', $year);
    }

    private function resolveImage(Merch $merch): ?Media
    {
        if ($merch->getImage() instanceof Media) {
            return $merch->getImage();
        }

        $media = $merch->getMedia()->first();

        return $media instanceof Media ? $media : null;
    }
}
