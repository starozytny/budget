<?php


namespace App\Service\Data;


use App\Entity\User;
use App\Service\ApiResponse;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataService
{
    private $em;
    private $apiResponse;

    public function __construct(EntityManagerInterface $em, ApiResponse $apiResponse)
    {
        $this->em = $em;
        $this->apiResponse = $apiResponse;
    }

    public function isSeenToTrue($obj, $groups = User::ADMIN_READ): JsonResponse
    {
        $obj->setIsSeen(true);

        $this->em->flush();
        return $this->apiResponse->apiJsonResponse($obj, $groups);
    }

    public function switchIsPublished($obj, $groups = User::ADMIN_READ): JsonResponse
    {
        $obj->setIsPublished(!$obj->getIsPublished());

        $this->em->flush();
        return $this->apiResponse->apiJsonResponse($obj, $groups);
    }

    public function delete($obj, $isSeen = false, $messageError = "Vous n'avez pas lu ce message."): JsonResponse
    {
        if($isSeen){
            if (!$obj->getIsSeen()) {
                return $this->apiResponse->apiJsonResponseBadRequest($messageError);
            }
        }

        $this->em->remove($obj);
        $this->em->flush();
        return $this->apiResponse->apiJsonResponseSuccessful("Supression réussie !");
    }

    public function deleteSelected($classe, $ids, $isSeen = false): JsonResponse
    {
        $objs = $this->em->getRepository($classe)->findBy(['id' => $ids]);

        if ($objs) {
            foreach ($objs as $obj) {
                if($isSeen){
                    if (!$obj->getIsSeen()) {
                        return $this->apiResponse->apiJsonResponseBadRequest('Vous n\'avez pas lu ce message.');
                    }
                }

                $this->em->remove($obj);
            }
        }

        $this->em->flush();
        return $this->apiResponse->apiJsonResponseSuccessful("Supression de la sélection réussie !");
    }

    public function createDateTimezoneEurope($timezone="Europe/Paris"): \DateTime
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone($timezone));

        return $date;
    }

    public function createFunction(Request $request, DataPlanningItem $dataEntity, ValidatorService $validator, $obj, $setNumGroup = false): JsonResponse
    {
        $data = json_decode($request->getContent());

        if ($data === null) {
            return $this->apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        if (!isset($data->name) || !isset($data->price)) {
            return $this->apiResponse->apiJsonResponseBadRequest('Il manque des données.');
        }

        $obj = $dataEntity->setData($obj, $data, $setNumGroup);
        if(!$obj){
            return $this->apiResponse->apiJsonResponseBadRequest("Une erreur est survenue. Veuillez contacter le support");
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $this->apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $this->em->persist($obj);
        $this->em->flush();

        return $this->apiResponse->apiJsonResponse($obj, User::ADMIN_READ);
    }

    public function updateFunction(Request $request, DataPlanningItem $dataEntity, ValidatorService $validator, $obj): JsonResponse
    {
        $data = json_decode($request->getContent());

        if($data === null){
            return $this->apiResponse->apiJsonResponseBadRequest('Les données sont vides.');
        }

        $obj = $dataEntity->setData($obj, $data);
        if(!$obj){
            return $this->apiResponse->apiJsonResponseBadRequest("Une erreur est survenue. Veuillez contacter le support");
        }

        $noErrors = $validator->validate($obj);
        if ($noErrors !== true) {
            return $this->apiResponse->apiJsonResponseValidationFailed($noErrors);
        }

        $this->em->persist($obj);
        $this->em->flush();

        return $this->apiResponse->apiJsonResponse($obj, User::USER_READ);
    }
}