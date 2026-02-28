<!-- 9. FINE COMPUTATION -->
<div id="tab-fines" class="tab-content space-y-6">

    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Fine Computation</h2>
            <p class="text-gray-500">Record and compute student absenteeism fines automatically.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.renderFines()" class="bg-purple-50 text-purple-700 hover:bg-purple-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Refresh List
            </button>
        </div>
    </div>

    <!-- Fine Table section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="fines-table">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Student Name</th>
                        <th class="px-6 py-4 font-semibold text-center w-24">Present</th>
                        <th class="px-6 py-4 font-semibold text-center w-24">Absent</th>
                        <th class="px-6 py-4 font-semibold text-right w-40">Computed Fine</th>
                    </tr>
                </thead>
                <tbody id="fines-tbody" class="divide-y divide-gray-50">
                    <!-- Populated via JS fineComputation.js -->
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Loading fine computations...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
