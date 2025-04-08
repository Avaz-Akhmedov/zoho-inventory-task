<?php

namespace App\Http\Controllers\Inventory;

use App\Exceptions\ZohoException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Services\ZohoInventoryService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(protected ZohoInventoryService $zohoService)
    {
    }

    public function index(): JsonResponse
    {
        try {
            $response = $this->zohoService->getCustomers();
            return response()->json($response);


        } catch (ZohoException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * @param StoreCustomerRequest $request
     * @return JsonResponse
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $response = $this->zohoService->createCustomer($request->validated());

            return response()->json($response);
        } catch (ZohoException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ]);
        }
    }
}
