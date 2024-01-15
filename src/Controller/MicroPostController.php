<?php

namespace App\Controller;

use DateTime;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use Doctrine\ORM\EntityManager;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function add(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid())
        {
            $micropost = $form->getData();
            $micropost->setCreated(new DateTime());

            // Tell Doctrine to eventually save the post - no queries yet
            $entityManager->persist(($micropost));
            // Actually executes the queries (i.e the INSERT query)
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', 'Your micro post has been successfully added.');

            // Redirect
            return $this->redirectToRoute('app_micro_post');
        }
        
        // Otherwise render the form view
        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', 'Your micro post has been successfully updated.');
            
            return $this->redirectToRoute('app_micro_post'); // Redirect to posts index page
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    public function addComment(MicroPost $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', 'Your comment has been successfully updated.');
            
            return $this->redirectToRoute(
                'app_micro_post_show',
                ['post' => $post->getId()]
            ); // Redirect to posts index page
        }

        return $this->render('micro_post/comment.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}