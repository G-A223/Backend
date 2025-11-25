<?php

namespace App\Tests\Controller;

use App\Entity\House;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HouseControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM reservations');
        $connection->executeStatement('DELETE FROM houses');
        $connection->executeStatement('DELETE FROM users');
    }

    public function testGetHouses(): void
    {
        $house = new House();
        $house->setName('Тестовый дом');
        $house->setFacilities('Удобства');
        $house->setBeds(2);
        $house->setBathrooms(1);
        $house->setPrice(100.0);
        $house->setAvailable(1);

        $this->entityManager->persist($house);
        $this->entityManager->flush();

        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Все доступные домики:');
        $this->assertPageTextContains('Тестовый дом');
    }

    public function testCreateHouse(): void
    {
        $this->client->request('POST', '/house', [
            'name' => 'Тестовый дом',
            'facilities' => 'Удобства',
            'beds' => 3,
            'bathrooms' => 2,
            'price' => 150.0,
            'available' => 2,
        ]);

        $this->assertResponseRedirects('/');

        $house = $this->entityManager->getRepository(House::class)->findOneBy(['name' => 'Тестовый дом']);
        $this->assertNotNull($house);
        $this->assertEquals(3, $house->getBeds());
    }

    public function testCreateReservation(): void
    {
        $house = new House();
        $house->setName('Тестовый дом');
        $house->setFacilities('Удобства');
        $house->setBeds(2);
        $house->setBathrooms(1);
        $house->setPrice(100.0);
        $house->setAvailable(1);

        $user = new User();
        $user->setName('Тестовый пользователь');
        $user->setPhone('88005553535');

        $this->entityManager->persist($house);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request('POST', '/reserve', [
            'phone_number' => '88005553535',
            'id' => $house->getId(),
            'comment' => 'Тестовый комментарий',
        ]);

        $this->assertResponseRedirects('/');

        $updatedHouse = $this->entityManager->getRepository(House::class)->find($house->getId());
        $this->assertEquals(0, $updatedHouse->getAvailable());
    }

    public function testCreateUser(): void
    {
        $this->client->request('POST', '/create_user', [
            'name' => 'Тестовый пользователь',
            'phone_number' => '88005553535',
        ]);

        $this->assertResponseRedirects('/');

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => '88005553535']);
        $this->assertNotNull($user);
        $this->assertEquals('Тестовый пользователь', $user->getName());
    }
}
