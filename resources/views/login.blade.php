<div id="login-view" class="min-h-screen bg-gray-50 flex items-center justify-center p-4 absolute inset-0 z-50 transition-opacity duration-300">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-green-900 p-8 text-center text-white">
            <h1 class="text-3xl font-bold tracking-wider mb-2">ESSU CCS</h1>
            <p class="text-green-200">AI Powered Attendance System</p>
        </div>
        <div class="p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Officer Login</h2>
            <form id="login-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="login-username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. FCO_Admin1" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="login-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none" placeholder="••••••••" required />
                </div>
                <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-3 rounded-lg transition-colors mt-4">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
