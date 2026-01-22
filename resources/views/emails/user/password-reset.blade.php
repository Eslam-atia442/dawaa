<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>{{ __('trans.password_reset_email_subject') }}</title>
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
        .code-box {
            background-color: #fff;
            border: 2px dashed #dc3545;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #dc3545;
            letter-spacing: 5px;
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
    </style>
</head>
<body>

<header>
    <div class="header">
        <h1>{{ __('trans.password_reset') }}</h1>
    </div>
</header>

<section>
    <div class="container">
        <div class="message">
            <p>{{ __('trans.password_reset_email_message') }}</p>
        </div>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>

        <div class="message">
            <p>{{ __('trans.password_reset_email_footer') }}</p>
        </div>
    </div>
</section>

<footer>
    <div class="footer">
        <p>{{ __('trans.password_reset_email_note') }}</p>
    </div>
</footer>

</body>
</html>