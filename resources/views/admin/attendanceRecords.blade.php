<!-- 4. ATTENDANCE RECORDS -->
<div id="tab-records" class="tab-content">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
            <div class="flex items-center space-x-4">
                <h3 class="font-bold text-gray-800">Attendance Records</h3>
                <div
                    class="flex items-center text-sm font-medium text-purple-700 bg-purple-50 px-3 py-1 rounded-full border border-purple-100">
                    <i data-lucide="calendar" class="w-[14px] h-[14px] mr-2"></i>
                    <select id="records-month-selector"
                        class="bg-transparent outline-none font-bold text-purple-800 cursor-pointer">
                        <option value="">Select Month</option>
                        <option value="January">January</option>
                        <option value="February">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                        <option value="May">May</option>
                        <option value="June">June</option>
                        <option value="July">July</option>
                        <option value="August">August</option>
                        <option value="September">September</option>
                        <option value="October">October</option>
                        <option value="November">November</option>
                        <option value="December">December</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button id="btn-compilation-mode"
                    class="text-xs font-bold uppercase tracking-widest bg-white border border-gray-200 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                    <i data-lucide="table-properties" class="w-4 h-4 mr-2 text-green-600"></i>
                    Excel View
                </button>
                <button id="btn-download-csv"
                    class="text-xs font-bold uppercase tracking-widest bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center hidden">
                    <i data-lucide="download" class="w-4 h-4 mr-2 text-white"></i>
                    Download Excel (CSV)
                </button>
                <span id="records-event-info" class="text-xs text-gray-500 font-medium">Event: <span
                        id="records-event-name" class="text-gray-800 font-bold"></span></span>
                <span class="text-xs text-gray-400">| Total: <span id="records-count"
                        class="font-bold text-gray-700">0</span></span>
            </div>
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
                    <th class="px-6 py-4 font-medium">Year and Section</th>
                    <th class="px-6 py-4 font-medium">Session</th>
                    <th class="px-6 py-4 font-medium text-center">Status & Time</th>
                </tr>
            </thead>
            <tbody id="records-table-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>

        <!-- Monthly Compilation Table (Excel look) -->
        <div id="compilation-container" class="hidden overflow-x-auto">
            <table id="compilation-table" class="min-w-full border-collapse border border-gray-200 text-xs">
                <thead id="compilation-thead" class="bg-gray-100 sticky top-0 z-10">
                    <!-- Dynamic Headers: [Student Info] | [Event 1] | [Event 2] | ... -->
                </thead>
                <tbody id="compilation-tbody" class="bg-white">
                    <!-- Dynamic Student Rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>