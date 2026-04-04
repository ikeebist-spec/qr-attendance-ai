<!-- 3. SCAN QR -->
<div id="tab-scan" class="tab-content">
    <div class="flex flex-col items-center max-w-2xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-sm border border-purple-100 w-full text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Live QR Scanner</h3>
            <p class="text-gray-500 mb-2">Click "Start Scanning" to activate your camera.</p>
            <p class="text-[10px] text-purple-600 font-bold uppercase tracking-widest mb-4 flex items-center justify-center">
                <i data-lucide="zap" class="w-3 h-3 mr-1 fill-purple-600"></i> Smart Session Detection Active
            </p>
            
            <div class="mb-6 flex flex-col items-center justify-center gap-4 w-full max-w-md mx-auto">
                <div class="flex flex-col items-center gap-2 w-full">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Selected Event</span>
                    <div class="flex items-center text-sm font-medium text-blue-700 bg-blue-50 px-4 py-2.5 rounded-xl border border-blue-100 shadow-sm w-full transition-all hover:border-blue-300">
                        <i data-lucide="calendar" class="w-5 h-5 mr-3 text-blue-500"></i>
                        <select id="event-selector" class="bg-transparent outline-none font-bold text-blue-900 cursor-pointer w-full appearance-none">
                            <!-- Populated via JS -->
                        </select>
                    </div>
                </div>

                <div id="session-selector-container" class="flex flex-col items-center gap-2 w-full">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Attendance Session</label>
                    <select id="log-type-selector" class="border p-2 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none w-full bg-gray-50/50 text-sm font-bold text-gray-700">
                        <option value="Morning In">Morning In</option>
                        <option value="Morning Out">Morning Out</option>
                        <option value="Afternoon In">Afternoon In</option>
                        <option value="Afternoon Out">Afternoon Out</option>
                    </select>
                </div>
            </div>
            <!-- Keep the element for JS logic but visually hide it since the selector now shows the active event -->
            <span id="scan-event-name" style="display:none;"></span>
            
            <div class="rounded-xl overflow-hidden border-2 border-purple-100 w-full max-w-md mx-auto bg-white mb-8 shadow-inner">
                <div id="qr-reader" class="w-full min-h-[300px]"></div>
            </div>
            
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <button onclick="window.simulateScan('2021-0001')" class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">Test Scan (Valid)</button>
                <button onclick="window.simulateScan('INVALID-QR')" class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">Test Scan (Invalid)</button>
            </div>
        </div>
    </div>
</div>
