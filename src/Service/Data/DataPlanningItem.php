<?php


namespace App\Service\Data;


use App\Entity\Budget\BuPlanning;
use Doctrine\ORM\EntityManagerInterface;

class DataPlanningItem
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function setData($obj, $data, $setNumGroup = false)
    {
        $planning = $this->em->getRepository(BuPlanning::class)->find($data->planning);
        if(!$planning){
            return false;
        }

        if($setNumGroup){
            if(!$obj->getNumGroup()){
                $numGroup = uniqid();

                $obj->setNumGroup($numGroup);
            }
        }

        return ($obj)
            ->setIcon($data->icon ?: null)
            ->setName(trim($data->name))
            ->setPrice($data->price)
            ->setPlanning($planning)
        ;
    }
}