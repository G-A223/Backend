<?php

namespace App\Tests\Controller;

use App\Entity\House;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HouseControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $this->cleanDatabase();
        $this->createTestUser();
        $this->loginTestUser();
    }

    private function cleanDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM reservations');
        $connection->executeStatement('DELETE FROM houses');
        $connection->executeStatement('DELETE FROM users');
    }

    private function createTestUser(): User
    {
        $user = new User();
        $user->setPhone('test_phone');
        $user->setName('Test User');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'testpassword');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function loginTestUser(): void
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => 'test_phone',
                'password' => 'testpassword'
            ])
        );
    }

     public function testGetHouses(): void
    {
        $house1 = new House();
        $house1->setName('Дом 1');
        $house1->setFacilities('WiFi, TV');
        $house1->setBeds(2);
        $house1->setBathrooms(1);
        $house1->setPrice(100.0);
        $house1->setAvailable(3);

        $house2 = new House();
        $house2->setName('Дом 2');
        $house2->setFacilities('Бассейн, Сауна');
        $house2->setBeds(4);
        $house2->setBathrooms(2);
        $house2->setPrice(200.0);
        $house2->setAvailable(2);

        $this->entityManager->persist($house1);
        $this->entityManager->persist($house2);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/houses');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertCount(2, $response['data']);
    }

    public function testCreateHouse(): void
    {
        $houseData = [
            'name' => 'Тестовый дом',
            'facilities' => 'WiFi, Кондиционер, TV',
            'beds' => 3,
            'bathrooms' => 2,
            'price' => 150.0,
            'available' => 2
        ];

        $this->client->request(
            'POST',
            '/api/house',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($houseData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['success']);
        $this->assertEquals('Домик успешно добавлен!', $response['message']);

        $house = $this->entityManager->getRepository(House::class)->findOneBy(['name' => 'Тестовый дом']);
        $this->assertNotNull($house);
        $this->assertEquals(3, $house->getBeds());
    }

    public function testGetReservations(): void
    {
        $house = new House();
        $house->setName('Тестовый дом');
        $house->setFacilities('WiFi');
        $house->setBeds(2);
        $house->setBathrooms(1);
        $house->setPrice(100.0);
        $house->setAvailable(2);

        $user = $this->createTestUser('88005553535');

        $reservation = new Reservation();
        $reservation->setHouse($house);
        $reservation->setUser($user);
        $reservation->setComment('Тестовый комментарий');

        $this->entityManager->persist($house);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/reservations');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertCount(1, $response['data']);
        $this->assertEquals('Тестовый комментарий', $response['data'][0]['comment']);
    }

    public function testCreateReservationSuccess(): void
    {
        $house = new House();
        $house->setName('Тестовый дом');
        $house->setFacilities('Удобства');
        $house->setBeds(2);
        $house->setBathrooms(1);
        $house->setPrice(100.0);
        $house->setAvailable(1);

        $user = $this->createTestUser('88005553535');

        $this->entityManager->persist($house);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/api/reserve',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone_number' => '88005553535',
                'id' => $house->getId(),
                'comment' => 'Тестовый комментарий'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['success']);
        $this->assertEquals('Заявка успешно создана!', $response['message']);

        $updatedHouse = $this->entityManager->getRepository(House::class)->find($house->getId());
        $this->assertEquals(0, $updatedHouse->getAvailable());

        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneBy([
            'house' => $house,
            'user' => $user
        ]);
        $this->assertNotNull($reservation);
        $this->assertEquals('Тестовый комментарий', $reservation->getComment());
    }
}
