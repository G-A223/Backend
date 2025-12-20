<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\House;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HouseController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function getHouses(EntityManagerInterface $entityManager): Response
    {
        $houses = $entityManager->getRepository(House::class)->findAll();
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('houses.html.twig', [
            'houses' => $houses,
            'reservations' => $reservations,
            'users' => $users,
        ]);
    }
}
