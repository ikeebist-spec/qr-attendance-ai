<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – ESSU CCS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('/images/login-bg.png'), #090118;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background mesh overlay */
        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(168, 85, 247, 0.15) 0%, transparent 50%);
            z-index: 1;
            filter: blur(80px);
            animation: pulse 12s ease-in-out infinite alternate;
            pointer-events: none;
        }

        /* Dark overlay for readability */
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(to bottom, rgba(9, 1, 24, 0.7), rgba(9, 1, 24, 0.9));
            z-index: 0;
            pointer-events: none;
        }

        @keyframes pulse {
            0% {
                transform: scale(1) translate(0, 0);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.1) translate(2%, 2%);
                opacity: 0.7;
            }
        }

        /* Premium Glassmorphism with Floating Animation */
        .glass-panel {
            background: rgba(20, 10, 36, 0.5);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(167, 139, 250, 0.15);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.7),
                inset 0 1px 1px rgba(255, 255, 255, 0.1);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .glass-panel:hover {
            transform: translateY(-8px) scale(1.015);
            border-color: rgba(167, 139, 250, 0.4);
            box-shadow:
                0 40px 70px -15px rgba(0, 0, 0, 0.8),
                0 0 30px rgba(139, 92, 246, 0.2);
            animation-play-state: paused;
        }

        /* Interactive Inputs */
        .glass-input {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(167, 139, 250, 0.25);
            color: #fff;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            padding: 1rem 1.5rem;
        }

        .glass-input:focus {
            outline: none;
            border-color: rgba(167, 139, 250, 0.7);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.25);
            transform: scale(1.02);
        }

        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        /* Label float effect */
        .input-group label {
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            pointer-events: none;
        }

        .glass-input:focus+label,
        .glass-input:not(:placeholder-shown)+label {
            color: #c084fc;
            text-shadow: 0 0 15px rgba(168, 85, 247, 0.6);
            transform: translateY(-2px);
        }

        /* Button Enhancement - High Impact */
        .btn-premium {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.5);
        }

        .btn-premium::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .btn-premium:hover::after {
            left: 100%;
        }

        .btn-premium:hover {
            transform: scale(1.03) translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(168, 85, 247, 0.6);
            filter: brightness(1.15);
        }

        .btn-premium:active {
            transform: scale(0.98);
        }

        /* Checkbox */
        .purple-checkbox {
            accent-color: #a855f7;
            background-color: transparent;
        }
    </style>
</head>

<body class="min-h-screen relative overflow-hidden">

    <div class="bg-overlay"></div>
    <div class="bg-mesh"></div>

    <div class="relative z-10 w-full min-h-screen flex items-center justify-center px-4">

        <!-- Login Form Card -->
        <div class="glass-panel w-full max-w-[400px] rounded-[2.5rem] p-10 md:p-14 relative z-20 overflow-hidden">

            <div class="text-center mb-12">
                <div class="inline-block px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-violet-300/80">Powered by
                        AI</span>
                </div>
                <h1 class="text-4xl font-black text-white tracking-tight drop-shadow-2xl">
                    Welcome <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-fuchsia-300">Back</span>
                </h1>
                <p class="text-violet-200/40 mt-3 text-xs font-medium tracking-wide">Enter your credentials to access
                    the portal</p>
            </div>

            @if (session('error'))
                <div
                    class="mb-6 bg-red-500/10 border border-red-500/20 rounded-2xl p-4 text-xs text-red-300 text-center animate-pulse">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('status'))
                <div
                    class="mb-6 bg-green-500/10 border border-green-500/20 rounded-2xl p-4 text-xs text-green-300 text-center">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-2xl p-4">
                    <ul class="text-xs text-red-300 space-y-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-6">
                @csrf

                <div class="relative input-group">
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                        class="glass-input w-full rounded-2xl text-sm" placeholder="Username">
                    <label for="username"
                        class="absolute -top-2.5 left-5 bg-[#140824] px-2 text-[10px] font-bold uppercase tracking-widest text-violet-400/80 rounded-md border border-violet-500/20">Username</label>
                </div>

                <div class="relative input-group mt-8">
                    <input type="password" name="password" id="password" required
                        class="glass-input w-full rounded-2xl text-sm" placeholder="••••••••">
                    <label for="password"
                        class="absolute -top-2.5 left-5 bg-[#140824] px-2 text-[10px] font-bold uppercase tracking-widest text-violet-400/80 rounded-md border border-violet-500/20">Password</label>
                </div>

                {{-- Login links disabled --}}

                <button type="submit"
                    class="btn-premium w-full text-white font-black py-4 rounded-2xl text-xs tracking-[0.25em] uppercase mt-6 shadow-2xl">
                    Sign In
                </button>

                {{-- Registration link disabled --}}
            </form>
        </div>

    </div>

</body>

</html>