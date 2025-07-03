<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ShopifyAuthController extends Controller
{
    public function install(Request $request)
    {
        $shop = $request->query('shop');
        if (!$shop) {
            return response('Missing shop parameter', 400);
        }

        $clientId = env('SHOPIFY_API_KEY');
        $scopes = env('SHOPIFY_SCOPES', 'read_products,write_products');
        $redirectUri = urlencode(env('SHOPIFY_REDIRECT_URI'));
        $state = csrf_token(); // You can save this in session

        $installUrl = "https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id' => $clientId,
            'scope' => $scopes,
            'redirect_uri' => env('SHOPIFY_REDIRECT_URI'),
            'state' => $state,
            'grant_options[]' => 'per-user',
        ]);

        return redirect($installUrl);
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'shop' => 'required',
            'state' => 'required',
        ]);

        $code = $request->get('code');
        $shop = $request->get('shop');

        $clientId = env('SHOPIFY_API_KEY');
        $clientSecret = env('SHOPIFY_API_SECRET');

        $response = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ]);

        if (!$response->successful()) {
            return response('Failed to get access token', 400);
        }

        $data = $response->json();
        $accessToken = $data['access_token'];

        // TODO: Save shop and access token to DB
        // e.g., Shop::updateOrCreate(['domain' => $shop], ['token' => $accessToken]);

User::updateOrCreate(
   
    [
        'access_token' => $accessToken,
        'shopify_domain' => $shop,
        'name' => $shop,
        'email' => 'shop@' . $shop,
        'password' => '',
    ] // data to update or insert
);

    return redirect('/?shop=' . $shop);
  
    }
}
