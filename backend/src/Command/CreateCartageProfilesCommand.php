<php

namespace App\Command;

use App\Repository\CartageRegistrationRepository;
use App\Service\CartageProfileCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cartage:create-profiles',
    description: 'Create missing user profiles from cartage registrations.',
)]
class CreateCartageProfilesCommand extends Command
{
    public function __construct(
        private CartageRegistrationRepository $registrationRepository,
        private CartageProfileCreator $profileCreator,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $createdOrUpdated = 0;

        foreach ($this->registrationRepository->findAll() as $registration) {
            $user = $this->profileCreator->createProfileIfNeeded($registration);
            if ($user !== null) {
                $createdOrUpdated++;
            }
        }

        $this->entityManager->flush();
        $io->success(sprintf('%d profil(s) cree(s) ou mis a jour depuis le cartage.', $createdOrUpdated));

        return Command::SUCCESS;
    }
}
