<?php


namespace App\Service\Data;


use App\Entity\Budget\BuExpense;
use App\Entity\Budget\BuPlanning;
use Doctrine\ORM\EntityManagerInterface;

class DataExpense
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function setData(BuExpense $obj, $data)
    {
        $planning = $this->em->getRepository(BuPlanning::class)->find($data->planning);
        if(!$planning){
            return false;
        }

        return ($obj)
            ->setIcon($data->icon ?: null)
            ->setName(trim($data->name))
            ->setPrice($data->price)
            ->setPlanning($planning)
        ;
    }
}