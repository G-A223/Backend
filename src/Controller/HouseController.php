<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\House;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HouseController extends AbstractController
{
    #[Route('/api/houses', name: 'houses')]
    public function getHouses(EntityManagerInterface $entityManager): Response
    {
        $houses = $entityManager->getRepository(House::class)->findAll();

        $data = [];
        foreach ($houses as $house) {
            $data[] = [
                'id' => $house->getId(),
                'name' => $house->getName(),
                'beds' => $house->getBeds(),
                'bathrooms' => $house->getBathrooms(),
                'price' => $house->getPrice(),
                'available' => $house->getAvailable(),
                'facilities' => $house->getFacilities(),
            ];
        }

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    #[Route('/api/house', name: 'create_house', methods: ['POST'])]
    public function createHouse(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json([
                'success' => false,
                'message' => 'Íåêîððåêòíûé JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        $house = new House();
        $house->setName($data['name']);
        $house->setBeds($data['beds']);
        $house->setBathrooms((int) $data['bathrooms']);
        $house->setPrice((float) $data['price']);
        $house->setAvailable((int) $data['available']);
        $house->setFacilities($data['facilities']);

        try {
            $entityManager->persist($house);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Äîìèê óñïåøíî äîáàâëåí!',
                'house' => [
                    'id' => $house->getId(),
                    'name' => $house->getName(),
                    'beds' => $house->getBeds(),
                    'bathrooms' => $house->getBathrooms(),
                    'price' => $house->getPrice(),
                    'available' => $house->getAvailable(),
                    'facilities' => $house->getFacilities(),
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Îøèáêà: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
