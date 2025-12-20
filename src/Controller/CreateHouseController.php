<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\House;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateHouseController extends AbstractController
{
    #[Route('/house', name: 'create_house', methods: ['POST', 'GET'])]
    public function createHouse(EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $house = new House();
            $house->setName($request->request->get('name'));
            $house->setBeds((int) $request->request->get('beds'));
            $house->setBathrooms((int) $request->request->get('bathrooms'));
            $house->setPrice((float) $request->request->get('price'));
            $house->setAvailable((int) $request->request->get('available'));
            $house->setFacilities($request->request->get('facilities'));

            try {
                $entityManager->persist($house);
                $entityManager->flush();

                $this->addFlash('Success', 'Домик успешно добавлен!');
            } catch (Exception $e) {
                $this->addFlash('error', 'Ошибка: ' . $e->getMessage());
            }


            return $this->redirectToRoute('home');
        }


        return $this->render('create_house.html.twig');
    }
}
