<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowInventoryAlert;
use App\Models\User;



class ProductController extends Controller
{
     public function index(Request $request)
    {
        $shopDomain = $request->query('shop');
        $shop = User::where('name', $shopDomain)->first();
        $Inventorythreshold = $shop->inventory_threshold ?? '';
        $Email = $shop->alert_email ?? '';
         return view('dashboard', compact('shopDomain', 'Inventorythreshold', 'Email'));
    }


public function saveSettings(Request $request)
{
    $validated = $request->validate([
        'shop' => 'required|string',
        'threshold_quantity' => 'required|integer|min:1',
        'alert_email' => 'required|email',
    ]);

    // Find existing user
    $user = User::where('shopify_domain', $validated['shop'])->first();

    if (!$user) {
        return redirect()->back()->withErrors(['shop' => 'User not found.']);
    }

    // Update fields
    $user->update([
        'inventory_threshold' => $validated['threshold_quantity'],
        'alert_email' => $validated['alert_email'],
    ]);

    return redirect()->back()->with('success', 'Settings updated successfully.');
}




    public function checkAllInventory(Request $request)
{


    $shopDomain = $request->query('shop');

    if (!$shopDomain) {
        return response('Shop domain required in ?shop=', 400);
    }

    $shop = User::where('name', $shopDomain)->first();

    if (!$shop || !$shop->access_token) {
        return response('Shop not authenticated or token missing', 403);
    }

    // Use dynamic values
    $threshold = $shop->inventory_threshold ?? 10; // fallback to 10 if null

    $alertEmail = $shop->alert_email ?? 'uiuxdesigner4756@gmail.com';

    $Inventorythreshold = $shop->inventory_threshold ?? '';
    $Email = $shop->alert_email ?? '';
    // Get all products
    $productsResponse = Http::withHeaders([
        'X-Shopify-Access-Token' => $shop->access_token,
    ])->get("https://{$shop->name}/admin/api/2025-01/products.json");

    $products = $productsResponse->json()['products'] ?? [];

    $lowInventoryProducts = [];
    $normalInventoryProducts = [];

    foreach ($products as $product) {
        foreach ($product['variants'] as $variant) {
            $productInfo = [
                'product_title' => $product['title'],
                'variant_title' => $variant['title'],
                'quantity' => $variant['inventory_quantity'],
            ];

            if ($variant['inventory_quantity'] < $threshold) {
                $lowInventoryProducts[] = $productInfo;
            } else {
                $normalInventoryProducts[] = $productInfo;
            }
        }
    }

    // Send email with both low and normal inventory
    Mail::to($alertEmail)->send(
        new LowInventoryAlert($lowInventoryProducts, $normalInventoryProducts, $Inventorythreshold, $Email)
    );

    return view('welcome', [
        'lowInventoryProducts' => $lowInventoryProducts,
        'normalInventoryProducts' => $normalInventoryProducts,
        'shopDomain' => $shopDomain,
        'Inventorythreshold' => $Inventorythreshold,
        'Email' => $Email

    ]);
}

}

