<div id="sidebar"
    class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-violet-900 via-purple-900 to-violet-950 text-white flex flex-col shadow-xl flex-shrink-0 z-40 transition-all duration-300 transform -translate-x-full md:translate-x-0 md:relative">
    <div class="p-4 border-b border-violet-800/50 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo.png') }}" alt="CCS - FCO Logo" class="h-10 w-10 object-contain drop-shadow-md">
            <div>
                <h1 class="text-lg font-bold tracking-wider leading-tight">CCS - FCO</h1>
                <p class="text-violet-200 text-xs text-opacity-80">AI Attendance System</p>
            </div>
        </div>
        <button id="sidebar-close" class="md:hidden p-2 text-violet-200 hover:text-white transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1" id="sidebar-nav">
        <button data-tab="dashboard"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium bg-violet-800 text-white"><i
                data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i> <span>Dashboard</span></button>
        <button data-tab="events"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="calendar" class="w-[18px] h-[18px]"></i> <span>Events Management</span></button>
        <button data-tab="scan"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="qr-code" class="w-[18px] h-[18px]"></i> <span>Scan QR</span></button>
        <button data-tab="records"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="users" class="w-[18px] h-[18px]"></i> <span>Attendance Records</span></button>
        <button data-tab="students"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="user-check" class="w-[18px] h-[18px]"></i> <span>Student Masterlist</span></button>
        <button data-tab="ai"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="brain" class="w-[18px] h-[18px]"></i> <span>AI Analytics</span></button>
        <button data-tab="fines"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="receipt" class="w-[18px] h-[18px]"></i> <span>Fine Computation</span></button>
        <button data-tab="logs"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="activity" class="w-[18px] h-[18px]"></i> <span>Activity Logs</span></button>
        <button data-tab="settings"
            class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-violet-100 hover:bg-violet-800/50 hover:text-white"><i
                data-lucide="settings" class="w-[18px] h-[18px]"></i> <span>Settings</span></button>
    </nav>

    <div class="mt-auto border-t border-violet-800/50 bg-violet-950/50 p-4">
        <div class="flex items-center space-x-3 mb-4">
            <div
                class="h-10 w-10 bg-violet-700 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-inner">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="font-medium text-sm truncate text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-violet-300">Active Officer</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 bg-red-600/20 hover:bg-red-600/40 text-red-200 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i> <span>Sign Out</span>
            </button>
        </form>
    </div>
</div>