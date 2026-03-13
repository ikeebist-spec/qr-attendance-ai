<!-- 3. SCAN QR -->
<div id="tab-scan" class="tab-content">
    <div class="flex flex-col items-center max-w-2xl mx-auto">
        <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-sm border border-purple-100 w-full text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Live QR Scanner</h3>
            <p class="text-gray-500 mb-4">Click "Start Scanning" to activate your camera.</p>
            
            <div class="mb-6 inline-block bg-purple-50 border border-purple-200 text-purple-800 px-4 py-2 rounded-lg text-sm font-medium">
                Currently Scanning For: <span id="scan-event-name" class="font-bold">...</span>
            </div>
            
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
