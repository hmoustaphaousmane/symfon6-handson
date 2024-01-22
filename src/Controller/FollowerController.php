<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FollowerController extends AbstractController
{
    #[Route('/followe/{id}', name: 'app_follow')]
    public function follow(
        User $userToFollow,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($userToFollow->getId() != $currentUser->getId())
        {
            $currentUser->follow($userToFollow);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollowe/{id}', name: 'app_unfollow')]
    public function unfollow(
        User $userToUnfollow,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($userToUnfollow->getId() != $currentUser->getId())
        {
            $currentUser->unfollow($userToUnfollow);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
