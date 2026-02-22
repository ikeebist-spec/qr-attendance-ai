<!-- 6. AI ANALYTICS -->
<div id="tab-ai" class="tab-content space-y-6">
    <!-- Banner -->
    <div class="bg-indigo-900 rounded-2xl p-8 text-white flex justify-between items-center shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2 flex items-center"><i data-lucide="brain" class="mr-3"></i> AI Analysis Engine</h2>
            <p class="text-indigo-200 max-w-xl">The system has automatically calculated fines based on escalating logic and predicted structural absenteeism risks.</p>
        </div>
        <div class="text-right relative z-10">
            <p class="text-indigo-300 text-sm font-medium uppercase tracking-wider">Total Fine Computations</p>
            <p id="ai-total-fines" class="text-5xl font-black text-white mt-1">₱0</p>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- AI Predictions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-lg"><i data-lucide="alert-triangle" class="mr-2 text-orange-500"></i> System Predictions</h3>
            <div id="ai-predictions-list" class="space-y-3">
                <!-- Rendered via JS -->
            </div>
        </div>

        <!-- At-Risk Sections -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 text-lg">At-Risk Sections (Averaged)</h3>
            <div id="ai-risk-sections-list" class="space-y-3">
                <!-- Rendered via JS -->
            </div>
        </div>
    </div>

    <!-- At-Risk Students Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
            <h3 class="font-bold text-red-800">High-Risk Students (2+ Absences)</h3>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Section</th>
                    <th class="px-6 py-3">Missed Events</th>
                    <th class="px-6 py-3">Computed Fine</th>
                </tr>
            </thead>
            <tbody id="ai-risk-students-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>
    </div>
</div>
