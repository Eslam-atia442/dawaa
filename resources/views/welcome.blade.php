<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for animations and styles -->
    <style>
        /* Custom Background for the body */
        body {
            background: linear-gradient(to right, #6fff5f, #232bc6);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        /* Container for centering content */
        .welcome-container {
            text-align: center;
            padding: 20px;
        }

        /* Animation for the heading */
        .welcome-heading {
            font-size: 3rem;
            animation: fadeIn 2s ease-in-out;
        }

        /* Fade-in animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Bounce animation */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-30px); }
            60% { transform: translateY(-15px); }
        }

        /* Animation for the button */
        .welcome-button {
            margin-top: 20px;
            animation: bounce 2s infinite;
        }

        /* Styling for the footer */
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 0.8rem;
            color: #fff;
        }

        /* Custom styling for the images */
        .welcome-image {
            width: 100%;
            max-width: 400px;''
            margin: 20px auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
<div class="welcome-container">
    <h1 class="welcome-heading">Welcome to Our {{ env('APP_NAME') }}!</h1>
     <a class="btn btn-primary welcome-button" href="{{route('admin.home')}}">Get Started</a>
</div>

<div class="footer">
    , {{__('trans.Copyrights')}} &copy; {{\Carbon\Carbon::now()->year}} <a href="https://eslamatia.com/" target="_blank" class="fw-medium">{{__('trans.comany_name')}}</a>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
