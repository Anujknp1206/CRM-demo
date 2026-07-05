<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #fff;
            text-align: center;
            background: linear-gradient(270deg, #0f2027, #203a43, #2c5364);
            background-size: 600% 600%;
            animation: gradientMove 10s ease infinite;
        }

        /* 🔥 Animated Gradient */
        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* ✨ Floating Particles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 10s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
        }

        .circle:nth-child(2) {
            width: 150px;
            height: 150px;
            bottom: 10%;
            right: 10%;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-30px);
            }
        }

        /* 💎 Glass Card */
        .container {
            position: relative;
            max-width: 500px;
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .error-code {
            font-size: 110px;
            font-weight: bold;
            opacity: 0.15;
        }

        .message {
            font-size: 26px;
            margin: 10px 0;
        }

        .description {
            font-size: 15px;
            opacity: 0.8;
            margin-bottom: 25px;
        }

        /* 🚀 Glow Button */
        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            background: linear-gradient(45deg, #448f22, #0cd32d);
            color: #fff;
            transition: 0.4s;
            box-shadow: 0 0 15px rgba(102, 173, 43, 0.6);
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 0 25px rgb(54, 138, 20);
        }

        /* 🎯 Icon Animation */
        .icon {
            font-size: 50px;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }
    </style>
</head>

<body>

    <!-- ✨ Background floating shapes -->
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>

    <div class="container">
        <div class="icon">@yield('icon')</div>
        <div class="error-code">@yield('code')</div>
        <div class="message">@yield('message')</div>
        <div class="description">@yield('description')</div>

        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
            <a href="{{ url('/') }}" class="btn">🏠 Home</a>

            <a href="javascript:history.back()" class="btn" style="background:#555;">
                ⬅ Go Back
            </a>
        </div>
    </div>

</body>

</html>