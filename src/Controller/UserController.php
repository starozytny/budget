<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\RegularSpend;
use App\Service\CalendarService;
use App\Service\SerializeData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        
        $data = $this->getData($serializer, $budget);
        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);

        return $this->render('root/user/index.html.twig', [
            'budget' => $budget,
            'regularSpends' => $data['regularSpends']
        ]);
    }
    /**
     * @Route("/espace-utilisateur/change-month/{id}", options={"expose"=true}, name="user_dashboard_month")
     */
    public function month(SerializeData $serializer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $budget = $em->getRepository(Budget::class)->findOneBy(['id' => $id, 'user' => $user]);

        if(!$budget){
            return new JsonResponse(['code' => 0, 'message' => 'Budget inconnu.']);
        }
        
        $data = $this->getData($serializer, $budget);
        $budget = $serializer->getSerializeData($budget, self::ATTRIBUTES_BUDGET);

        return new JsonResponse(['code' => 1, 'budget' => $budget, 'regularSpends' => $data['regularSpends']]);
    }

    private function getData(SerializeData $serializer, $budget)
    {
        $em = $this->getDoctrine()->getManager();
        $regularSpends = $em->getRepository(RegularSpend::class)->findBy(['budget' => $budget], ['name' => 'ASC']);
        
        $regularSpends = $regularSpends ? $serializer->getSerializeData($regularSpends, self::ATTRIBUTES_DONNEES) : null;

        return [
            'regularSpends' => $regularSpends
        ];
    }
}
