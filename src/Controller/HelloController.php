<?php

namespace  App\Controller; // Every controller should have a name space that
// starts with `App\`

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// The class name and the file name should be the same and there should be no
// more than one class per file
class HelloController
{
    private array $messages = [
        'Hello', 'Hi', 'Bye!'
    ];

    #[Route('/{limit<\d+>?3}', name: 'app_index')] // Always give a `name` to the routes
    public function index(int $limit): Response
    {
        return new Response(
            implode(',', array_slice($this->messages, 0, $limit))
        );
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')] // A route with parameter
    public function showOne(int $id): Response
    {
        return new Response($this->messages[$id]);
    }
}