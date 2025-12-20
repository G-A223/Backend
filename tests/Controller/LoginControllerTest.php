<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends WebTestCase
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
    }

    private function cleanDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM reservations');
        $connection->executeStatement('DELETE FROM houses');
        $connection->executeStatement('DELETE FROM users');
    }

    private function createTestUser(string $phone = '88005553535', string $password = 'testpassword123'): User
    {
        $user = new User();
        $user->setPhone($phone);
        $user->setName('Тестовый пользователь');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testSuccessfulLogin(): void
    {
        $user = $this->createTestUser('88005553535', 'testpassword123');

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'phone' => '88005553535',
                'password' => 'testpassword123'
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['success']);
        $this->assertEquals('Вы успешно вошли в свою учетную запись', $response['message']);
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals($user->getId(), $response['user']['id']);
        $this->assertEquals($user->getName(), $response['user']['username']);
        $this->assertEquals($user->getPhone(), $response['user']['phone']);
        $this->assertEquals($user->getRoles(), $response['user']['roles']);
    }
}
