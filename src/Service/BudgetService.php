<?php


namespace App\Service;

use App\Entity\Economy;
use App\Entity\Income;
use App\Entity\Outgo;
use App\Entity\RegularSpend;
use Doctrine\ORM\EntityManagerInterface;

class BudgetService
{
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateOrGet($type, $whoReturn, $action, $budget, $donnee)
    {
        $em = $this->em;
        if($whoReturn == 'budget' && $action == "remove"){
            $toSpend = $budget->getToSpend();
            $price = $donnee->getPrice();
            if($type != 'income'){
                $budget->setToSpend($toSpend + $price);
            }else{
                $budget->setToSpend($toSpend - $price);
            }

            return $budget;
        }

        switch($type){
            case 'income':
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addIncome($donnee); }
                }else{
                    $donnee = ($action == "add") ? new Income() : $em->getRepository(Income::class)->find($donnee);
                }
                break;
            case 'outgo':
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addOutgo($donnee); }
                }else{
                    $donnee = ($action == "add") ? new Outgo() : $em->getRepository(Outgo::class)->find($donnee);
                }
                break;
            case 'economy':
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addEconomy($donnee); }
                }else{
                    $donnee = ($action == "add") ? new Economy() : $em->getRepository(Economy::class)->find($donnee);
                }
                break;
            default: //regularSpend
                if($whoReturn == 'budget'){
                    if($action == "add"){ $budget->addRegularSpend($donnee); }
                }else{
                    $donnee = ($action == "add") ? new RegularSpend() : $em->getRepository(RegularSpend::class)->find($donnee);
                }
                break;
        }

        if($whoReturn == 'budget'){
            return $budget;
        }else{
            return $donnee;
        }
        
    }

    public function updateNextBudget($budgets, $budget, $isAddition, $valueDonnee)
    {
        $em = $this->em;
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
