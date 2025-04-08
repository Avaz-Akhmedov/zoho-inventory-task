<?php

use App\Http\Controllers\ZohoInventoryController;
use Illuminate\Support\Facades\Route;


Route::get('/zoho/inventory-items', [ZohoInventoryController::class, 'getInventoryItems']);
Route::post('/zoho/sales-order', [ZohoInventoryController::class, 'createSalesOrder']);
