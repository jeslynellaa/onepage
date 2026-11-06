<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Maintenance — OnePage</title>
    <link rel="icon" type="image/png" href="{{ asset('onepage-blue.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #F5F7FA; color: #0B1020; padding: 24px;
        }
        .card { max-width: 460px; text-align: center; }
        .logo { height: 26px; width: auto; margin-bottom: 8px; }
        /* multiply blends the illustration's white backdrop into the page background */
        .illustration { display: block; width: 100%; max-width: 280px; height: auto; margin: 0 auto 8px; mix-blend-mode: multiply; }
        h1 { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; margin: 0 0 12px; }
        p { font-size: 15px; line-height: 1.65; color: #5B6478; margin: 0 0 28px; }
        a {
            display: inline-block; background: #0B1020; color: #fff; text-decoration: none;
            font-size: 14.5px; font-weight: 600; padding: 13px 26px; border-radius: 999px;
        }
        a:hover { background: #1F6FEB; }
    </style>
</head>
<body>
    <div class="card">
        <img class="logo" src="{{ asset('onepage-name.png') }}" alt="OnePage">
        <img class="illustration" src="{{ asset('img/maintenance.png') }}" alt="" width="834" height="834">
        <h1>We're doing some maintenance</h1>
        <p>OnePage is temporarily unavailable while we carry out scheduled maintenance. Sign-in will be back shortly. Thank you for your patience.</p>
        <a href="{{ url('/') }}">Back to Home</a>
    </div>
</body>
</html>
