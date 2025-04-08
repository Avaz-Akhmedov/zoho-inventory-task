<?php

namespace App\Services;

use App\Models\ZohoToken;
use App\Exceptions\ZohoException;
use Illuminate\Support\Facades\Http;

class ZohoInventoryService
{
    protected string $baseUrl;
    private string $accessToken;
    private string $organizationId;

    /**
     * @throws ZohoException()
     */
    public function __construct()
    {
        $this->baseUrl = config('zoho.inventory_api_url');
        $this->accessToken = $this->getAccessToken();
        $this->organizationId = config('zoho.organization_id');
    }

    /**
     * @throws ZohoException
     */
    public function getAccessToken(): string
    {
        $token = ZohoToken::query()->latest()->first();

        if ($token && now()->lte($token->expires_at)) {
            return $token->token;
        }

        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => config('zoho.refresh_token'),
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            throw new ZohoException('Failed to refresh Zoho access token');
        }

        $data = $response->json();

        ZohoToken::query()->updateOrCreate([
            'expires_at' => ZohoToken::query()->max('expires_at')
        ], [
            'token' => $data['access_token'],
            'expires_at' => now()->addMinutes(10)
        ]);

        return $data['access_token'];
    }


    /**
     * @throws ZohoException
     */
    public function getInventoryItems()
    {
        $response = Http::withHeaders($this->headers())->get("{$this->baseUrl}/items", [
            'organization_id' => $this->organizationId
        ]);

        if ($response->failed()) {
            throw new ZohoException('Failed to fetch inventory items');
        }

        $data = $response->json();

        return $data['items'];
    }


    /**
     * @throws ZohoException
     */
    public function createCustomerAndSalesOrder(array $data): array
    {
        $customerResponse = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/contacts", [
                'contact_name' => $data['customer']['name'],
                'contact_persons' => [[
                    'email' => $data['customer']['email'],
                    'phone' => $data['customer']['phone']
                ]]
            ]);

        $customer = $customerResponse->json()['contact'] ?? null;

        if (!$customer || !isset($customer['contact_id'])) {
            throw new ZohoException('Failed to create customer');
        }


        $salesOrderResponse = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/salesorders", [
                'customer_id' => $customer['contact_id'],
                'line_items' => $data['items']
            ]);

        if ($salesOrderResponse->failed()) {
            throw new ZohoException('Failed to create sales order');
        }

        $salesOrder = $salesOrderResponse->json()['sales_order'] ?? null;

        return [
            'customer' => $customer,
            'salesOrder' => $salesOrder
        ];

    }

    private function headers(): array
    {
        return [
            'Authorization' => "Zoho-oauthtoken {$this->accessToken}",
            'Content-Type' => 'application/json',
        ];
    }
}
