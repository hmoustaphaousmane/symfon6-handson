<?php

namespace App\Controller;

use DateTime;
use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAll()
        ]);
    }

    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post) : Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('micro-post/add', name: 'app_micro_post_add', priority: 2)]
    public function add() : Response {
        $micropost = new MicroPost();
        // Create a form using form builder and configure it to handle the properties of the MicroPost entity (`title` and `text`)
        $form = $this->createFormBuilder($micropost)
            ->add('title')
            ->add('text')
            ->add('submit', SubmitType::class, ['label' => 'Save'])
            ->getForm(); // Retrieve the form

        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }
}
