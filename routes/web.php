<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopifyAuthController;
use Illuminate\Http\Request;

use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/checkAllInventory', [ProductController::class, 'checkAllInventory'])->name('checkAllInventory');
Route::post('/settings/save', [ProductController::class, 'saveSettings'])->name('settings.save');


Route::get('install', [ShopifyAuthController::class, 'install'])->name('shopify.install');
Route::get('authenticate', [ShopifyAuthController::class, 'authenticate'])->name('shopify.authenticate');

// Route::get('/auth', [AuthController::class, 'authenticate'])->name('shopify.auth');

// Route::get('/', function (Request $request) {
//     // $shop = $request->query('shop'); // e.g., ?shop=yourshop.myshopify.com
//       $shopDomain = $request->query('shop');
//     // dd($shop);
//     return view('welcome', compact('shopDomain'));
// })->name('home');

Route::get('/send-test-mail', function () {
    Mail::raw('Yeh Mailtrap ke through bheja gaya test email hai!', function ($message) {
        $message->to('shubham123@yopmail.com') // koi bhi email de do, Mailtrap pe hi jaayega
                ->subject('Mailtrap Test Email from Laravel');
    });

    return 'Email sent successfully!';
});