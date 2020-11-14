<?php

namespace App\Controller\User;

use App\Entity\Budget;
use App\Entity\Goal;
use App\Service\BudgetService;
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
    /** 
     * @Route("/{type}/{id}/ajouter", options={"expose"=true}, name="add")
     */
    public function add(Request $request, SerializeData $serializer, CalendarService $calendarService, BudgetService $budgetService, $type, $id)
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

        $existe = $budgetService->getRegularOrIncome($type, $budget, $name, $price);
        if($existe){
            return new JsonResponse(['code' => 0, 'message' => 'Cette donnée existe déjà.']);
        }

        //budgets of others months of this year
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $budget->getYear(), 'user' => $user], ['month' => 'ASC']);

        //init data to create + to know if isAddition (+) or not (-)
        $isAddition = $type != 'income' ? false : true; // car on soustrait ce que l'on dépense
        $donnee = $budgetService->updateOrGet($type, "donnee", "add", null, null);

        //set new data
        $donnee->setName($name);
        $donnee->setPrice($price);

        if($type == "economy"){
            if($data->goal->value != ""){
                $goal = $em->getRepository(Goal::class)->find($data->goal->value);
                if($goal){
                    $donnee->setGoal($goal);
                    $goal->setFill($goal->getFill() + $price);
                }else{
                    return new JsonResponse(['code' => 0, 'message' => 'L\'objectif n\'existe pas. Veuillez contacter le support.']);
                }
            }
        }

        //add data to this budget
        $budget = $budgetService->updateOrGet($type, "budget", "add", $budget, $donnee);

        //new value of toSpend fo this budget
        $toSpend = $budget->getToSpend();
        $budget->setToSpend($isAddition ? ($toSpend + $price) : ($toSpend - $price));
        //update initMonth and toSpend of other months of this year
        $budgetService->updateNextBudget($budgets, $budget, $isAddition, $price);

        // ---- if month not passed = spread to others 
        if($type == "regularSpend" or $type == "income"){
            $today = $calendarService->getToday();
            if($budget->getYear() >= $today['year'] && $budget->getMonth() >= $today['mon']){
                $budgetService->addRegularDonneeToNextBudget($type, $budgets, $budget, $isAddition, $name, $price);
            }
        }
        
        $em->persist($budget); $em->persist($donnee); $em->flush();
        $budget = $serializer->getSerializeData($budget, Budget::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, Budget::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budgets' => $budgets, 'budget' => $budget]);
    }

    /**
     * @Route("/{type}/{id}/supprimer", options={"expose"=true}, name="delete")
     */
    public function delete(SerializeData $serializer, CalendarService $calendarService, BudgetService $budgetService, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $isAddition = $type != "income" ? true : false; //car on rajoute ce qu'on a dépensé
        $donnee = $budgetService->updateOrGet($type, 'donnee', "remove", null, $id); //get donnee to delete
        if(!$donnee){
            return new JsonResponse(['code' => 0, 'message' => 'Valeur inconnue.']);
        }

        $user = $this->getUser();
        $budget = $donnee->getBudget();
        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }

        $budget = $budgetService->updateOrGet($type, "budget", "remove", $budget, $donnee);
        //budgets of others months of this year to update  initMonth and toSpend of other months
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $budget->getYear(), 'user' => $user], ['month' => 'ASC']);
        $budgetService->updateNextBudget($budgets, $budget, $isAddition, $donnee->getPrice());

        // ---- if month not passed = spread to others 
        if($type == "regularSpend" or $type == "income"){
            $today = $calendarService->getToday();
            if($budget->getYear() >= $today['year'] && $budget->getMonth() >= $today['mon']){
                $budgetService->removeRegulatDonneeToNextBudget($type, $budgets, $budget, $isAddition, $donnee->getName(), $donnee->getPrice());
            }
        }

        if($type == "economy"){
            $goal = $donnee->getGoal();
            if($goal){
                $goal->setFill($goal->getFill() - $donnee->getPrice());
            }
        }

        $em->persist($budget); $em->remove($donnee); $em->flush();

        $budget = $serializer->getSerializeData($donnee->getBudget(), Budget::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, Budget::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budgets' => $budgets, 'budget' => $budget]);
    }
}
