<?php

namespace  App\Controller; // Every controller should have a name space that
// starts with `App\`

use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// The class name and the file name should be the same and there should be no
// more than one class per file
class HelloController extends AbstractController // The AbstractController helps to generate based on a Twig template this why we do the extend
{
    private array $messages = [
        ['message' => 'Hello', 'created' => '2024/01/12'],
        ['message' => 'Hi', 'created' => '2023/11/12'],
        ['message' => 'Bye!', 'created' => '2022/12/20']
    ];

    #[Route('/', name: 'app_index')] // Always give a `name` to the routes
    public function index(Request $request, EntityManagerInterface $entitymanger): Response
    {
        // $user = new User();
        // $user->setEmail('hello@mail.com');
        // $user->setPassword('qwert123');

        // $profile = new UserProfile();
        // $profile->setUser($user);
        // $entitymanger->persist($profile);
        // $entitymanger->flush();

        return $this->render(
            'hello/index.html.twig',
            [
                'messages' => $this->messages,
                'limit' =>  3
            ]
        );
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')] // A route with parameter
    public function showOne(int $id): Response
    {
        return $this->render(
            'hello/show_one.html.twig',
            [
                'message' => $this->messages[$id]
            ]
        );
    }
}