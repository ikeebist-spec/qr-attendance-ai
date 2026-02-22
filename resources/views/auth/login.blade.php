<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – ESSU CCS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-900 via-green-800 to-emerald-900 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/20">
            <span class="text-4xl font-black text-white">C</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Admin Login</h1>
        <p class="text-green-200 mt-1 text-sm">ESSU CCS AI Powered Attendance System</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
            <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="/login" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                    placeholder="yourname@gmail.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                    placeholder="Your password">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-green-600">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-lg transition-colors text-sm mt-2">
                Sign In to Dashboard
            </button>
        </form>
    </div>

    <p class="text-center text-green-200 text-xs mt-6">ESSU CCS AI Attendance System</p>
</div>

</body>
</html>
