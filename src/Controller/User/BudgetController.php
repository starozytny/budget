<?php

namespace App\Controller\User;

use App\Entity\Budget;
use App\Entity\Goal;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/espace-utilisateur/budgets", name="user_budgets_")
 */
class BudgetController extends AbstractController
{
    /**
     * @Route("/{id}/ajouter-comment", options={"expose"=true}, name="add_comment")
     */
    public function addComment(Request $request, SerializeData $serializer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $comment = $data->comment->html;

        $budget = $em->getRepository(Budget::class)->find($id);
        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Erreur, budget introuvable.']);
        }

        $budget->setComment($comment);
        $em->persist($budget); $em->flush();

        $user = $this->getUser();
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $budget->getYear(), 'user' => $user], ['month' => 'ASC']);

        $budget = $serializer->getSerializeData($budget, Budget::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, Budget::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budget' => $budget, 'budgets' => $budgets]);
    }
}
