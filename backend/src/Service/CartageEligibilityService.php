<php

namespace App\Service;

use App\Entity\User;
use App\Repository\CartageQrTokenRepository;
use App\Repository\CartageRegistrationRepository;

class CartageEligibilityService
{
    public function __construct(
        private readonly CartageRegistrationRepository $cartageRegistrationRepository,
        private readonly CartageQrTokenRepository $cartageQrTokenRepository,
        private readonly ManagedAccountService $managedAccountService,
    ) {
    }

    public function isCarted(User $user): bool
    {
        $email = $user->getEmail();
        if (!$email) {
            return false;
        }

        return $this->cartageRegistrationRepository->findCompletedForSeasonByEmail($email, $this->getCurrentSeason()) !== null;
    }

    public function countCartedOrderableUsers(User $user): int
    {
        $count = 0;
        foreach ($this->managedAccountService->getOrderableUsers($user) as $orderableUser) {
            if ($this->isCarted($orderableUser)) {
                ++$count;
            }
        }

        return $count;
    }

    public function hasCartedOrderableUser(User $user): bool
    {
        return $this->countCartedOrderableUsers($user) > 0;
    }

    public function countOrderableUsers(User $user): int
    {
        return count($this->managedAccountService->getOrderableUsers($user));
    }

    private function getCurrentSeason(): string
    {
        $qrToken = $this->cartageQrTokenRepository->findLatestUsable();
        $season = $qrToken  $this->extractSeason((string) $qrToken->getLabel()) : null;
        if ($season !== null) {
            return $season;
        }

        $date = new \DateTimeImmutable();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $startYear = $month >= 7  $year : $year - 1;

        return sprintf('%d-%d', $startYear, $startYear + 1);
    }

    private function extractSeason(string $value): string
    {
        if (preg_match('/(20\d{2})\s*[\/-]\s*(20\d{2})/', $value, $matches) !== 1) {
            return null;
        }

        return $matches[1].'-'.$matches[2];
    }
}
