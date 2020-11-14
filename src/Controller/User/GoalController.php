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

    const ATTRIBUTES_GOAL = ['id', 'name', 'total', 'fill'];

    /**
     * @Route("/", options={"expose"=true}, name="index")
     */
    public function index(SerializeData $serializer)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $goals = $em->getRepository(Goal::class)->findBy(['user' => $user], ['name' => 'ASC']);

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
            ->setUser($user)
            ->setName($name)
            ->setTotal($total)
        ;

        $em->persist($newGoal); $em->flush();
        $newGoal = $serializer->getSerializeData($newGoal, self::ATTRIBUTES_GOAL);

        return new JsonResponse(['code' => 1, 'goal' => $newGoal]);
    }

    /**
     * @Route("/modifier/{id}", options={"expose"=true}, name="edit")
     */
    public function edit(Request $request, SerializeData $serializer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $name = $data->name->value;
        $total = $data->total->value;

        $goal = $em->getRepository(Goal::class)->find($id);
        if(!$goal){
            return new JsonResponse(['code' => 0, 'message' => 'Cet objectif n\'existe pas.']);
        }

        $user = $this->getUser();
        $existes = $em->getRepository(Goal::class)->findBy(['name' => $name, 'user' => $user]);
        if($existes){
            foreach($existes as $existe){
                if($existe->getId() != $goal->getId()){
                    return new JsonResponse(['code' => 0, 'message' => 'Cet objectif existe déjà.']);
                }
            }
        }

        $goal->setName($name);
        $goal->setTotal($total);

        $em->persist($goal); $em->flush();
        $goal = $serializer->getSerializeData($goal, Goal::ATTRIBUTES_GOAL);
        return new JsonResponse(['code' => 1, 'goal' => $goal]);
    }

    /**
     * @Route("/supprimer/{id}", options={"expose"=true}, name="delete")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();

        $goal = $em->getRepository(Goal::class)->find($id);
        if(!$goal){
            return new JsonResponse(['code' => 0, 'message' => 'Cet objectif n\'existe pas.']);
        }
        
        if($goal->getEconomies()){
            foreach($goal->getEconomies() as $eco){
                $eco->setGoal(null);
            }
        }

        $em->remove($goal); $em->flush();
        return new JsonResponse(['code' => 1]);
    }

}
