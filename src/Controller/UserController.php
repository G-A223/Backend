<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'users')]
    public function getUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getName(),
                'phone number' => $user->getPhone(),
                'roles' => $user->getRoles(),
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/api/create_user', name: 'create_user', methods: ['POST'])]
    public function createUser(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $phone = $data['phone_number'];
        $password = $data['password'];

        if (strlen($password) < 8) {
            return $this->json([
                'success' => false,
                'message' => 'Пароль должен содержать минимум 8 символов'
            ], Response::HTTP_BAD_REQUEST);
        }

        $existingUser  = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (!$existingUser) {
            $user = new User();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );

            $user->setPhone($phone);
            $user->setName($name);
            $user->setPassword($hashedPassword);
            $user->setRoles('ROLE_USER');

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->json([
                    'success' => true,
                    'message' => 'Пользователь создан',
                    'user' => [
                        'id' => $user->getId(),
                        'username' => $user->getName(),
                        'phone number' => $user->getPhone(),
                        'roles' => $user->getRoles(),
                    ]
                ], Response::HTTP_CREATED);
            } catch (Exception $e) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ошибка: ' . $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: пользователь с данным номером телефона уже существует'
            ], Response::HTTP_CONFLICT);
        }
    }
}
