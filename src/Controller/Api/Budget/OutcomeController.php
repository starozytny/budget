<?php

namespace App\Controller\Api\Budget;

use App\Entity\Budget\BuExpense;
use App\Entity\Budget\BuOutcome;
use App\Service\Data\DataPlanningItem;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/outcomes", name="api_outcomes_")
 */
class OutcomeController extends AbstractController
{
    private $dataService;

    public function __construct(DataService $dataService)
    {

        $this->dataService = $dataService;
    }

    /**
     * Create an outcome
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new outcome object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Outcomes")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param DataPlanningItem $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, DataPlanningItem $dataEntity): JsonResponse
    {
        return $this->dataService->createFunction($request, $dataEntity, $validator, new BuOutcome(), true);
    }

    /**
     * Update an outcome
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an outcome object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Outcomes")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param BuOutcome $obj
     * @param DataPlanningItem $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, BuOutcome $obj, DataPlanningItem $dataEntity): JsonResponse
    {
        return $this->dataService->updateFunction($request, $dataEntity, $validator, $obj);
    }

    /**
     * Delete an outcome
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Outcomes")
     *
     * @param BuOutcome $obj
     * @return JsonResponse
     */
    public function delete(BuOutcome $obj): JsonResponse
    {
        return $this->dataService->delete($obj);
    }

    /**
     * Spread an outcome to others months of year
     *
     * @Route("/spread/{id}", name="spread", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an outcome object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Outcomes")
     *
     * @param BuOutcome $obj
     * @param DataPlanningItem $dataEntity
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function spread(BuOutcome $obj, DataPlanningItem $dataEntity, SerializerInterface $serializer): JsonResponse
    {
        return $this->dataService->spreadFunction($serializer, $dataEntity, $obj, new BuOutcome());
    }
}
