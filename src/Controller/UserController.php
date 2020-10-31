<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const ATTRIBUTES_BUDGET = ['id', 'year', 'month', 'spend'];

    /**
     * @Route("/espace-utilisateur", name="user_dashboard")
     */
    public function index(SerializeData $serializer, CalendarService $calendarService)
    {
        $em = $this->getDoctrine()->getManager();
        $today = $calendarService->getToday();
        $budget = $em->getRepository(Budget::class)->findOneBy(['year' => $today['year'], 'month' => $today['mon']]);

        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);

        return $this->render('root/user/index.html.twig', [
            'budget' => $budget
        ]);
    }
}
