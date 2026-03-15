<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
        }
        .btn-login {
            padding: 12px 24px;
            font-size: 18px;
            background-color: #3490dc;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-login:hover {
            background-color: #2779bd;
        }
    </style>
</head>
<body>
    <a href="{{ route('login.page') }}" class="btn-login">Go to Login</a>
</body>
</html>
