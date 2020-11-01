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
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'monthString', 'spend'];
    const ATTRIBUTES_DONNEES = ['id', 'name', 'price'];

    /**
     * @Route("/espace-utilisateur", name="user_dashboard")
     */
    public function index(SerializeData $serializer, CalendarService $calendarService)
    {
        $em = $this->getDoctrine()->getManager();
        $today = $calendarService->getToday();
        $user = $this->getUser();
        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $today['year'], 'month' => $today['mon'], 'user' => $user]);
        $regularSpends = $em->getRepository(RegularSpend::class)->findBy(['budget' => $budget], ['name' => 'ASC']);

        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);
        $regularSpends = $serializer->getSerializeData($regularSpends, self::ATTRIBUTES_DONNEES);

        return $this->render('root/user/index.html.twig', [
            'budget' => $budget,
            'regularSpends' => $regularSpends
        ]);
    }
}
