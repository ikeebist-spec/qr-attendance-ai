<!-- 2. EVENTS MANAGEMENT -->
<div id="tab-events" class="tab-content space-y-6">
    <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-purple-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i data-lucide="calendar" class="mr-2 text-[#A044FF] w-5 h-5"></i> Create New Event
        </h3>
        <form id="add-event-form" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input required id="event-name" placeholder="Event Name (e.g. Alay sa Paaralan)"
                    class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none md:col-span-2" />
                <input required id="event-date" type="date"
                    class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none md:col-span-1" />
                <select required id="event-month"
                    class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none md:col-span-1">
                    <option value="" disabled selected>Select Month</option>
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

            <!-- MODE SELECTION -->
            <div class="flex items-center justify-center space-x-4 bg-gray-50 p-2 rounded-xl border border-gray-100 max-w-sm mx-auto">
                <button type="button" id="btn-mode-single" class="flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all bg-purple-600 text-white shadow-sm">
                    Single Scan
                </button>
                <button type="button" id="btn-mode-multi" class="flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-gray-500 hover:bg-gray-100">
                    Multi-Session
                </button>
                <input type="hidden" id="event-is-single-scan" value="1">
            </div>

            <!-- SINGLE SCAN WINDOW (Shown by default) -->
            <div id="section-single-scan" class="bg-green-50/50 p-6 rounded-xl border border-green-100 space-y-4 max-w-lg mx-auto">
                <h4 class="text-xs font-bold text-green-700 uppercase tracking-widest flex items-center justify-center">
                    <i data-lucide="scan" class="w-4 h-4 mr-2"></i> Attendance Window
                </h4>
                <div class="flex items-center justify-center gap-4">
                    <div class="space-y-1 flex-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase block text-center">Start Time</label>
                        <input id="event-start-time" type="time" class="border p-2 rounded text-sm w-full" />
                    </div>
                    <span class="text-gray-400 mt-4 text-xl">→</span>
                    <div class="space-y-1 flex-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase block text-center">End Time</label>
                        <input id="event-end-time" type="time" class="border p-2 rounded text-sm w-full" />
                    </div>
                </div>
                <p class="text-[10px] text-green-600/70 text-center italic">Students will only need to scan once during this period.</p>
            </div>

            <div id="section-multi-session" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Morning Session -->
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
                    <h4 class="text-xs font-bold text-blue-700 uppercase tracking-widest flex items-center">
                        <i data-lucide="sun" class="w-4 h-4 mr-2"></i> Morning Sessions
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Login Window</label>
                            <div class="flex items-center gap-1">
                                <input id="morn-in-start" type="time" class="border p-1.5 rounded text-xs w-full" />
                                <span class="text-gray-400">-</span>
                                <input id="morn-in-end" type="time" class="border p-1.5 rounded text-xs w-full" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Logout Window</label>
                            <div class="flex items-center gap-1">
                                <input id="morn-out-start" type="time" class="border p-1.5 rounded text-xs w-full" />
                                <span class="text-gray-400">-</span>
                                <input id="morn-out-end" type="time" class="border p-1.5 rounded text-xs w-full" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Afternoon Session -->
                <div class="bg-purple-50/50 p-4 rounded-xl border border-purple-100 space-y-4">
                    <h4 class="text-xs font-bold text-purple-700 uppercase tracking-widest flex items-center">
                        <i data-lucide="sunset" class="w-4 h-4 mr-2"></i> Afternoon Sessions
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Login Window</label>
                            <div class="flex items-center gap-1">
                                <input id="aft-in-start" type="time" class="border p-1.5 rounded text-xs w-full" />
                                <span class="text-gray-400">-</span>
                                <input id="aft-in-end" type="time" class="border p-1.5 rounded text-xs w-full" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Logout Window</label>
                            <div class="flex items-center gap-1">
                                <input id="aft-out-start" type="time" class="border p-1.5 rounded text-xs w-full" />
                                <span class="text-gray-400">-</span>
                                <input id="aft-out-end" type="time" class="border p-1.5 rounded text-xs w-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-1">
                    <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Event Fine</label>
                    <input required id="event-fine" type="number" min="20" max="50" placeholder="Fine ₱20-50"
                        class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none w-full" />
                </div>
                <div class="md:col-span-1">
                    <label class="text-[10px] font-bold text-gray-500 uppercase block mb-1">Event Type</label>
                    <select id="event-type" class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none w-full">
                        <option value="Mandatory">Mandatory</option>
                        <option value="Major">Major</option>
                        <option value="Voluntary">Voluntary</option>
                    </select>
                </div>
                <button type="submit"
                    class="bg-[#A044FF] text-white font-bold rounded hover:bg-[#8B3DDF] transition-all md:col-span-2 py-3 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Create Event with Session Windows
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-purple-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-purple-100 bg-white/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Event List</h3>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white/50 text-gray-500 border-b border-purple-100">
                <tr>
                    <th class="px-6 py-4 font-medium">Event ID</th>
                    <th class="px-6 py-4 font-medium">Name</th>
                    <th class="px-6 py-4 font-medium">Month</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium">Scanning Window</th>
                    <th class="px-6 py-4 font-medium">Fine</th>
                    <th class="px-6 py-4 font-medium">Type</th>
                    <th class="px-6 py-4 font-medium text-center">Action</th>
                </tr>
            </thead>
            <tbody id="events-table-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>
    </div>
</div>