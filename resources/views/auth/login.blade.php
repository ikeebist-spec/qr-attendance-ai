<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('/images/login-bg.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Beautiful gradient overlay to mimic the rich purple/pinkish tone */
        .bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(238, 210, 255, 0.75), rgba(167, 139, 250, 0.75), rgba(88, 28, 135, 0.85));
            backdrop-filter: blur(6px);
            z-index: 1;
        }

        /* Seamless glass transparency */
        .glass-panel {
            background: rgba(255, 255, 255, 0.08); /* More transparent */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1); /* Very faint border */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            border-radius: 1.5rem;
            position: relative;
            z-index: 10;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 400px;
        }

        .glass-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(45, 19, 72, 0.3);
            color: #2d1348;
            width: 100%;
            padding: 0.5rem 2rem 0.5rem 0.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .glass-input:focus {
            outline: none;
            border-bottom-color: #2d1348;
        }

        .glass-input::placeholder {
            color: rgba(45, 19, 72, 0.65);
            font-size: 0.95rem;
        }

        .input-icon {
            position: absolute;
            right: 0.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(45, 19, 72, 0.65);
            font-size: 0.85rem;
        }

        .btn-login {
            background: linear-gradient(to right, #2d1348, #401b69);
            box-shadow: 0 8px 15px rgba(45, 19, 72, 0.3);
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(45, 19, 72, 0.4);
        }
    </style>
</head>
<body>
    <div class="bg-overlay"></div>

    <!-- Top Left Logo Container (Without the text) -->
    <div class="absolute top-8 left-10 z-20 flex items-center gap-3">
        @if(file_exists(public_path('images/logo.png')))
        <img src="/images/logo.png" alt="Logo" class="h-10 w-auto opacity-90 drop-shadow-md">
        @endif
    </div>

    <!-- Login Form Card -->
    <div class="glass-panel">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-[#2d1348] tracking-tight">Login</h1>
        </div>

        @if (session('error'))
            <div class="mb-5 bg-red-100/90 border border-red-300 text-red-700 px-4 py-2 rounded-lg text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-5 bg-green-100/90 border border-green-300 text-green-700 px-4 py-2 rounded-lg text-sm text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 bg-red-100/90 border border-red-300 text-red-700 px-4 py-2 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-6">
            @csrf

            <div class="relative">
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                    class="glass-input" placeholder="Username">
                <i class="fa-solid fa-envelope input-icon"></i>
            </div>

            <div class="relative mt-2">
                <input type="password" name="password" id="password" required
                    class="glass-input" placeholder="Password">
                <i class="fa-solid fa-lock input-icon"></i>
            </div>

            <div class="flex items-center justify-between text-xs font-medium mt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-400 text-[#401b69] focus:ring-[#401b69] bg-transparent">
                    <span class="text-[#2d1348]">Remember me</span>
                </label>
            </div>

            <button type="submit"
                class="btn-login w-full text-white font-semibold py-3 rounded-[12px] text-sm mt-8 tracking-wide">
                Login
            </button>
        </form>
    </div>
</body>
</html>