@extends('shopify-app::layouts.default')

@section('content')

    <ui-title-bar title="Welcome"></ui-title-bar>

    <div style="min-height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px;">

        <h2 style="margin-bottom: 30px;">Welcome, {{ $shopDomain }}</h2>

        {{-- Back Button --}}
        <a href="{{ url()->previous() }}" 
           style="
               display: inline-block; 
               padding: 10px 25px; 
               background-color: #008060; 
               color: white; 
               text-decoration: none; 
               border-radius: 6px; 
               font-weight: bold;
               margin-bottom: 40px;
               box-shadow: 0 2px 6px rgba(0, 128, 96, 0.4);
               transition: background-color 0.3s ease;
           "
           onmouseover="this.style.backgroundColor='#006644'"
           onmouseout="this.style.backgroundColor='#008060'">
            ⬅ Back
        </a>

        {{-- Low Inventory --}}
        @if(!empty($lowInventoryProducts))
            <h3 style="color: #d32f2f; margin-bottom: 15px;">⚠️ Low Inventory Products (less than {{ $Inventorythreshold }})</h3>
            <ul style="list-style: none; padding-left: 0; margin-bottom: 40px; max-width: 400px; text-align: left;">
                @foreach ($lowInventoryProducts as $item)
                    <li style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                        <strong>{{ $item['product_title'] }}</strong> — {{ $item['variant_title'] }} ({{ $item['quantity'] }} left)
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- High Inventory --}}
        @if(!empty($normalInventoryProducts))
            <h3 style="color: #388e3c; margin-bottom: 15px;">✅ High Inventory Products ({{ $Inventorythreshold }} or more)</h3>
            <ul style="list-style: none; padding-left: 0; max-width: 400px; text-align: left;">
                @foreach ($normalInventoryProducts as $item)
                    <li style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                        <strong>{{ $item['product_title'] }}</strong> — {{ $item['variant_title'] }} ({{ $item['quantity'] }} in stock)
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Email Sent Confirmation --}}
        <p style="margin-top: 20px; font-weight: 600; color: #444;">
            📬 Email has been sent successfully with product inventory details!
        </p>

    </div>

@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function handleClick(event) {
            const btn = event.target;
            btn.disabled = true;
            btn.innerText = 'Loading...';

            window.location.href = '/kyon/checkAllInventory?shop={{ $shopDomain }}';
        }
    </script>
@endsection
