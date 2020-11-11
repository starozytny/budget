<?php

namespace App\Controller\User;

use App\Entity\Budget;
use App\Entity\RegularSpend;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/espace-utilisateur/donnees", name="user_donnees_")
 */
class DonneeController extends AbstractController
{
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'monthString', 'initAccount', 'toSpend', 
                               'regularSpends' => ['id', 'name', 'price'] ];

    /**
     * @Route("/{type}/{id}/ajouter", options={"expose"=true}, name="add")
     */
    public function add(Request $request, SerializeData $serializer, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $name = $data->name->value;
        $price = $data->price->value;

        $budget = $em->getRepository(Budget::class)->find($id);

        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        switch($type){
            default:
                $donnee = new RegularSpend();
                break;
        }

        $toSpend = $budget->getToSpend();

        $donnee->setName($name);
        $donnee->setPrice($price);

        switch($type){
            default:
                $budget->addRegularSpend($donnee);
                $budget->setToSpend($toSpend - $price);
                break;
        }

        $em->persist($budget); $em->persist($donnee); $em->flush();

        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budget' => $budget, 'type' => $type]);
    }

    /**
     * @Route("/{type}/{id}/supprimer", options={"expose"=true}, name="delete")
     */
    public function delete(SerializeData $serializer, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch($type){
            default:
                $donnee = $em->getRepository(RegularSpend::class)->find($id);
                break;
        }

        if(!$donnee){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        $budget = $donnee->getBudget();
        $toSpend = $budget->getToSpend();

        switch($type){
            default:
                $budget->setToSpend($toSpend + $donnee->getPrice());
                break;
        }

        $em->persist($budget); 
        $em->remove($donnee); 
        $em->flush();

        $budget = $serializer->getSerializeData($donnee->getBudget(), self::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budget' => $budget, 'type' => $type]);
    }
}
