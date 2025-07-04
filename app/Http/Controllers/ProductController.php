<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowInventoryAlert;
use App\Models\User;
use App\Models\InventorySetting;



class ProductController extends Controller
{
     public function index(Request $request)
    {
        $shopDomain = $request->query('shop');
        $shop = User::where('name', $shopDomain)->first();
        $inventory = InventorySetting::firstWhere('shop_domain', $shop->name);
        $inventorythreshold = $inventory->threshold_quantity?? '';
        $email = $inventory->alert_email ?? '';
        // dd($inventory);
         return view('welcome', compact('shopDomain', 'inventorythreshold', 'email'));
    }


public function saveSettings(Request $request)
{
    $validated = $request->validate([
        'shop' => 'required|string',
        'threshold_quantity' => 'required|integer|min:1',
        'alert_email' => 'required|email',
    ]);

    // Try to find existing record
    $user = InventorySetting::firstWhere('shop_domain', $validated['shop']);

    if ($user) {
        // Update existing
        $user->update([
            'inventory_threshold' => $validated['threshold_quantity'],
            'alert_email' => $validated['alert_email'],
        ]);
    } else {
        // Create new
        InventorySetting::create([
            'inventory_threshold' => $validated['threshold_quantity'],
            'alert_email' => $validated['alert_email'],
            'shopify_domain' => $validated['shop'],
        ]);
    }

    return redirect()->back()->with('success', 'Settings saved successfully.');
}



public function checkAllInventory(Request $request)
{
    $shopDomain = $request->query('shop');

    if (!$shopDomain) {
        return response('Shop domain required in ?shop=', 400);
    }

    $shop = User::where('name', $shopDomain)->first();
    if (!$shop || !$shop->password) {
        return response('Shop not authenticated or token missing', 403);
    }

    $inventory = InventorySetting::firstWhere('shopify_domain', $shopDomain);
    $threshold = $inventory->threshold_quantity ?? 10;
    $alertEmail = $inventory->alert_email ?? 'uiuxdesigner4756@gmail.com';

    $inventorythreshold = $inventory->threshold_quantity ?? '';
    $email = $inventory->alert_email ?? '';

    // GraphQL query
    $query = <<<'GRAPHQL'
    {
      products(first: 100) {
        edges {
          node {
            title
            variants(first: 10) {
              edges {
                node {
                  title
                  inventoryQuantity
                }
              }
            }
          }
        }
      }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'X-Shopify-Access-Token' => $shop->password,
        'Content-Type' => 'application/json',
    ])->post("https://{$shop->name}/admin/api/2025-01/graphql.json", [
        'query' => $query,
    ]);

    $data = $response->json();
    $products = $data['data']['products']['edges'] ?? [];

    $lowInventoryProducts = [];
    $normalInventoryProducts = [];

    foreach ($products as $productEdge) {
        $product = $productEdge['node'];
        foreach ($product['variants']['edges'] as $variantEdge) {
            $variant = $variantEdge['node'];
            $quantity = $variant['inventoryQuantity'];

            $productInfo = [
                'product_title' => $product['title'],
                'variant_title' => $variant['title'],
                'quantity' => $quantity,
            ];

            if ($quantity < $threshold) {
                $lowInventoryProducts[] = $productInfo;
            } else {
                $normalInventoryProducts[] = $productInfo;
            }
        }
    }

    // Send alert email
    Mail::to($alertEmail)->send(
        new LowInventoryAlert($lowInventoryProducts, $normalInventoryProducts, $inventorythreshold, $email)
    );

    return view('dashboard', [
        'lowInventoryProducts' => $lowInventoryProducts,
        'normalInventoryProducts' => $normalInventoryProducts,
        'shopDomain' => $shopDomain,
        'Inventorythreshold' => $inventorythreshold,
        'Email' => $email
    ]);
}

}

