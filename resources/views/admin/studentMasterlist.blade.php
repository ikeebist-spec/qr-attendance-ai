<!-- 5. STUDENT MASTERLIST -->
<div id="tab-students" class="tab-content space-y-6">
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@v5.0.3/dist/tesseract.min.js"></script>
    <!-- Add Student Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i data-lucide="user-plus" class="mr-2 text-purple-600 w-5 h-5"></i> Add New Student
            </h3>
            <button id="toggle-ai-camera"
                class="flex items-center space-x-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition-colors">
                <i data-lucide="zap" class="w-[18px] h-[18px]"></i>
                <span>Fast Image Enrollment</span>
            </button>
        </div>

        <!-- Enrollment Scanner UI (NON-AI) -->
        <div id="ai-camera-container" class="hidden mb-6 p-6 bg-purple-900 rounded-xl text-white shadow-2xl">
            <div class="flex justify-between items-center mb-5 border-b border-purple-800 pb-3">
                <h4 class="font-black text-lg flex items-center uppercase tracking-wider">
                    <i data-lucide="zap" class="mr-2 text-yellow-400 fill-yellow-400"></i> Fast Image Enrollment
                </h4>
                <button id="close-ai-camera" class="text-blue-200 hover:text-white transition-colors">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Tabs: Upload | Paste Text -->
            <div class="flex space-x-2 mb-6">
                <button id="scanner-tab-upload"
                    class="scanner-tab-btn flex items-center space-x-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-purple-600 text-white shadow-lg transition-all">
                    <i data-lucide="upload" class="w-4 h-4"></i><span>Upload Photo</span>
                </button>
                <button id="scanner-tab-paste"
                    class="scanner-tab-btn flex items-center space-x-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-purple-800/50 text-blue-200 hover:bg-purple-700 transition-all">
                    <i data-lucide="clipboard-paste" class="w-4 h-4"></i><span>Paste Text</span>
                </button>
            </div>

            <!-- UPLOAD PANEL (Now Guided OCR) -->
            <div id="scanner-panel-upload">
                <!-- Drop Zone -->
                <label for="ai-upload-input" id="ai-upload-zone"
                    class="flex flex-col items-center justify-center h-48 border-2 border-dashed border-purple-500/40 rounded-2xl cursor-pointer hover:border-blue-400 hover:bg-purple-800/40 transition-all group">
                    <i data-lucide="image-plus"
                        class="w-12 h-12 text-purple-400 mb-3 group-hover:scale-110 transition-transform"></i>
                    <p class="text-blue-100 font-black text-sm uppercase">Click to upload class list photo</p>
                    <p class="text-blue-300 text-xs mt-2 font-medium">JPG, PNG — Free extraction, no quota limits!</p>
                    <input type="file" id="ai-upload-input" accept="image/*" class="hidden" />
                </label>

                <!-- BIG photo preview + OCR Guide -->
                <div id="ai-upload-preview-container" class="hidden mt-4 space-y-5">
                    <div class="bg-purple-950 p-2 rounded-2xl border border-purple-500/30 overflow-hidden relative">
                        <img id="ai-upload-preview"
                            class="w-full max-h-[400px] object-contain rounded-xl shadow-inner border border-white/5" />
                        <label for="ai-upload-input"
                            class="absolute top-4 right-4 bg-black/70 hover:bg-black text-white text-[10px] font-black px-4 py-2 rounded-full cursor-pointer flex items-center shadow-lg transition-all border border-white/10 uppercase">
                            <i data-lucide="refresh-cw" class="w-3 h-3 mr-2"></i>Change Photo
                        </label>
                    </div>

                    <!-- STEP-BY-STEP FLOW -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Step 1: Trigger OCR -->
                        <div
                            class="bg-purple-800/20 p-6 rounded-2xl border border-blue-500/30 shadow-xl flex flex-col items-center text-center">
                            <p
                                class="text-[10px] font-black text-blue-300 mb-6 uppercase tracking-widest self-start flex items-center">
                                <span
                                    class="bg-blue-500 text-white w-4 h-4 rounded-full flex items-center justify-center mr-2 font-black">1</span>
                                Data Extraction
                            </p>

                            <div id="ocr-trigger-area" class="w-full space-y-4">
                                <!-- Text Actions Bar (Choice Bar) -->
                                <div
                                    class="bg-white/10 backdrop-blur-md rounded-xl p-1.5 flex items-center space-x-2 border border-white/10 shadow-lg mb-2">
                                    <div
                                        class="px-3 py-1.5 text-[10px] font-black text-blue-300 border-r border-white/10 uppercase tracking-tighter">
                                        Text Actions
                                    </div>
                                    <button id="copy-all-text-btn"
                                        class="flex-1 flex items-center justify-center space-x-2 py-2 px-3 rounded-lg hover:bg-white/10 text-[10px] font-bold text-white transition-all active:scale-95">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                        <span>Copy all text</span>
                                    </button>
                                    <button id="extract-students-btn"
                                        class="flex-1 flex items-center justify-center space-x-2 py-2 px-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-[10px] font-black text-white transition-all active:scale-95 shadow-lg shadow-blue-900/40">
                                        <i data-lucide="table" class="w-3.5 h-3.5"></i>
                                        <span>Extract Students</span>
                                    </button>
                                </div>

                                <p
                                    class="text-[9px] text-blue-200 opacity-60 font-bold uppercase tracking-widest text-center">
                                    Choose an action above to process the photo
                                </p>

                                <!-- PROGRESS INDICATOR (Used by both actions) -->
                                <div id="ocr-loader-container" class="hidden w-full pt-4 space-y-3">
                                    <div class="flex justify-between items-end">
                                        <div class="flex flex-col">
                                            <span id="ocr-status-msg"
                                                class="text-[9px] font-black text-blue-400 uppercase tracking-tighter animate-pulse">Initializing
                                                Engine...</span>
                                            <span class="text-[11px] font-black text-white uppercase">Processing
                                                Image</span>
                                        </div>
                                        <span id="ocr-percent"
                                            class="text-lg font-black text-blue-400 tracking-tighter">0%</span>
                                    </div>
                                    <div
                                        class="h-2.5 bg-black/40 rounded-full overflow-hidden border border-purple-800/50 p-0.5">
                                        <div id="ocr-bar"
                                            class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 bg-[length:200%_auto] animate-gradient transition-all duration-300 rounded-full"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Verification -->
                        <div id="ocr-result-panel"
                            class="hidden bg-purple-800/20 p-6 rounded-2xl border border-green-500/30 shadow-xl flex flex-col">
                            <p
                                class="text-[10px] font-black text-green-300 mb-6 uppercase tracking-widest flex items-center">
                                <span
                                    class="bg-green-500 text-white w-4 h-4 rounded-full flex items-center justify-center mr-2 font-black">2</span>
                                Enroll Students
                            </p>

                            <div id="ocr-students-list"
                                class="space-y-2 max-h-48 overflow-y-auto mb-6 pr-1 scrollbar-thin"></div>

                            <div class="mt-auto pt-6 border-t border-purple-800/50 space-y-4">
                                <div class="flex items-center space-x-3">
                                    <span
                                        class="text-[10px] font-black text-blue-300 uppercase tracking-widest">Section:</span>
                                    <div class="flex-1 relative">
                                        <input id="ocr-section-sel" list="ocr-sections-list"
                                            placeholder="Select or type section..."
                                            class="w-full bg-purple-950 border border-purple-800 rounded-xl px-3 py-2.5 text-white text-xs font-bold outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-inner">
                                        <datalist id="ocr-sections-list">
                                            <!-- Populated via JS -->
                                        </datalist>
                                    </div>
                                </div>
                                <button id="ocr-enroll-btn"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-tighter shadow-[0_10px_30px_rgba(22,163,74,0.3)] active:scale-95 transition-all flex items-center justify-center">
                                    <i data-lucide="user-check" class="w-5 h-5 mr-2"></i>
                                    ENROLL <span id="ocr-count" class="mx-1">0</span> STUDENTS
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASTE TEXT PANEL -->
            <div id="scanner-panel-paste" class="hidden">
                <p class="text-blue-300 text-xs mb-3">
                    📋 Scan your class list with <strong>Google Lens</strong>, copy the text, then paste it below.
                </p>
                <textarea id="paste-text-input" rows="6"
                    placeholder="Paste the copied text here...&#10;&#10;Example:&#10;2024-001  Dela Cruz, Juan A.&#10;2024-002  Santos, Maria B."
                    class="w-full bg-purple-950/60 border border-purple-600 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-purple-400 resize-none placeholder-blue-300/50 font-mono leading-relaxed"></textarea>
                <div class="mt-3 flex justify-center">
                    <button id="paste-parse-btn"
                        class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2.5 rounded-full font-bold text-sm flex items-center space-x-2 transition-colors">
                        <i data-lucide="list-checks" class="w-4 h-4"></i><span>Extract Students from Text</span>
                    </button>
                </div>
                <!-- Parsed result -->
                <div id="paste-result" class="hidden mt-4 bg-purple-800/40 rounded-xl p-4 border border-purple-600/30">
                    <p class="text-sm font-semibold text-blue-200 mb-3">
                        <span id="paste-extracted-count"></span> students detected — choose their year and section:
                    </p>
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="text-xs text-blue-300 font-medium whitespace-nowrap">Year and Section:</label>
                        <select id="paste-year-and-section-select"
                            class="flex-1 bg-purple-950/60 border border-purple-600 rounded-lg px-3 py-2 text-white text-sm outline-none focus:border-purple-400">
                        </select>
                    </div>
                    <div id="paste-extracted-list" class="space-y-2 max-h-44 overflow-y-auto pr-1 mb-4"></div>
                    <button id="paste-save-btn"
                        class="w-full text-sm bg-purple-500 hover:bg-purple-600 text-white py-2.5 rounded-xl font-bold flex items-center justify-center space-x-2 transition-colors">
                        <i data-lucide="save" class="w-4 h-4"></i><span>Save All to Masterlist</span>
                    </button>
                </div>
            </div>

        </div>


        <form id="add-student-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input required id="student-id" placeholder="Student ID (e.g. 2024-001)"
                class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none" />
            <input required id="student-name" placeholder="Full Name"
                class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none md:col-span-2" />
            <select id="student-year-and-section"
                class="border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none">
                <!-- Populated via JS -->
            </select>
            <button type="submit"
                class="bg-purple-700 text-white font-bold rounded hover:bg-purple-800 transition-colors">Add & Gen
                QR</button>
        </form>

        <div id="generated-qr-container"
            class="hidden mt-6 p-6 bg-purple-50 border border-purple-200 rounded-xl flex items-center space-x-6">
            <img id="generated-qr-img" src="" alt="QR Code"
                class="w-32 h-32 border-4 border-white shadow-sm rounded-lg" />
            <div>
                <h4 class="font-bold text-purple-900 text-lg">QR Generated Successfully!</h4>
                <p class="text-purple-700 text-sm mt-1">Student: <strong id="generated-qr-name"></strong> (<span
                        id="generated-qr-id"></span>)</p>
                <button id="dismiss-qr-btn"
                    class="mt-3 text-sm bg-white text-purple-700 px-4 py-1.5 rounded border border-purple-300 hover:bg-purple-100">Dismiss</button>
            </div>
        </div>
    </div>

    <!-- Masterlist Viewer -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div
            class="px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50 gap-4">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i data-lucide="users" class="mr-2 text-blue-600 w-5 h-5"></i> Student Masterlist
            </h3>
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-4 h-4 text-gray-400"></i>
                <select id="student-filter"
                    class="border-gray-200 border rounded-lg text-sm p-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                    <option value="All">All Year and Sections</option>
                    <!-- Populated via JS -->
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse border border-gray-200">
                <thead class="bg-gray-100 text-gray-600 border-b-2 border-gray-300">
                    <tr>
                        <th class="px-4 py-3 font-bold border border-gray-300 w-32">Student ID</th>
                        <th class="px-4 py-3 font-bold border border-gray-300">Full Name</th>
                        <th class="px-4 py-3 font-bold border border-gray-300 w-40 text-center">Year & Section</th>
                        <th class="px-4 py-3 font-bold border border-gray-300 w-24 text-center">Absences</th>
                        <th class="px-4 py-3 font-bold border border-gray-300 w-28 text-center">Credentials</th>
                        <th class="px-4 py-3 font-bold border border-gray-300 w-24 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="students-table-body" class="divide-y divide-gray-200">
                    <!-- Rendered via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qr-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="qr-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center max-w-xs w-full z-10">
        <button id="qr-modal-close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
        <h3 class="text-lg font-bold text-gray-800 mb-1" id="qr-modal-name"></h3>
        <p class="text-xs text-gray-500 mb-4" id="qr-modal-id"></p>
        <img id="qr-modal-img" src="" alt="QR Code" class="w-48 h-48 border-4 border-purple-100 rounded-xl shadow-md" />
        <p class="text-xs text-blue-400 mt-4 text-center">Scan on attendance event</p>
    </div>
</div>