<!-- 4. ATTENDANCE RECORDS -->
<div id="tab-records" class="tab-content">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Recent Logs (<span id="records-event-name"></span>)</h3>
            <span class="text-sm text-gray-500">Total: <span id="records-count">0</span> scans</span>
        </div>
        
        <div id="records-empty" class="p-16 text-center text-gray-500 flex flex-col items-center">
            <i data-lucide="users" class="w-12 h-12 text-gray-300 mb-4"></i>
            <p>No records yet for this event.</p>
        </div>

        <table id="records-table" class="w-full text-left text-sm hidden">
            <thead class="bg-white text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium">ID</th>
                    <th class="px-6 py-4 font-medium">Name</th>
                    <th class="px-6 py-4 font-medium">Section</th>
                    <th class="px-6 py-4 font-medium">Time Scanned</th>
                </tr>
            </thead>
            <tbody id="records-table-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>
    </div>
</div>
