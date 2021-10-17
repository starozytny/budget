<?php

namespace App\Controller;

use App\Entity\Budget\BuPlanning;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/espace-membre", options={"expose"=true}, name="user_homepage")
     */
    public function index(SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();
        $objs = $em->getRepository(BuPlanning::class)->findBy(['user' => $user->getId()]);

        $objs = $serializer->serialize($objs, 'json', ['groups' => User::USER_READ]);

        return $this->render('user/pages/index.html.twig', [
            'donnees' => $objs
        ]);
    }
}
