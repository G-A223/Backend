<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\House;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReserveController extends AbstractController
{
    #[Route('/api/reservations', name: 'reservations')]
    public function getReservations(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'house id' => $reservation->getHouse()->getId(),
                'username' => $reservation->getUser()->getName(),
                'phone number' => $reservation->getUser()->getPhone(),
                'comment' => $reservation->getComment(),
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/api/reserve', name: 'reserve', methods: ['POST'])]
    public function reserve(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'success' => false,
                'message' => 'Некорректный JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        $phone = $data['user'];
        $id = $data['house'];
        $comment = $data['comment'];

        $house = $entityManager->getRepository(House::class)->find($id);

        if (!$house) {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: домика с таким id не существует'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($house->getAvailable() <= 0) {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: нет свободных домиков для бронирования'
            ], Response::HTTP_CONFLICT);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: такого пользователя не сущетсвует'
            ], Response::HTTP_NOT_FOUND);
        }

        $reservation = new Reservation();
        $reservation->setHouse($house);
        $reservation->setUser($user);
        $reservation->setComment($comment);

        $house->setAvailable($house->getAvailable() - 1);

        try {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Заявка успешно создана!',
                'reservation' => [
                    'id' => $reservation->getId(),
                    'house id' => $reservation->getHouse()->getId(),
                    'username' => $reservation->getUser()->getName(),
                    'phone number' => $reservation->getUser()->getPhone(),
                    'comment' => $reservation->getComment(),
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/reserve/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
        $data = json_decode($request->getContent(), true);
        $comment = $data['comment'];
        $reservation->setComment($comment);

        try {
            $entityManager->flush();
            return $this->json([
                'success' => true,
                'message' => 'Комментарий изменен',
                'reservation' => [
                    'id' => $reservation->getId(),
                    'comment' => $reservation->getComment(),
                ]
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
