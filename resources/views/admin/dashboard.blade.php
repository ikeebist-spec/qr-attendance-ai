<!-- 1. DASHBOARD -->
<div id="tab-dashboard" class="tab-content active space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-blue-50 text-blue-600"><i data-lucide="user-check"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Present (Current Event)</p>
                <h3 id="dash-present" class="text-2xl font-black text-gray-800 mt-1">0</h3>
            </div>
        </div>
        <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-purple-50 text-purple-600"><i data-lucide="users"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Students</p>
                <h3 id="dash-total" class="text-2xl font-black text-gray-800 mt-1">0</h3>
            </div>
        </div>
        <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-purple-100 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-purple-50 text-purple-600"><i data-lucide="brain"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">AI Fine Estimate</p>
                <h3 id="dash-fine" class="text-2xl font-black text-gray-800 mt-1">₱0</h3>
            </div>
        </div>
        <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-red-100 flex items-center space-x-4">
            <div class="p-4 rounded-xl bg-red-50 text-red-600"><i data-lucide="shield-alert"></i></div>
            <div>
                <p class="text-sm text-gray-500 font-medium">At Risk Students</p>
                <h3 id="dash-risk" class="text-2xl font-black text-gray-800 mt-1">0</h3>
            </div>
        </div>
    </div>

    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-purple-100 p-6 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
            <p class="text-gray-500 text-sm mt-1">Manage today's attendance activities efficiently.</p>
        </div>
        <div class="space-x-4">
            <button onclick="window.switchTab('scan')" class="bg-[#A044FF] hover:bg-[#8B3DDF] text-white px-6 py-2 rounded-lg font-medium transition-colors">Open Scanner</button>
            <button onclick="window.switchTab('students')" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors">Add Student</button>
        </div>
    </div>
</div>
