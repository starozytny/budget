<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $month = $em->getRepository(Budget::class)->findOneBy(['year' => $today['year'], 'month' => $today['mon']]);


        return $this->render('root/user/index.html.twig');
    }
}
