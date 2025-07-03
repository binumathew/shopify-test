<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Shop;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $shop = $request->get('shop');
        $code = $request->get('code');

        if (!$shop || !$code) {
            return redirect('/')->withErrors('Missing shop or code');
        }

        // ⚠️ Get API key & secret from config/services.php (which reads .env)
        $clientId = config('services.shopify.key');
        $clientSecret = config('services.shopify.secret');

        // 🚀 Exchange temporary code for permanent token
        $response = Http::post("https://$shop/admin/oauth/access_token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to retrieve access token from Shopify'], 500);
        }

        $data = $response->json();
        $accessToken = $data['access_token'] ?? null;

        if (!$accessToken) {
            return response()->json(['error' => 'Access token missing in response'], 500);
        }

        // ✅ Save or update shop
        $shopModel = Shop::updateOrCreate(
            ['shopify_domain' => $shop],
            ['shopify_token' => $accessToken]
        );

        // ✅ Laravel Auth login
        auth()->login($shopModel);

        // 🚀 Redirect to dashboard or wherever you want
        return redirect('/dashboard')->with('success', 'Shop connected successfully!');
    }
}
