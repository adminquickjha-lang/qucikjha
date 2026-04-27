<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 40px; max-width: 480px; width: 100%; text-align: center; }
        h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
        p { color: #64748b; font-size: 14px; margin-bottom: 28px; }
        .btn-pay { background: #0ea5e9; color: #fff; font-weight: 700; font-size: 15px; padding: 14px 32px; border: none; border-radius: 10px; cursor: pointer; width: 100%; transition: background 0.2s; }
        .btn-pay:hover { background: #0284c7; }
        .btn-cancel { display: block; margin-top: 14px; color: #94a3b8; font-size: 13px; text-decoration: none; }
        .btn-cancel:hover { color: #475569; }
        .loading { display: none; color: #64748b; font-size: 14px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $title }}</h1>
        <p>Secure payment powered by Paddle</p>
        <button class="btn-pay" id="pay-btn" onclick="openCheckout()">Pay Now</button>
        <p class="loading" id="loading">Opening secure checkout...</p>
        <a href="{{ $cancelUrl }}" class="btn-cancel">← Cancel and go back</a>
    </div>

    <script>
        @if(config('cashier.sandbox'))
        Paddle.Environment.set('sandbox');
        @endif

        Paddle.Initialize({
            token: '{{ config('cashier.client_side_token') }}',
            eventCallback: function(data) {
                if (data.name === 'checkout.completed') {
                    document.getElementById('pay-btn').textContent = 'Payment successful! Redirecting...';
                    document.getElementById('pay-btn').disabled = true;
                    setTimeout(() => { window.location.href = '{{ $checkout->getReturnUrl() }}'; }, 2000);
                }
            }
        });

        function openCheckout() {
            document.getElementById('loading').style.display = 'block';
            Paddle.Checkout.open(@json($checkout->options()));
        }

        // Auto-open on load
        window.addEventListener('load', function() {
            setTimeout(openCheckout, 500);
        });
    </script>
</body>
</html>
