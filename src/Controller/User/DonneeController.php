<?php

namespace App\Controller\User;

use App\Entity\Budget;
use App\Entity\RegularSpend;
use App\Entity\User;
use App\Service\CalendarService;
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
     * @Route("/{id}/ajouter", options={"expose"=true}, name="add")
     */
    public function add(Request $request,SerializeData $serializer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $name = $data->name->value;
        $price = $data->price->value;

        $budget = $em->getRepository(Budget::class)->find($id);

        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        $donnee = (new RegularSpend())
            ->setName($name)
            ->setPrice($price)
            ->setBudget($budget)
        ;

        $em->persist($donnee);
        $em->flush();

        return new JsonResponse(['code' => 1]);
    }
}
