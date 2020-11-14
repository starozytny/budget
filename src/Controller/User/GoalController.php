<?php

namespace App\Controller\User;

use App\Entity\Goal;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/espace-utilisateur/objectifs", name="user_goals_")
 */
class GoalController extends AbstractController
{    
    /**
     * @Route("/", options={"expose"=true}, name="index")
     */
    public function index(SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $goals = $em->getRepository(Goal::class)->findBy(['user' => $user]);

        $goals = $serializer->getSerializeData($goals, Goal::ATTRIBUTES_GOAL);     
        return $this->render('root/user/pages/goals/index.html.twig', [
            'goals' => $goals,
        ]);
    }

    /**
     * @Route("/ajouter", options={"expose"=true}, name="add")
     */
    public function add(Request $request, SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $name = $data->name->value;
        $total = $data->total->value;

        $user = $this->getUser();
        $goal = $em->getRepository(Goal::class)->findBy(['name' => $name, 'user' => $user]);
        if($goal){
            return new JsonResponse(['code' => 0, 'message' => 'Cet objectif existe déjà.']);
        }

        $newGoal = (new Goal())
            ->setUser($this->getUser())
            ->setName($name)
            ->setTotal($total)
        ;

        $em->persist($newGoal); $em->flush();
        $newGoal = $serializer->getSerializeData($newGoal, Goal::ATTRIBUTES_GOAL);
        return new JsonResponse(['code' => 1, 'goal' => $newGoal]);
    }
}
