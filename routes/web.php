<?php

use Illuminate\Support\Facades\Route;
use App\Models\InventorySetting;
use App\Http\Controllers\ProductController;
 Route::get('/', [ProductController::class, 'index'])->middleware(['verify.shopify'])->name('home');
// Route::get('/', function () {
//     $inventory = InventorySetting::first();
//         $inventorythreshold = $inventory->threshold_quantity?? '';
//         $email = $inventory->alert_email ?? '';
//          return view('dashboard', compact('inventorythreshold', 'email'));
//     // return view('welcome');
// })->middleware(['verify.shopify'])->name('home');

Route::get('/checkAllInventory', [ProductController::class, 'checkAllInventory'])->name('checkAllInventory');
Route::post('/settings/save', [ProductController::class, 'saveSettings'])->name('settings.save');