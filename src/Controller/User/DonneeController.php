<?php

namespace App\Controller\User;

use App\Entity\Budget;
use App\Entity\Economy;
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
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'monthString', 'initMonth', 'toSpend', 
                               'regularSpends' => ['id', 'name', 'price'],
                               'economies' => ['id', 'name', 'price'],
                               'outgos' => ['id', 'name', 'price'],
                               'incomes' => ['id', 'name', 'price'],
                            ];

    /**
     * @Route("/{type}/{id}/ajouter", options={"expose"=true}, name="add")
     */
    public function add(Request $request, SerializeData $serializer, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $name = $data->name->value;
        $price = $data->price->value;

        $user = $this->getUser();
        $budget = $em->getRepository(Budget::class)->find($id);

        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        //budgets of others months of this year
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $budget->getYear(), 'user' => $user], ['month' => 'ASC']);

        //init data to create + to know if isAddition (+) or not (-)
        $isAddition = $type != 'income' ? false : true; // car on soustrait ce que l'on dépense
        $donnee = $this->updateOrGet($em, $type, "donnee", "add", null, null);

        //set new data
        $donnee->setName($name);
        $donnee->setPrice($price);

        //add data to this budget
        $budget = $this->updateOrGet($em, $type, "budget", "add", $budget, $donnee);

        //new value of toSpend fo this budget
        $toSpend = $budget->getToSpend();
        $budget->setToSpend($isAddition ? ($toSpend + $price) : ($toSpend - $price));

        //update initMonth and toSpend of other months of this year
        $this->updateNextBudget($em, $budgets, $budget, $isAddition, $price);
        
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

        $isAddition = $type != "income" ? true : false; //car on rajoute ce qu'on a dépensé
        $donnee = $this->updateOrGet($em, $type, 'donnee', "remove", null, $id);

        if(!$donnee){
            return new JsonResponse(['code' => 0, 'message' => 'Valeur inconnue.']);
        }
        $user = $this->getUser();
        $budget = $donnee->getBudget();

        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        $budget = $this->updateOrGet($em, $type, "budget", "remove", $budget, $donnee);

        //budgets of others months of this year
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $budget->getYear(), 'user' => $user], ['month' => 'ASC']);
        //update initMonth and toSpend of other months of this year
        $this->updateNextBudget($em, $budgets, $budget, $isAddition, $donnee->getPrice());

        $em->persist($budget); 
        $em->remove($donnee); 
        $em->flush();

        $budget = $serializer->getSerializeData($donnee->getBudget(), self::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budget' => $budget, 'type' => $type]);
    }

    private function updateOrGet($em, $type, $whoReturn, $action, $budget, $donnee)
    {
        if($whoReturn == 'budget' && $action == "remove"){
            $toSpend = $budget->getToSpend();
            $price = $donnee->getPrice();
            $budget->setToSpend($toSpend + $price);

            return $budget;
        }

        switch($type){
            case 'economy':
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addEconomy($donnee); }
                }else{
                    if($action == "add"){ 
                        $donnee = new Economy(); 
                    }else{ 
                        $donnee = $em->getRepository(Economy::class)->find($donnee); 
                    }
                }
                break;
            default: //regularSpend
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addRegularSpend($donnee); }
                }else{
                    if($action == "add"){ 
                        $donnee = new RegularSpend(); 
                    }else{ 
                        $donnee = $em->getRepository(RegularSpend::class)->find($donnee); 
                    }
                }
                break;
        }

        if($whoReturn == 'budget'){
            return $budget;
        }else{
            return $donnee;
        }
        
    }

    private function updateNextBudget($em, $budgets, $budget, $isAddition, $valueDonnee)
    {
        foreach($budgets as $next){
            if($next->getMonth() > $budget->getMonth()){
            
                if(!$isAddition){
                    $nextInitMonth = $next->getInitMonth() - $valueDonnee;
                    $nextToSpend = $next->getToSpend() - $valueDonnee;
                }else{
                    $nextInitMonth = $next->getInitMonth() + $valueDonnee;
                    $nextToSpend = $next->getToSpend() + $valueDonnee;
                }

                $next->setInitMonth($nextInitMonth);
                $next->setToSpend($nextToSpend);

                $em->persist($next);
            }
        }
    }
}
