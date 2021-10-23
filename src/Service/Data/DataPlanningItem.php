<?php


namespace App\Service\Data;


use App\Entity\Budget\BuOutcome;
use App\Entity\Budget\BuPlanning;
use Doctrine\ORM\EntityManagerInterface;

class DataPlanningItem
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function setData($obj, $data, $setNumGroup = false, BuPlanning $planning = null)
    {
        if(!$planning){
            $planning = $this->em->getRepository(BuPlanning::class)->find($data->planning);
            if(!$planning){
                return false;
            }
        }

        if($setNumGroup){
            $numGroup = (isset($data->numGroup) && $data->numGroup) ? $data->numGroup : uniqid();
            $obj->setNumGroup($numGroup);
        }

        return ($obj)
            ->setIcon($data->icon ?: null)
            ->setName(trim($data->name))
            ->setPrice($data->price)
            ->setPlanning($planning)
        ;
    }

    public function getNewObject($obj)
    {
        if($obj instanceof BuOutcome){
            return new BuOutcome();
        }

        return null;
    }

    public function isExisteObject($planning, $obj): bool
    {
        $find = false;
        if($obj instanceof BuOutcome){
            foreach($planning->getOutcomes() as $outcome){
                if($outcome->getNumGroup() == $obj->getNumGroup()){
                    $find = true;
                }
            }
        }

        return $find;
    }
}