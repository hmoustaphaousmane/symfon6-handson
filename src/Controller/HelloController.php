<?php

namespace  App\Controller; // Every controller should have a name space that
// starts with `App\`

use Symfony\Component\HttpFoundation\Response;

// The class name and the file name should be the same and there should be no
// more than one class per file
class HelloController
{
    public function index(): Response
    {
        return new Response("HI!");
    }
}