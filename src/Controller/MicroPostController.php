<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments()
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts->findAllWithMinLikes(2)
        ]);
    }

    #[Route('/micro-post/follws', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        return $this->render(
            'micro_post/follows.html.twig',
            [
                'posts' => $posts->findAllByAuthors(
                    $currentUser->getFollows()
                ),
            ]
        );
    }
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post) : Response
    {
        return $this->render('micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('ROLE_WRITER')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager
    ) : Response
    {
        // `denyAccessUnlessGranted()` method protect controller action from
        // unauthorised use. This does the samething as the pHp8 attirbute
        // `#[IsGranted('IS_AUTHENTICATED_FULLY')]`
        // $this->denyAccessUnlessGranted(
        //     'IS_AUTHENTICATED_FULLY'
        // );
        // dd($this->getUser());
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid())
        {
            $micropost = $form->getData();
            $micropost->setAuthor($this->getUser());

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
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(
        MicroPost $post,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(MicroPostType::class, $post);
        $form->handleRequest($request);

        // $this->denyAccessUnlessGranted(MicroPost::EDIT, 'post');

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $entityManager->flush();

            // Add success flash message
            $this->addFlash('success', 'Your micro post has been successfully updated.');
            
            return $this->redirectToRoute('app_micro_post'); // Redirect to posts index page
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(
        MicroPost $post,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());
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