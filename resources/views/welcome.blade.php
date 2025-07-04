
@extends('shopify-app::layouts.default')

@section('content')
    <ui-title-bar title="Welcome"></ui-title-bar>

<div style="display: flex; justify-content: center; align-items: center; flex-direction: column; min-height: 60vh;">
    <h2 style="margin-bottom: 30px;">Welcome, {{ $shopDomain ?? Auth::user()->name }}</h2>

    <form id="settings-form" method="POST" action="{{ route('settings.save') }}">
        @csrf
        <input type="hidden" name="shop" value="{{ $shopDomain ?? Auth::user()->name }}">

        <div style="margin-bottom: 20px;">
            <label for="threshold_quantity">Threshold Quantity:</label><br>
            <input type="number" name="threshold_quantity" id="threshold_quantity" value="{{ $inventorythreshold }}" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="alert_email">Alert Email:</label><br>
            <input type="email" name="alert_email" id="alert_email" value="{{ $email }}" required>
        </div>

    <button type="submit" class="savebtn" id="save-btn">
        <span id="save-btn-text">Save</span>
        <span id="save-btn-loader" style="display: none;">
            <svg width="18" height="18" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="35" stroke="#fff" stroke-width="10" stroke-dasharray="164" stroke-dashoffset="124" stroke-linecap="round">
                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" from="0 50 50" to="360 50 50" />
                </circle>
            </svg>
        </span>
    </button>
    </form>
        <button type="button" id="get-inventory-btn">
            <span id="btn-text">Get Inventory</span>
            <span id="btn-loader" style="display: none;">
                <svg width="18" height="18" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="35" stroke="#fff" stroke-width="10" stroke-dasharray="164" stroke-dashoffset="124" stroke-linecap="round">
                        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" from="0 50 50" to="360 50 50" />
                    </circle>
                </svg>
            </span>
        </button>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        function handleClick() {
            // Show loader, hide text
            $('#btn-text').hide();
            $('#btn-loader').show();

            // Redirect with shop domain
            window.location.href = '/kyon/checkAllInventory?shop={{ $shopDomain }}';
        }

        $('#get-inventory-btn').on('click', function (e) {
            e.preventDefault();
            handleClick();
        });
        $('#settings-form').on('submit', function (e) {
        e.preventDefault(); // Stop default submission

        // Show loader, hide text
        $('#save-btn-text').hide();
        $('#save-btn-loader').show();

        // Optional: disable button to prevent double clicks
        $('.savebtn').prop('disabled', true);

        // Submit after delay (e.g., 800ms)
        setTimeout(() => {
            this.submit();
        }, 800);
    });
    </script>

    <style>
        #get-inventory-btn,.savebtn {
            background-color: #5c6ac4;
            color: white;
            margin-top: 20px;
            padding: 12px 25px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
            justify-content: center;
            transition: background-color 0.3s ease;
        }
        input {
            padding: 10px;
            border-radius: 5px;
        }
        form#settings-form {
            padding: 50px;
            align-items: center;
            border: 1px solid #ccc;
        }
        #get-inventory-btn:hover {
            background-color: #474fb4;
        }

        #btn-loader svg {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
