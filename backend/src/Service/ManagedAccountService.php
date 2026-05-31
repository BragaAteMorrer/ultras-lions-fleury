<php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserAccountLinkRepository;

class ManagedAccountService
{
    public function __construct(
        private readonly UserAccountLinkRepository $linkRepository,
    ) {
    }

    /**
     * @return User[]
     */
    public function getOrderableUsers(User $user): array
    {
        $users = [$user];

        foreach ($this->linkRepository->findAcceptedForGuardian($user) as $link) {
            $managedUser = $link->getManagedUser();
            if ($managedUser instanceof User) {
                $users[] = $managedUser;
            }
        }

        return $users;
    }

    public function resolveOrderUser(User $currentUser, mixed $requestedId): User
    {
        $requestedId = (int) $requestedId;
        if ($requestedId <= 0 || $requestedId === $currentUser->getId()) {
            return $currentUser;
        }

        foreach ($this->linkRepository->findAcceptedForGuardian($currentUser) as $link) {
            $managedUser = $link->getManagedUser();
            if ($managedUser instanceof User && $managedUser->getId() === $requestedId) {
                return $managedUser;
            }
        }

        return $currentUser;
    }

    public function canAccessUser(User $currentUser, User $targetUser): bool
    {
        if ($currentUser->getId() === $targetUser->getId()) {
            return true;
        }

        foreach ($this->linkRepository->findAcceptedForGuardian($currentUser) as $link) {
            if ($link->getManagedUser()->getId() === $targetUser->getId()) {
                return true;
            }
        }

        return false;
    }
}
