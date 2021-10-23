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

    public function setData($obj, $data, $setNumGroup = false, BuPlanning $planning = null, $isDepense = true)
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

        $price = $data->price;


        $end = $planning->getEnd();
        $newEnd = $isDepense ? $end - $price : $end + $price;

        $planning->setEnd($newEnd);

        $this->updateNextMonths($isDepense, $newEnd, $planning->getNext());

        return ($obj)
            ->setIcon($data->icon ?: null)
            ->setName(trim($data->name))
            ->setPrice($price)
            ->setPlanning($planning)
        ;
    }

    public function updateNextMonths($isDepense, $newEnd, BuPlanning $nextPlanning)
    {
        $nextEnd =  $nextPlanning->getEnd();
        $nextStart = $nextPlanning->getStart();

        $nextPlanning->setStart($newEnd);

        $newNextEnd = $isDepense ? $nextEnd - ($nextStart - $newEnd) : $nextEnd + ($nextStart - $newEnd);

        $nextPlanning->setEnd($newNextEnd);

        if($nextPlanning->getNext()){
            $this->updateNextMonths($isDepense, $newNextEnd, $nextPlanning->getNext());
        }
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