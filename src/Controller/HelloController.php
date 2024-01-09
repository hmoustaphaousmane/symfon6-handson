<?php

namespace  App\Controller; // Every controller should have a name space that
// starts with `App\`

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// The class name and the file name should be the same and there should be no
// more than one class per file
class HelloController extends AbstractController // The AbstractController helps to generate based on a Twig template this why we do the extend
{
    private array $messages = [
        'Hello', 'Hi', 'Bye!'
    ];

    #[Route('/{limit<\d+>?3}', name: 'app_index')] // Always give a `name` to the routes
    public function index(int $limit): Response
    {
        return $this->render(
            'hello/index.html.twig',
            [
                'messages' => $this->messages,
                'limit' =>  $limit
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