<?php

namespace App\Controller;

use App\Services\ServicesCSV;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReserveController extends AbstractController
{
    private ServicesCSV $servicesCsv;

    public function __construct(ServicesCSV $servicesCsv)
    {
        $this->servicesCsv = $servicesCsv;
    }

    #[Route('/reserve', name: "reserve", methods: ['POST'])]
    public function reserve(Request $request): Response
    {
        $message = '';
        $phone = $request->request->get('phone_number');
        $id = $request->request->get('id');
        $comment = $request->request->get('comment');

        $data = array(0, $id, $phone, $comment);

        $houses = $this->servicesCsv->readCSV('houses.csv');
        $is_available = true;
        foreach($houses as $house_param) {
            if ($house_param[0] == $id) {
                if ($house_param[6] == 0) {
                    $is_available = false;
                }
                break;
            }
        }

        if ($is_available) {
            try {
            $this->servicesCsv->makeReservation('reservations.csv', $data);
            $this->addFlash('Success', 'Заявка успешно создана!');
            }
            catch (\Exception $e) {
                $this->addFlash('error', 'Ошибка: ' . $e->getMessage());

            }
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/reserve/{id}/edit', name: "edit", methods: ['GET', 'POST', 'PUT'])]
    public function edit(Request $request, int $id): Response
    {
        $reservations = $this->servicesCsv->readCSV('reservations.csv');
        $reservation = null;

        foreach ($reservations as $reserv) {
            if ($reserv[0] == $id) {
                $reservation = $reserv;
                break;
            }
        }

        if ($request->isMethod('POST') && $request->request->get('_method') === 'PUT') {
            $comment = $request->request->get('comment');
            $reservation[3] = $comment;

            try {
                $this->servicesCsv->updateComment('reservations.csv', $comment, $id);
                $this->addFlash('Success', 'Комментарий изменен');

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
