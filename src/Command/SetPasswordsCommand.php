<?php

namespace App\Command;

use App\Entity\Employe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:set-passwords',
    description: 'Add a short description for your command',
)]
class SetPasswordsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(Employe::class);
        $employes = $repo->findAll();

        $count = 0;
        foreach ($employes as $employe) {
            if (!$employe->getPassword()) {
                // 🔐 définit un mot de passe par défaut ou unique
                $plainPassword = 'agduc'; // ou générer un mot de passe aléatoire
                $hashedPassword = $this->passwordHasher->hashPassword($employe, $plainPassword);
                $employe->setPassword($hashedPassword);
                $count++;

                $output->writeln("→ Employé {$employe->getEmail()} : mot de passe = {$plainPassword}");
            }
        }

        $this->em->flush();

        $output->writeln("✅ $count mot(s) de passe ajouté(s)");
        return Command::SUCCESS;
    }
}
