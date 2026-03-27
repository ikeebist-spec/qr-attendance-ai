<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CCS FCO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- ParticlesJS for the floating network graph effect seen in the image -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('/images/login-bg.png') no-repeat center center;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }

        /* Subtle dark overlay for the background to make the white glass pop */
        .bg-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 20, 35, 0.4);
            z-index: 1;
        }

        /* Canvas for the glowing network nodes */
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        /* The giant transparent logo perfectly centered */
        .watermark-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            max-width: 90vw;
            opacity: 0.2; /* Very transparent */
            z-index: 3;
            pointer-events: none;
            /* Give it a slight glow/drop shadow so it stands out a bit on various backgrounds */
            filter: drop-shadow(0 0 20px rgba(255,255,255,0.4));
        }

        /* The central glass card mimicking the reference */
        .glass-card {
            background: rgba(255, 255, 255, 0.2); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.45); 
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.15);
            border-radius: 1.5rem;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 3rem 2.5rem;
            position: relative;
            animation: floatCard 6s ease-in-out infinite alternate;
        }

        @keyframes floatCard {
            0% { transform: translateY(0); }
            100% { transform: translateY(-8px); }
        }

        /* Input pill boxes */
        .input-pill {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 9999px;
            display: flex;
            align-items: center;
            padding: 0.6rem 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .input-pill:focus-within {
            background: #fff;
            border-color: #7995ff;
            box-shadow: 0 0 15px rgba(121, 149, 255, 0.4);
        }

        .input-pill input {
            background: transparent;
            border: none;
            outline: none;
            width: 100%;
            padding: 0.5rem;
            margin-left: 0.75rem;
            font-weight: 500;
            color: #1f2937;
            font-size: 0.95rem;
        }

        .input-pill input::placeholder {
            color: #6b7280;
            font-weight: 500;
        }

        .pill-icon {
            color: #6b7280;
            font-size: 1.15rem;
            transition: color 0.3s ease;
        }

        .input-pill:focus-within .pill-icon {
            color: #7995ff;
        }

        /* Solid White Login Button */
        .btn-solid {
            background: #ffffff;
            color: #111827;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-radius: 9999px;
            width: 100%;
            padding: 1rem;
            margin-top: 1rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-solid:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.25);
            background: #f9fafb;
        }
    </style>
</head>
<body>

    <!-- Background Layers -->
    <div class="bg-overlay"></div>
    
    <!-- Giant transparent CCS FCO logo in the very middle -->
    @if(file_exists(public_path('images/logo.png')))
        <img src="/images/logo.png" alt="CCS FCO Watermark" class="watermark-logo">
    @endif

    <!-- Interactive Network Graph matching the image -->
    <div id="particles-js"></div>
    
    <!-- Glassmorphism Card Wrapper -->
    <div class="glass-card text-center">
        
        <!-- Status Indicators (Online / Secured) -->
        <div class="flex justify-center items-center gap-8 mb-6 text-xs font-bold text-white tracking-widest uppercase">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-green-400 shadow-[0_0_8px_#4ade80]"></span>
                <span>Online</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 shadow-[0_0_8px_#facc15]"></span>
                <span>Secured</span>
            </div>
        </div>

        <!-- Heading -->
        <h2 class="text-[1.8rem] font-black text-white mb-8 drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)] tracking-wide">WELCOME BACK!</h2>

        <!-- Alert messages -->
        @if (session('error'))
            <div class="mb-5 bg-red-100/90 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm text-center font-bold shadow-lg">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 bg-red-100/90 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm font-bold shadow-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <!-- Username Pill -->
            <div class="input-pill">
                <i class="fa-regular fa-user pill-icon"></i>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus placeholder="Username">
            </div>

            <!-- Password Pill -->
            <div class="input-pill">
                <i class="fa-solid fa-key pill-icon"></i>
                <input type="password" name="password" id="password" required placeholder="Password">
            </div>

            <!-- Solid White Login Button -->
            <button type="submit" class="btn-solid">
                Login
            </button>
        </form>
    </div>

    <!-- Initialize Network Graph Particles -->
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 70, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.6, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": {
                    "enable": true, "distance": 160, "color": "#ffffff", "opacity": 0.5, "width": 1.5
                },
                "move": {
                    "enable": true, "speed": 1.5, "direction": "none", "random": false, "straight": false,
                    "out_mode": "out", "bounce": false
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": { "enable": true, "mode": "grab" },
                    "onclick": { "enable": true, "mode": "push" },
                    "resize": true
                },
                "modes": {
                    "grab": { "distance": 180, "line_linked": { "opacity": 1 } },
                    "push": { "particles_nb": 4 }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>