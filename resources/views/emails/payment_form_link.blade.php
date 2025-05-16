<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Link</title>
</head>
<body>
    <h1>Hello, {{ $booking->client->names }}</h1>
    <p>Your booking <strong>#{{ $booking->id }}</strong> has been created.</p>
    <p>Please complete your payment of <strong>{{ number_format($booking->amount_to_pay,2) }}</strong> by clicking the link below:</p>
    <p><a href="{{ $paymentUrl }}" style="padding:10px 20px; background-color:#4F46E5; color:#fff; text-decoration:none; border-radius:4px;">
        Pay Now
    </a></p>
    <p>If the link doesn’t work, copy and paste this URL into your browser:</p>
    <p>{{ $paymentUrl }}</p>
    <p>Thank you!</p>
</body>
</html>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 0;
        padding: 20px;
        background-color: #f4f4f4;
    }
    h1 {
        color: #333;
    }
    p {
        color: #555;
    }
    a {
        color: #4F46E5;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4F46E5;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
    }
    .button:hover {
        background-color: #3b3f8c;
    }
    @media (max-width: 600px) {
        body {
            padding: 10px;
        }
        h1 {
            font-size: 24px;
        }
        p {
            font-size: 16px;
        }
        .button {
            padding: 8px 16px;
        }
    }
</style>
