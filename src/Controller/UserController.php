<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\Goal;
use App\Entity\Settings;
use App\Service\BudgetService;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/espace-utilisateur", name="user_dashboard")
     */
    public function index(SerializeData $serializer, CalendarService $calendarService)
    {
        $em = $this->getDoctrine()->getManager();
        $today = $calendarService->getToday();
        $user = $this->getUser();

        $year = $today['year'];
        $month = $today['mon'];

        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $year, 'month' => $month, 'user' => $user]);
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $year, 'user' => $user], ['month' => 'ASC']);

        if($month == 1){
            $previousBudget = $em->getRepository(Budget::class)->findOneBy(['year' => $year-1, 'month' => 12, 'user' => $user]);
            if(!$previousBudget){
                $previousBudget = $budget;
            }
        }else{
            $previousBudget = $em->getRepository(Budget::class)->findOneBy(['year' => $year, 'month' => $month-1, 'user' => $user]);
        }

        $goals = $em->getRepository(Goal::class)->findBy(['user' => $user]);

        $budget = $serializer->getSerializeData($budget, Budget::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, Budget::ATTRIBUTES_BUDGET);
        $previousBudget = $serializer->getSerializeData($previousBudget, Budget::ATTRIBUTES_BUDGET);
        $goals = $serializer->getSerializeData($goals, Goal::ATTRIBUTES_GOAL);

        return $this->render('root/user/index.html.twig', [
            'budgets' => $budgets,
            'budget' => $budget,
            'previousBudget' => $previousBudget,
            'goals' => $goals,
        ]);
    }

    /**
     * @Route("/espace-utilisateur/annee/{direction}/{year}", options={"expose"=true}, name="user_dashboard_year")
     */
    public function changeYear(SerializeData $serializer, BudgetService $budgetService, $direction, $year)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $settings = $em->getRepository(Settings::class)->findAll();
        $previousBudget = $em->getRepository(Budget::class)->findOneBy(['year' => $year-1, 'month' => 12, 'user' => $user]);
        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $year, 'month' => 1, 'user' => $user]);
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $year, 'user' => $user], ['month' => 'ASC']);

        if(!$budget){
            if($direction == 'previous'){
                return new JsonResponse(['code' => 0, 'message' => 'Aucune donnée antérieur.']);
            }else{
                if($year > $settings[0]->getMaxYear()){
                    return new JsonResponse(['code' => 0, 'message' => 'Impossible d\'accéder à cette année pour l\'instant.']);
                }
                $budgets = [];
                for($m=1 ; $m<=12 ; $m++){                    
                    $createBudget = (new Budget())
                        ->setYear($year)
                        ->setMonth($m)
                        ->setToSpend($previousBudget->getToSpend())
                        ->setInitMonth($previousBudget->getInitMonth())
                        ->setUser($user)
                    ;
                    
                    if($m == 1){
                        $budget = $createBudget;
                    }

                    //add regularspend and income manually each year                    

                    $em->persist($createBudget);
                    array_push($budgets, $createBudget);
                }
            }
        }

        if($direction == 'next'){
            if($budget->getInitMonth() > $previousBudget->getToSpend()){
                $isAddition = false;
                $difference = $budget->getInitMonth() - $previousBudget->getToSpend();
                $budget->setToSpend($budget->getToSpend() - $difference);
            }else{
                $isAddition = true;
                $difference = $previousBudget->getToSpend() - $budget->getInitMonth();
                $budget->setToSpend($budget->getToSpend() + $difference);
            }
            $budget->setInitMonth($previousBudget->getToSpend());
            $budgetService->updateNextBudget($budgets, $budget, $isAddition, $difference);
        }

        $em->persist($budget); $em->flush();
        $budget = $serializer->getSerializeData($budget, Budget::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, Budget::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budgets' => $budgets, 'budget' => $budget]);
    }
}
