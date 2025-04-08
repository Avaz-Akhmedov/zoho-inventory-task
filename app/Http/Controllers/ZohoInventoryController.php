<?php

namespace App\Http\Controllers;

use App\Exceptions\ZohoException;
use App\Http\Requests\CreateCustomerAndSalesRequest;
use App\Services\ZohoInventoryService;
use Exception;
use Illuminate\Http\JsonResponse;

class ZohoInventoryController extends Controller
{
    public function __construct(protected ZohoInventoryService $zohoService)
    {
    }

    /**
     * @throws Exception
     */
    public function getInventoryItems(): JsonResponse
    {
        try {
            $response = $this->zohoService->getInventoryItems();

            return response()->json($response);
        } catch (ZohoException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }


    public function createSalesOrder(CreateCustomerAndSalesRequest $request): JsonResponse
    {
        try {
            $response = $this->zohoService->createCustomerAndSalesOrder($request->validated());

            return response()->json($response);
        } catch (ZohoException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }

    }
}
