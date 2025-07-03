<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrameHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Allow iframe from Shopify Admin
        $shop = $request->get('shop') ?? '*.myshopify.com';
        $response->headers->set('X-Frame-Options', 'ALLOWALL');
        $response->headers->set('Content-Security-Policy', "frame-ancestors https://{$shop} https://admin.shopify.com https://*.shopify.com");

        return $response;
    }
}
