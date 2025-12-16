<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'success' => false,
                'message' => 'Некорректный JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Введены неверные данные'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'success' => true,
            'message' => 'Вы успешно вошли в свою учетную запись',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getName(),
                'phone' => $user->getPhone(),
                'roles' => $user->getRoles()
            ],
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): Response
    {
        return $this->json([
            'success' => true,
            'message' => 'Вы вышли из своей учетной записи'
        ]);
    }
}
