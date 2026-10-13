<?php

namespace App\Service;

use App\Repository\MerchOrderRepository;
use App\Repository\MerchStockRepository;
use App\Repository\DonationRepository;
use App\Repository\PaymentCheckoutRepository;
use App\Repository\TicketOrderRepository;
use App\Repository\UserRepository;
use App\Repository\VisitRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

class AdminDashboardStatisticsService
{
    public function __construct(
        private readonly VisitRepository $visitRepository,
        private readonly MerchOrderRepository $merchOrderRepository,
        private readonly TicketOrderRepository $ticketOrderRepository,
        private readonly DonationRepository $donationRepository,
        private readonly PaymentCheckoutRepository $paymentCheckoutRepository,
        private readonly MerchStockRepository $merchStockRepository,
        private readonly UserRepository $userRepository,
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return array{
     *     period: array{key: string, label: string, comparison: string},
     *     kpis: array<int, array<string, mixed>>,
     *     queues: array<int, array<string, mixed>>,
     *     charts: array<string, array<string, mixed>>,
     *     topMerch: array<int, array<string, mixed>>,
     *     topTickets: array<int, array<string, mixed>>,
     *     topDonations: array<int, array<string, mixed>>,
     *     activity: array<int, array<string, mixed>>
     * }
     */
    public function build(string $periodKey = '30d'): array
    {
        $period = $this->buildPeriod($periodKey);

        $visitsCurrent = $this->visitRepository->countBetween($period['start'], $period['end']);
        $visitsPrevious = $this->visitRepository->countBetween($period['previousStart'], $period['previousEnd']);
        $merchOrdersCurrent = $this->merchOrderRepository->countBetween($period['start'], $period['end']);
        $merchOrdersPrevious = $this->merchOrderRepository->countBetween($period['previousStart'], $period['previousEnd']);
        $ticketOrdersCurrent = $this->ticketOrderRepository->countBetween($period['start'], $period['end']);
        $ticketOrdersPrevious = $this->ticketOrderRepository->countBetween($period['previousStart'], $period['previousEnd']);
        $merchRevenueCurrent = $this->merchOrderRepository->sumTotalBetween($period['start'], $period['end']);
        $merchRevenuePrevious = $this->merchOrderRepository->sumTotalBetween($period['previousStart'], $period['previousEnd']);
        $ticketRevenueCurrent = $this->ticketOrderRepository->sumTotalBetween($period['start'], $period['end']);
        $ticketRevenuePrevious = $this->ticketOrderRepository->sumTotalBetween($period['previousStart'], $period['previousEnd']);
        $ticketQuantityCurrent = $this->ticketOrderRepository->sumQuantityBetween($period['start'], $period['end']);
        $ticketQuantityPrevious = $this->ticketOrderRepository->sumQuantityBetween($period['previousStart'], $period['previousEnd']);
        $merchQuantityCurrent = $this->merchOrderRepository->sumQuantityBetween($period['start'], $period['end']);
        $merchQuantityPrevious = $this->merchOrderRepository->sumQuantityBetween($period['previousStart'], $period['previousEnd']);
        $donationRevenueCurrent = $this->donationRepository->sumPaidBetween($period['start'], $period['end']);
        $donationRevenuePrevious = $this->donationRepository->sumPaidBetween($period['previousStart'], $period['previousEnd']);
        $donationCountCurrent = $this->donationRepository->countPaidBetween($period['start'], $period['end']);
        $donationCountPrevious = $this->donationRepository->countPaidBetween($period['previousStart'], $period['previousEnd']);

        $revenueCurrent = $merchRevenueCurrent + $ticketRevenueCurrent;
        $revenuePrevious = $merchRevenuePrevious + $ticketRevenuePrevious;
        $fundraisingCurrent = $revenueCurrent + $donationRevenueCurrent;
        $fundraisingPrevious = $revenuePrevious + $donationRevenuePrevious;
        $ordersCurrent = $merchOrdersCurrent + $ticketOrdersCurrent;
        $ordersPrevious = $merchOrdersPrevious + $ticketOrdersPrevious;

        $todayStart = new \DateTimeImmutable('today');
        $todayEnd = $todayStart->modify('+1 day -1 second');
        $visitsToday = $this->visitRepository->countBetween($todayStart, $todayEnd);
        $ordersToday = $this->merchOrderRepository->countBetween($todayStart, $todayEnd) + $this->ticketOrderRepository->countBetween($todayStart, $todayEnd);
        $revenueToday = $this->merchOrderRepository->sumTotalBetween($todayStart, $todayEnd) + $this->ticketOrderRepository->sumTotalBetween($todayStart, $todayEnd);
        $donationsToday = $this->donationRepository->countPaidBetween($todayStart, $todayEnd);
        $donationRevenueToday = $this->donationRepository->sumPaidBetween($todayStart, $todayEnd);

        $series = $this->buildSeries($period['start'], $period['end'], $period['granularity']);
        $visitSeries = $this->mergeSeries(
            $series,
            $this->fetchSeries('visit', 'visited_at', $period['start'], $period['end'], $period['granularity'], false)
        );
        $merchRevenueSeries = $this->fetchSeries('merch_order', 'created_at', $period['start'], $period['end'], $period['granularity'], true);
        $ticketRevenueSeries = $this->fetchSeries('ticket_order', 'created_at', $period['start'], $period['end'], $period['granularity'], true);
        $donationRevenueSeries = $this->fetchSeries('donation', 'paid_at', $period['start'], $period['end'], $period['granularity'], true, 'amount');
        $revenueSeries = $this->mergeSeries($series, $merchRevenueSeries, $ticketRevenueSeries);
        $fundraisingSeries = $this->mergeSeries($series, $merchRevenueSeries, $ticketRevenueSeries, $donationRevenueSeries);
        $orderSeries = $this->mergeSeries(
            $series,
            $this->fetchSeries('merch_order', 'created_at', $period['start'], $period['end'], $period['granularity'], false),
            $this->fetchSeries('ticket_order', 'created_at', $period['start'], $period['end'], $period['granularity'], false),
        );
        $donationCountSeries = $this->fetchSeries('donation', 'paid_at', $period['start'], $period['end'], $period['granularity'], false);

        $topMerch = $this->formatTopItems($this->merchOrderRepository->findTopProducts($period['start'], $period['end'], 5));
        $topTickets = $this->formatTopItems($this->ticketOrderRepository->findTopMatches($period['start'], $period['end'], 5));
        $topDonations = array_map(static function ($donation): array {
            return [
                'id' => $donation->getId() ?? 0,
                'label' => $donation->getDisplayName(),
                'quantity' => 1,
                'revenue' => $donation->getAmount(),
                'formatted' => number_format($donation->getAmount(), 2, ',', ' ') . ' EUR',
                'date' => $donation->getPaidAt() ?? $donation->getCreatedAt(),
                'status' => $donation->getPaymentStatusTranslationKey(),
            ];
        }, $this->donationRepository->findTopPaid(5));
        $ticketMatches = $this->formatMatchBreakdown($this->ticketOrderRepository->findMatchBreakdown($period['start'], $period['end'], 12));
        $lowStock = $this->merchStockRepository->findLowStock(5);
        $pendingCheckouts = $this->paymentCheckoutRepository->countPending();
        $merchToConfirm = $this->merchOrderRepository->countPendingConfirmation();
        $merchToPrepare = $this->merchOrderRepository->countConfirmedAwaitingExecution();
        $recentDonations = $this->donationRepository->findRecentPaid(4);

        $activity = [];
        foreach (array_slice($this->merchOrderRepository->findRecent(4), 0, 4) as $order) {
            $activity[] = [
                'date' => $order->getConfirmedAt() ?? $order->getCreatedAt(),
                'label' => $order->isConfirmed() ? 'Commande merch confirmée' : 'Commande merch créée',
                'title' => sprintf('%s • %s', $order->getMerch()?->getTitle() ?? 'Produit', $order->getTotalPrice() ? number_format($order->getTotalPrice(), 2, ',', ' ') . ' EUR' : '0 EUR'),
                'meta' => sprintf('#%d • %s', $order->getId() ?? 0, $order->getWorkflowStateLabel()),
                'tone' => $order->getWorkflowState(),
            ];
        }

        foreach (array_slice($this->ticketOrderRepository->findRecent(4), 0, 4) as $order) {
            $activity[] = [
                'date' => $order->getCreatedAt(),
                'label' => $order->isPaid() ? 'Commande billet réglée' : 'Commande billet en attente',
                'title' => sprintf('%s • %d billet(s)', $order->getTicket()?->getTitle() ?? 'Billet', $order->getQuantity()),
                'meta' => sprintf('#%d • %s', $order->getId() ?? 0, $order->isPaid() ? 'Payée' : 'En attente'),
                'tone' => $order->isPaid() ? 'success' : 'warning',
            ];
        }

        foreach (array_slice($recentDonations, 0, 4) as $donation) {
            $activity[] = [
                'date' => $donation->getPaidAt() ?? $donation->getCreatedAt(),
                'label' => 'Don reçu',
                'title' => sprintf('%s • %s', $donation->getDisplayName(), $this->money($donation->getAmount())),
                'meta' => sprintf('#%d • %s', $donation->getId() ?? 0, $donation->getPaymentStatusTranslationKey()),
                'tone' => 'success',
            ];
        }

        usort($activity, static function (array $left, array $right): int {
            $leftDate = $left['date'] instanceof \DateTimeInterface ? $left['date']->getTimestamp() : 0;
            $rightDate = $right['date'] instanceof \DateTimeInterface ? $right['date']->getTimestamp() : 0;

            return $rightDate <=> $leftDate;
        });

        $activity = array_map(static function (array $item): array {
            return [
                'date' => $item['date'] instanceof \DateTimeInterface ? $item['date'] : null,
                'label' => $item['label'],
                'title' => $item['title'],
                'meta' => $item['meta'],
                'tone' => $item['tone'],
            ];
        }, array_slice($activity, 0, 8));

        return [
            'period' => [
                'key' => $period['key'],
                'label' => $period['label'],
                'comparison' => $period['comparisonLabel'],
            ],
            'kpis' => [
                [
                    'label' => 'Visites',
                    'value' => $visitsCurrent,
                    'trend' => $this->buildTrend($visitsCurrent, $visitsPrevious),
                    'help' => 'Trafic mesuré sur la période.',
                ],
                [
                    'label' => 'CA',
                    'value' => $revenueCurrent,
                    'formatted' => $this->money($revenueCurrent),
                    'trend' => $this->buildTrend($revenueCurrent, $revenuePrevious),
                    'help' => 'Boutique et billetterie réelles.',
                ],
                [
                    'label' => 'Dons',
                    'value' => $donationRevenueCurrent,
                    'formatted' => $this->money($donationRevenueCurrent),
                    'trend' => $this->buildTrend($donationRevenueCurrent, $donationRevenuePrevious),
                    'help' => 'Soutiens via la boutique.',
                ],
                [
                    'label' => 'Donateurs',
                    'value' => $donationCountCurrent,
                    'trend' => $this->buildTrend($donationCountCurrent, $donationCountPrevious),
                    'help' => 'Dons validés sur la période.',
                ],
                [
                    'label' => 'Commandes',
                    'value' => $ordersCurrent,
                    'trend' => $this->buildTrend($ordersCurrent, $ordersPrevious),
                    'help' => 'Commandes merch et billet.',
                ],
                [
                    'label' => 'Billets vendus',
                    'value' => $ticketQuantityCurrent,
                    'trend' => $this->buildTrend($ticketQuantityCurrent, $ticketQuantityPrevious),
                    'help' => 'Quantité réellement commandée.',
                ],
                [
                    'label' => 'Produits vendus',
                    'value' => $merchQuantityCurrent,
                    'trend' => $this->buildTrend($merchQuantityCurrent, $merchQuantityPrevious),
                    'help' => 'Quantité merch réellement vendue.',
                ],
                [
                    'label' => 'Utilisateurs',
                    'value' => $this->userRepository->count([]),
                    'help' => 'Comptes enregistrés dans la base.',
                ],
            ],
            'queues' => [
                [
                    'label' => 'Commandes merch à confirmer',
                    'value' => $merchToConfirm,
                    'tone' => 'warning',
                    'summary' => 'Action métier immédiate.',
                    'state' => 'pending_confirmation',
                ],
                [
                    'label' => 'Commandes merch à préparer',
                    'value' => $merchToPrepare,
                    'tone' => 'info',
                    'summary' => 'Confirmées mais non exécutées.',
                    'state' => 'to_prepare',
                ],
                [
                    'label' => 'Paiements à vérifier',
                    'value' => $pendingCheckouts,
                    'tone' => 'danger',
                    'summary' => 'Transactions en attente de validation.',
                    'resource' => 'paiements',
                ],
                [
                    'label' => 'Stocks faibles',
                    'value' => $this->merchStockRepository->countLowStock(5),
                    'tone' => 'warning',
                    'summary' => 'Variantes à surveiller avant rupture.',
                    'resource' => 'stocks-merch',
                ],
            ],
            'charts' => [
                'visits' => [
                    'label' => 'Visites',
                    'series' => $visitSeries,
                    'max' => $this->maxSeriesValue($visitSeries),
                ],
                'revenue' => [
                    'label' => 'Chiffre d\'affaires',
                    'series' => $revenueSeries,
                    'max' => $this->maxSeriesValue($revenueSeries),
                ],
                'fundraising' => [
                    'label' => 'CA + dons',
                    'series' => $fundraisingSeries,
                    'max' => $this->maxSeriesValue($fundraisingSeries),
                ],
                'orders' => [
                    'label' => 'Commandes',
                    'series' => $orderSeries,
                    'max' => $this->maxSeriesValue($orderSeries),
                ],
                'donations' => [
                    'label' => 'Dons',
                    'series' => $donationCountSeries,
                    'max' => $this->maxSeriesValue($donationCountSeries),
                ],
            ],
            'topMerch' => $topMerch,
            'topTickets' => $topTickets,
            'topDonations' => $topDonations,
            'ticketMatches' => $ticketMatches,
            'lowStock' => array_map(static fn ($stock): array => [
                'title' => $stock->getMerch()?->getTitle() ?? 'Produit',
                'size' => $stock->getSize(),
                'quantity' => $stock->getQuantity(),
            ], $lowStock),
            'activity' => $activity,
            'summary' => [
                'visitsToday' => $visitsToday,
                'ordersToday' => $ordersToday,
                'revenueToday' => $revenueToday,
                'donationRevenueToday' => $donationRevenueToday,
                'donationsToday' => $donationsToday,
                'pendingCheckouts' => $pendingCheckouts,
                'merchToConfirm' => $merchToConfirm,
                'merchToPrepare' => $merchToPrepare,
                'donationCountCurrent' => $donationCountCurrent,
                'donationCountPrevious' => $donationCountPrevious,
                'fundraisingCurrent' => $fundraisingCurrent,
                'fundraisingPrevious' => $fundraisingPrevious,
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, comparisonLabel: string, start: \DateTimeImmutable, end: \DateTimeImmutable, previousStart: \DateTimeImmutable, previousEnd: \DateTimeImmutable, granularity: 'day'|'month'}
     */
    private function buildPeriod(string $periodKey): array
    {
        $now = new \DateTimeImmutable();
        $key = in_array($periodKey, ['7d', '30d', 'month', 'year'], true) ? $periodKey : '30d';

        return match ($key) {
            '7d' => [
                'key' => $key,
                'label' => '7 derniers jours',
                'comparisonLabel' => 'par rapport aux 7 jours précédents',
                'start' => $now->modify('-6 days')->setTime(0, 0),
                'end' => $now,
                'previousStart' => $now->modify('-13 days')->setTime(0, 0),
                'previousEnd' => $now->modify('-7 days')->setTime(23, 59, 59),
                'granularity' => 'day',
            ],
            'month' => [
                'key' => $key,
                'label' => 'Ce mois',
                'comparisonLabel' => 'par rapport au mois précédent',
                'start' => $now->modify('first day of this month')->setTime(0, 0),
                'end' => $now,
                'previousStart' => $now->modify('first day of last month')->setTime(0, 0),
                'previousEnd' => $now->modify('last day of last month')->setTime(23, 59, 59),
                'granularity' => 'day',
            ],
            'year' => [
                'key' => $key,
                'label' => 'Cette année',
                'comparisonLabel' => 'par rapport à l\'année précédente',
                'start' => $now->modify('first day of january this year')->setTime(0, 0),
                'end' => $now,
                'previousStart' => $now->modify('first day of january last year')->setTime(0, 0),
                'previousEnd' => $now->modify('last day of december last year')->setTime(23, 59, 59),
                'granularity' => 'month',
            ],
            default => [
                'key' => '30d',
                'label' => '30 derniers jours',
                'comparisonLabel' => 'par rapport aux 30 jours précédents',
                'start' => $now->modify('-29 days')->setTime(0, 0),
                'end' => $now,
                'previousStart' => $now->modify('-59 days')->setTime(0, 0),
                'previousEnd' => $now->modify('-30 days')->setTime(23, 59, 59),
                'granularity' => 'day',
            ],
        };
    }

    /**
     * @return array<int, array{label: string, value: float}>
     */
    private function buildSeries(\DateTimeImmutable $start, \DateTimeImmutable $end, string $granularity): array
    {
        $series = [];
        if ($granularity === 'month') {
            $cursor = $start->modify('first day of this month');
            $limit = $end->modify('first day of next month');

            while ($cursor < $limit) {
                $series[$cursor->format('Y-m')] = [
                    'label' => $cursor->format('m/Y'),
                    'value' => 0.0,
                ];
                $cursor = $cursor->modify('first day of next month');
            }

            return array_values($series);
        }

        $cursor = $start->setTime(0, 0);
        $limit = $end->setTime(0, 0);
        while ($cursor <= $limit) {
            $series[$cursor->format('Y-m-d')] = [
                'label' => $cursor->format('d/m'),
                'value' => 0.0,
            ];
            $cursor = $cursor->modify('+1 day');
        }

        return array_values($series);
    }

    /**
     * @return array<int, array{label: string, value: float}>
     */
    private function fetchSeries(string $table, string $dateColumn, \DateTimeImmutable $start, \DateTimeImmutable $end, string $granularity, bool $revenue = false, string $metricColumn = 'total_price'): array
    {
        if ($granularity === 'month') {
            $rows = $this->connection->fetchAllAssociative(
                sprintf(
                    'SELECT DATE_FORMAT(%1$s, \'%%Y-%%m\') AS bucket, %2$s AS metric
                     FROM %3$s
                     WHERE %1$s BETWEEN :from AND :to
                     GROUP BY DATE_FORMAT(%1$s, \'%%Y-%%m\')
                     ORDER BY bucket ASC',
                    $dateColumn,
                    $revenue ? sprintf('COALESCE(SUM(%s), 0)', $metricColumn) : 'COUNT(*)',
                    $table
                ),
                ['from' => $start, 'to' => $end],
                ['from' => Types::DATETIME_IMMUTABLE, 'to' => Types::DATETIME_IMMUTABLE]
            );

            $index = [];
            foreach ($rows as $row) {
                $index[(string) ($row['bucket'] ?? '')] = (float) ($row['metric'] ?? 0);
            }

            $series = [];
            $cursor = $start->modify('first day of this month');
            $limit = $end->modify('first day of next month');
            while ($cursor < $limit) {
                $bucket = $cursor->format('Y-m');
                $series[] = [
                    'label' => $cursor->format('m/Y'),
                    'value' => $index[$bucket] ?? 0.0,
                ];
                $cursor = $cursor->modify('first day of next month');
            }

            return $series;
        }

        $rows = $this->connection->fetchAllAssociative(
            sprintf(
                'SELECT DATE(%1$s) AS bucket, %2$s AS metric
                 FROM %3$s
                 WHERE %1$s BETWEEN :from AND :to
                 GROUP BY DATE(%1$s)
                 ORDER BY bucket ASC',
                $dateColumn,
                $revenue ? sprintf('COALESCE(SUM(%s), 0)', $metricColumn) : 'COUNT(*)',
                $table
            ),
            ['from' => $start, 'to' => $end],
            ['from' => Types::DATETIME_IMMUTABLE, 'to' => Types::DATETIME_IMMUTABLE]
        );

        $index = [];
        foreach ($rows as $row) {
            $index[(string) ($row['bucket'] ?? '')] = (float) ($row['metric'] ?? 0);
        }

        $series = [];
        $cursor = $start->setTime(0, 0);
        $limit = $end->setTime(0, 0);
        while ($cursor <= $limit) {
            $bucket = $cursor->format('Y-m-d');
            $series[] = [
                'label' => $cursor->format('d/m'),
                'value' => $index[$bucket] ?? 0.0,
            ];
            $cursor = $cursor->modify('+1 day');
        }

        return $series;
    }

    /**
     * @param array<int, array{label: string, value: float}> ...$seriesList
     * @return array<int, array{label: string, value: float}>
     */
    private function mergeSeries(array $baseSeries, array ...$seriesList): array
    {
        $merged = [];

        foreach ($baseSeries as $index => $entry) {
            $merged[$index] = [
                'label' => $entry['label'],
                'value' => 0.0,
            ];
        }

        foreach ($seriesList as $series) {
            foreach ($series as $index => $entry) {
                $merged[$index]['value'] += (float) ($entry['value'] ?? 0);
            }
        }

        return array_values($merged);
    }

    /**
     * @param array<int, mixed> $rows
     * @return array<int, array{label: string, quantity: int, revenue: float}>
     */
    private function formatTopItems(array $rows): array
    {
        return array_map(static fn (array $row): array => [
            'label' => (string) ($row['title'] ?? ''),
            'quantity' => (int) ($row['quantity'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
        ], $rows);
    }

    /**
     * @param array<int, mixed> $rows
     * @return array<int, array{label: string, opponent: string, matchDate: string, quantity: int, orders: int, revenue: float}>
     */
    private function formatMatchBreakdown(array $rows): array
    {
        return array_map(static fn (array $row): array => [
            'label' => (string) ($row['title'] ?? ''),
            'opponent' => (string) ($row['opponent'] ?? ''),
            'matchDate' => (string) ($row['match_date'] ?? ''),
            'quantity' => (int) ($row['quantity'] ?? 0),
            'orders' => (int) ($row['orders'] ?? 0),
            'revenue' => (float) ($row['revenue'] ?? 0),
        ], $rows);
    }

    private function buildTrend(float|int $current, float|int $previous): array
    {
        $delta = (float) $current - (float) $previous;
        if (abs((float) $previous) < 0.0001) {
            return [
                'direction' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'),
                'label' => $delta > 0 ? 'Nouveau' : 'Stable',
                'percent' => null,
                'tone' => $delta > 0 ? 'success' : ($delta < 0 ? 'danger' : 'neutral'),
            ];
        }

        $percent = ($delta / (float) $previous) * 100;

        return [
            'direction' => $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat'),
            'label' => sprintf('%s%s%%', $percent > 0 ? '+' : '', number_format($percent, 1, ',', ' ')),
            'percent' => $percent,
            'tone' => $percent > 0 ? 'success' : ($percent < 0 ? 'danger' : 'neutral'),
        ];
    }

    private function maxSeriesValue(array $series): float
    {
        $values = array_map(static fn (array $entry): float => (float) ($entry['value'] ?? 0), $series);

        return max(1.0, max($values ?: [0]));
    }

    private function money(float $value): string
    {
        return number_format($value, 2, ',', ' ') . ' EUR';
    }
}
