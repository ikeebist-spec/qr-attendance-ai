<div class="w-64 bg-purple-900 text-white flex flex-col shadow-xl flex-shrink-0 z-20 relative">
    <div class="p-6 border-b border-purple-800">
        <h1 class="text-xl font-bold tracking-wider">ESSU CCS</h1>
        <p class="text-blue-200 text-xs mt-1">AI Attendance System</p>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-1" id="sidebar-nav">
        <button data-tab="dashboard" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium bg-purple-800 text-white"><i data-lucide="layout-dashboard" class="w-[18px] h-[18px]"></i> <span>Dashboard</span></button>
        <button data-tab="events" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="calendar" class="w-[18px] h-[18px]"></i> <span>Events Management</span></button>
        <button data-tab="scan" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="qr-code" class="w-[18px] h-[18px]"></i> <span>Scan QR</span></button>
        <button data-tab="records" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="users" class="w-[18px] h-[18px]"></i> <span>Attendance Records</span></button>
        <button data-tab="students" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="user-check" class="w-[18px] h-[18px]"></i> <span>Student Masterlist</span></button>
        <button data-tab="ai" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="brain" class="w-[18px] h-[18px]"></i> <span>AI Analytics</span></button>
        <button data-tab="fines" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="receipt" class="w-[18px] h-[18px]"></i> <span>Fine Computation</span></button>
        <button data-tab="logs" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="activity" class="w-[18px] h-[18px]"></i> <span>Activity Logs</span></button>
        <button data-tab="settings" class="sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-purple-100 hover:bg-purple-800/50"><i data-lucide="settings" class="w-[18px] h-[18px]"></i> <span>Settings</span></button>
    </nav>
    
    <div class="mt-auto border-t border-purple-800 bg-purple-950 p-4">
        <div class="flex items-center space-x-3 mb-4">
            <div class="h-10 w-10 bg-purple-700 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="font-medium text-sm truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-blue-300">Active Officer</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-red-600/20 hover:bg-red-600/40 text-red-200 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i> <span>Sign Out</span>
            </button>
        </form>
    </div>
</div>
