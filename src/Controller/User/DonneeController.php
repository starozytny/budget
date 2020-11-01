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
    const ATTRIBUTES_DONNEES = ['id', 'name', 'price'];

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

        $donnee->setName($name);
        $donnee->setPrice($price);
        $donnee->setBudget($budget);

        $em->persist($donnee); $em->flush();

        $donnee = $serializer->getSerializeData($donnee, self::ATTRIBUTES_DONNEES);
        return new JsonResponse(['code' => 1, 'donnee' => $donnee, 'type' => $type]);
    }
}
