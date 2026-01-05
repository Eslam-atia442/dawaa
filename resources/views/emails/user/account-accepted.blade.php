@php
    $locale = $locale ?? app()->getLocale();
    app()->setLocale($locale);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"/>
    <title>{{ __('trans.account_accepted_email_subject') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .password-box {
            background-color: #fff;
            border: 2px dashed #28a745;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .password {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 2px;
            font-family: monospace;
        }
        .message {
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>

<header>
    <div class="header">
        <h1>{{ __('trans.welcome') }} {{ $user->name }}</h1>
    </div>
</header>

<section>
    <div class="container">
        <div class="message">
            <p>{{ __('trans.account_accepted_email_message', ['name' => $user->name]) }}</p>
        </div>
        
        <div class="password-box">
            <p style="margin: 0 0 10px 0; color: #666;">{{ __('trans.your_password') }}</p>
            <div class="password">{{ $password }}</div>
        </div>
        
        <div class="warning">
            <p style="margin: 0;"><strong>{{ __('trans.important') }}:</strong> {{ __('trans.account_accepted_password_note') }}</p>
        </div>
        
        <div class="message">
            <p>{{ __('trans.account_accepted_email_footer') }}</p>
        </div>
    </div>
</section>

<footer>
    <div class="footer">
        <p>{{ __('trans.account_accepted_email_note') }}</p>
    </div>
</footer>

</body>
</html>

