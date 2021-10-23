<?php

namespace App\Controller\Api\Budget;

use App\Entity\Budget\BuExpense;
use App\Service\Data\DataPlanningItem;
use App\Service\Data\DataService;
use App\Service\ValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/expenses", name="api_expenses_")
 */
class ExpenseController extends AbstractController
{
    private $dataService;

    public function __construct(DataService $dataService)
    {

        $this->dataService = $dataService;
    }

    /**
     * Create an expense
     *
     * @Route("/", name="create", options={"expose"=true}, methods={"POST"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a new expense object"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="JSON empty or missing data or validation failed",
     * )
     *
     * @OA\Tag(name="Expenses")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param DataPlanningItem $dataEntity
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorService $validator, DataPlanningItem $dataEntity): JsonResponse
    {
        return $this->dataService->createFunction($request, $dataEntity, $validator, new BuExpense());
    }

    /**
     * Update an expense
     *
     * @Route("/{id}", name="update", options={"expose"=true}, methods={"PUT"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns an expense object"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     *
     * @OA\Tag(name="Expenses")
     *
     * @param Request $request
     * @param ValidatorService $validator
     * @param BuExpense $obj
     * @param DataPlanningItem $dataEntity
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorService $validator, BuExpense $obj, DataPlanningItem $dataEntity): JsonResponse
    {
        return $this->dataService->updateFunction($request, $dataEntity, $validator, $obj);
    }

    /**
     * Delete an expense
     *
     * @Route("/{id}", name="delete", options={"expose"=true}, methods={"DELETE"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return message successful",
     * )
     *
     * @OA\Tag(name="Expenses")
     *
     * @param BuExpense $obj
     * @param DataPlanningItem $dataEntity
     * @return JsonResponse
     */
    public function delete(BuExpense $obj, DataPlanningItem $dataEntity): JsonResponse
    {
        return $this->dataService->deleteItem($obj, $dataEntity);
    }
}
