<?php

namespace App\Command;

use App\Entity\Member;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new Member();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setRole(Role::MEMBER);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully.');

        return Command::SUCCESS;
    }
}
