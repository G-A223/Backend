<?php

namespace App\Controller;

use App\Entity\House;
use App\Entity\User;
use App\Entity\Reservation;
use App\Services\ServicesCSV;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class ReserveController extends AbstractController
{
    #[Route('/reserve', name: "reserve", methods: ['POST'])]
    public function reserve(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = '';
        $phone = $request->request->get('phone_number');
        $id = $request->request->get('id');
        $comment = $request->request->get('comment');

        $house = $entityManager->getRepository(House::class)->find($id);

        if (!$house) {
            $this->addFlash('error', 'Домик не найден');
            return $this->redirectToRoute('home');
        }

        if ($house->getAvailable() <= 0) {
            $this->addFlash('error', 'Нет свободных домиков для бронирования');
            return $this->redirectToRoute('home');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (!$user) {
            $user = new User();
            $user->setPhone($phone);
            $user->setName('Пользователь');

            $entityManager->persist($user);
        }

        $reservation = new Reservation();
        $reservation->setHouse($house);
        $reservation->setUser($user);
        $reservation->setComment($comment);

        $house->setAvailable($house->getAvailable() - 1);

        try {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Заявка успешно создана!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ошибка: ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/reserve/{id}/edit', name: "edit", methods: ['GET', 'POST', 'PUT'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);

        if ($request->isMethod('POST') && $request->request->get('_method') === 'PUT') {
            $comment = $request->request->get('comment');
            $reservation->setComment($comment);

            try {
                $entityManager->flush();
                $this->addFlash('success', 'Комментарий изменен');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Ошибка: ' . $e->getMessage());

            }

            return $this->redirectToRoute('home');
        }

        return $this->render('edit.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}
