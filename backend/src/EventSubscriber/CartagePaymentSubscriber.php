<?php

namespace App\EventSubscriber;

use App\Entity\CartageRegistration;
use App\Entity\PaymentCheckout;
use App\Service\CartageProfileCreator;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
class CartagePaymentSubscriber
{
    /**
     * @var array<int, CartageRegistration>
     */
    private array $profilesToCreate = [];

    private bool $creatingProfiles = false;

    public function __construct(
        private CartageProfileCreator $profileCreator,
        private LoggerInterface $logger,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        if ($this->creatingProfiles) {
            return;
        }

        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof PaymentCheckout) {
                continue;
            }

            if (!method_exists($entity, 'getType') || !method_exists($entity, 'getStatus') || !method_exists($entity, 'getCheckoutReference')) {
                continue;
            }

            if ($entity->getType() !== 'cartage' || !$this->isPaidStatus((string) $entity->getStatus())) {
                continue;
            }

            $reference = (string) $entity->getCheckoutReference();
            if ($reference === '') {
                continue;
            }

            $registration = $entityManager->getRepository(CartageRegistration::class)->findOneBy([
                'checkoutReference' => $reference,
            ]);

            if (!$registration instanceof CartageRegistration || $registration->getStatus() === CartageRegistration::STATUS_PAID_ONLINE) {
                continue;
            }

            $registration->markPaidOnline();

            $entityManager->persist($registration);
            $unitOfWork->computeChangeSet($entityManager->getClassMetadata(CartageRegistration::class), $registration);
            $this->profilesToCreate[spl_object_id($registration)] = $registration;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->creatingProfiles || $this->profilesToCreate === []) {
            return;
        }

        $registrations = $this->profilesToCreate;
        $this->profilesToCreate = [];
        $this->creatingProfiles = true;

        try {
            $entityManager = $args->getObjectManager();
            $hasProfileToFlush = false;

            foreach ($registrations as $registration) {
                if ($this->profileCreator->createProfileIfNeeded($registration) !== null) {
                    $hasProfileToFlush = true;
                }
            }

            if ($hasProfileToFlush) {
                $entityManager->flush();
            }
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to create cartage profile after payment confirmation.', [
                'exception' => $exception,
            ]);
        } finally {
            $this->creatingProfiles = false;
        }
    }

    private function isPaidStatus(string $status): bool
    {
        return in_array(strtolower($status), ['paid', 'success', 'successful', 'completed'], true);
    }
}
