<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\RegularSpend;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'monthString', 'startSpend', 
                               'regularSpends' => ['id', 'name', 'price'] ];

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
}
