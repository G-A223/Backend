final <?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/create_user', name: 'create_user', methods: ['POST', 'GET'])]
    public function createUser(EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $phone = $request->request->get('phone_number');

            $existingUser  = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

            if (!$existingUser) {
                $user = new User();
                $user->setPhone($phone);
                $user->setName($name);

                try {
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Пользователь создан');
                } catch (Exception $e) {
                    $this->addFlash('error', 'Ошибка: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'Пользователь с данным номером телефона уже существует');
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('create_user.html.twig');
    }
}
