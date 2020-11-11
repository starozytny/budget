<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Service\BudgetService;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'monthString', 'initMonth', 'toSpend', 
                               'regularSpends' => ['id', 'name', 'price'],
                               'economies' => ['id', 'name', 'price'],
                               'outgos' => ['id', 'name', 'price'],
                               'incomes' => ['id', 'name', 'price'],
                            ];

    /**
     * @Route("/espace-utilisateur", name="user_dashboard")
     */
    public function index(SerializeData $serializer, CalendarService $calendarService)
    {
        $em = $this->getDoctrine()->getManager();
        $today = $calendarService->getToday();
        $user = $this->getUser();
        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $today['year'], 'month' => $today['mon'], 'user' => $user]);
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $today['year'], 'user' => $user], ['month' => 'ASC']);

        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, self::ATTRIBUTES_BUDGET);

        return $this->render('root/user/index.html.twig', [
            'budgets' => $budgets,
            'budget' => $budget
        ]);
    }

    /**
     * @Route("/espace-utilisateur/annee/{direction}/{year}", options={"expose"=true}, name="user_dashboard_year")
     */
    public function changeYear(SerializeData $serializer, BudgetService $budgetService, $direction, $year)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $previousBudget = $em->getRepository(Budget::class)->findOneBy(['year' => $year-1, 'month' => 12, 'user' => $user]);
        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $year, 'month' => 1, 'user' => $user]);
        $budgets = $em->getRepository(Budget::class)->findBy(['year' => $year, 'user' => $user], ['month' => 'ASC']);

        if(!$budget){
            if($direction == 'previous'){
                return new JsonResponse(['code' => 0, 'message' => 'Aucune donnée antérieur.']);
            }else{
                // create years months 
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
        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);
        $budgets = $serializer->getSerializeData($budgets, self::ATTRIBUTES_BUDGET);
        return new JsonResponse(['code' => 1, 'budgets' => $budgets, 'budget' => $budget]);
    }
}
