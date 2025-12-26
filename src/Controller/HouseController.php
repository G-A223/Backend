<?php

namespace App\Controller;

use App\Services\ServicesCSV;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HouseController extends AbstractController
{
    private ServicesCSV $servicesCsv;

    public function __construct(ServicesCSV $servicesCsv)
    {
        $this->servicesCsv = $servicesCsv;
    }

    #[Route('/', name: 'home')]
    public function getHouses(): Response
    {
        $houses = $this->servicesCsv->readCSV('houses.csv');
        $reservations = $this->servicesCsv->readCSV('reservations.csv');

        return $this->render('houses.html.twig', [
            'houses' => $houses,
            'reservations' => $reservations,
        ]);
    }
}
