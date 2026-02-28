<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password – ESSU CCS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-image: url('/images/building.png'), url('/images/building.jpg'), linear-gradient(to bottom right, #000, #1a0033);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-panel {
            background: rgba(10, 0, 20, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(167, 139, 250, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.7);
        }

        .ring-wrapper {
            position: absolute;
            inset: -40px;
            pointer-events: none;
            z-index: -1;
        }

        .animated-ticks {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: repeating-conic-gradient(
                from 0deg,
                rgba(139, 92, 246, 0.15) 0deg 3deg,
                transparent 3deg 6deg
            );
            -webkit-mask: radial-gradient(farthest-side, transparent 83%, black 85%);
            mask: radial-gradient(farthest-side, transparent 83%, black 85%);
        }

        .animated-ticks-glow {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: conic-gradient(
                from 0deg, 
                transparent 180deg, 
                rgba(192, 132, 252, 0.2) 270deg, 
                rgba(216, 180, 254, 1) 360deg
            );
            -webkit-mask-image: 
                repeating-conic-gradient(from 0deg, black 0deg 3deg, transparent 3deg 6deg), 
                radial-gradient(farthest-side, transparent 83%, black 85%);
            -webkit-mask-composite: source-in;
            mask-image: 
                repeating-conic-gradient(from 0deg, black 0deg 3deg, transparent 3deg 6deg), 
                radial-gradient(farthest-side, transparent 83%, black 85%);
            mask-composite: intersect;
            animation: spin 3s linear infinite;
            filter: drop-shadow(0 0 15px rgba(216, 180, 254, 0.8));
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(167, 139, 250, 0.3);
            color: #fff;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            outline: none;
            border-color: rgba(216, 180, 254, 0.8);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);
        }
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden py-10">

<div class="absolute inset-0 bg-black/50 z-0 pointer-events-none"></div>

<div class="relative z-10 w-full max-w-[400px] flex items-center justify-center">

    <div class="ring-wrapper hidden sm:block h-[500px]">
        <div class="animated-ticks"></div>
        <div class="animated-ticks-glow"></div>
    </div>

    <!-- Form Card -->
    <div class="glass-panel w-full rounded-[2rem] p-8 relative z-20">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-violet-300 tracking-wide drop-shadow-lg">Reset Password</h1>
            <p class="text-violet-200/70 mt-2 text-xs">Enter your new password below.</p>
        </div>

        @if ($errors->any())
        <div class="mb-5 bg-red-900/50 border border-red-500/50 rounded-xl p-3">
            <ul class="text-sm text-red-200 space-y-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="relative">
                <label class="absolute -top-2.5 left-4 bg-[#140824] px-1 text-xs font-semibold text-violet-300 rounded">Email</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus readonly
                    class="glass-input w-full rounded-full px-5 py-3 text-sm cursor-not-allowed opacity-70">
            </div>

            <div class="relative mt-5">
                <label class="absolute -top-2.5 left-4 bg-[#140824] px-1 text-xs font-semibold text-violet-300 rounded">New Password</label>
                <input type="password" name="password" required minlength="8"
                    class="glass-input w-full rounded-full px-5 py-3 text-sm"
                    placeholder="Min 8 characters">
            </div>

            <div class="relative mt-5">
                <label class="absolute -top-2.5 left-4 bg-[#140824] px-1 text-xs font-semibold text-violet-300 rounded">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="glass-input w-full rounded-full px-5 py-3 text-sm"
                    placeholder="Repeat password">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-violet-600 to-purple-500 hover:from-violet-500 hover:to-purple-400 text-white font-bold py-3 rounded-full transition-all shadow-[0_0_15px_rgba(139,92,246,0.5)] hover:shadow-[0_0_25px_rgba(168,85,247,0.8)] text-sm tracking-wider uppercase mt-6">
                Reset Password
            </button>
        </form>
    </div>

</div>

</body>
</html>
