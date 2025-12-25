<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin'
)]
class CreateAdmin extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль администратора')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'Имя пользователя', 'Admin')
            ->addArgument('phone', InputArgument::REQUIRED, 'Телефон пользователя')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Создание администратора');

        $phone = $input->getArgument('phone');
        $password = $input->getArgument('password');

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        try {
            $user = new User();
            $user->setPhone($phone);
            $io->note('Создание нового пользователя...');
        } catch (Exception $e) {
        }

        $user->setName($input->getOption('name'));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Администратор успешно создан!');

        $io->table(
            ['Поле', 'Значение'],
            [
                ['ID', $user->getId()],
                ['Имя', $user->getName()],
                ['Телефон', $user->getPhone()],
                ['Роли', implode(', ', $user->getRoles())],
            ]
        );

        return Command::SUCCESS;
    }
}
