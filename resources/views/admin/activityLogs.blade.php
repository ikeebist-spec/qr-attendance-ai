<!-- 7. ACTIVITY LOGS -->
<div id="tab-logs" class="tab-content">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i data-lucide="scroll-text" class="w-5 h-5 mr-2 text-purple-600"></i>
                System Activity Logs
            </h3>
            <div class="relative w-full md:w-64">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="log-search-input" placeholder="Search ID or Event..." 
                    class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-all">
            </div>
        </div>
        <div id="logs-empty" class="p-16 text-center text-gray-500 flex flex-col items-center">
            <i data-lucide="activity" class="w-12 h-12 text-gray-300 mb-4"></i>
            <p>No recent activity to display.</p>
        </div>
        <ul id="logs-list" class="divide-y divide-gray-100 hidden">
            <!-- Rendered via JS -->
        </ul>
    </div>
</div>
