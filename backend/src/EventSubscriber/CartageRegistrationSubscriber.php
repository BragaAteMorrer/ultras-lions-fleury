<php

namespace App\EventSubscriber;

use App\Entity\CartageRegistration;
use App\Service\CartageProfileCreator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
class CartageRegistrationSubscriber
{
    /**
     * @var array<int, CartageRegistration>
     */
    private array $registrationsToProcess = [];

    private bool $creatingProfiles = false;

    public function __construct(
        private readonly CartageProfileCreator $profileCreator,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        if ($this->creatingProfiles) {
            return;
        }

        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof CartageRegistration) {
                continue;
            }

            if ($this->isPaidOnline($entity)) {
                $this->registrationsToProcess[spl_object_id($entity)] = $entity;
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof CartageRegistration) {
                continue;
            }

            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            if (!isset($changeSet['status'])) {
                continue;
            }

            $newStatus = (string) ($changeSet['status'][1]  '');
            if (!$this->isPaidOnline($entity, $newStatus)) {
                continue;
            }

            $this->registrationsToProcess[spl_object_id($entity)] = $entity;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->creatingProfiles || $this->registrationsToProcess === []) {
            return;
        }

        $registrations = $this->registrationsToProcess;
        $this->registrationsToProcess = [];
        $this->creatingProfiles = true;

        try {
            $entityManager = $args->getObjectManager();
            $hasChanges = false;

            foreach ($registrations as $registration) {
                $user = $this->profileCreator->createProfileIfNeeded($registration);
                if ($user !== null) {
                    $hasChanges = true;
                    $this->logger->info('Cartage registration triggered profile creation.', [
                        'cartage_registration_id' => $registration->getId(),
                        'email' => $registration->getEmail(),
                        'user_id' => $user->getId(),
                    ]);
                }
            }

            if ($hasChanges) {
                $entityManager->flush();
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to create cartage profile from registration update.', [
                'exception' => $exception,
            ]);
        } finally {
            $this->creatingProfiles = false;
        }
    }

    private function isPaidOnline(CartageRegistration $registration, string $status = null): bool
    {
        $status = $status !== null  $status : $registration->getStatus();

        return strtolower($status) === CartageRegistration::STATUS_PAID_ONLINE;
    }
}
