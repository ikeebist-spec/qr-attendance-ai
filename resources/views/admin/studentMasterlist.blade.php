<!-- 5. STUDENT MASTERLIST -->
<div id="tab-students" class="tab-content space-y-6">
    <!-- Add Student Section -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                <i data-lucide="user-plus" class="mr-2 text-green-600 w-5 h-5"></i> Add New Student
            </h3>
            <button id="toggle-ai-camera" class="flex items-center space-x-2 bg-indigo-50 text-indigo-700 px-4 py-2 rounded-lg font-medium hover:bg-indigo-100 transition-colors">
                <i data-lucide="camera" class="w-[18px] h-[18px]"></i>
                <span>AI Masterlist Scanner</span>
            </button>
        </div>

        <!-- AI Scanner UI -->
        <div id="ai-camera-container" class="hidden mb-6 p-6 bg-indigo-900 rounded-xl text-white">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-lg flex items-center"><i data-lucide="brain" class="mr-2"></i> AI Masterlist Scanner</h4>
                <button id="close-ai-camera" class="text-indigo-300 hover:text-white"><i data-lucide="x-circle" class="w-5 h-5"></i></button>
            </div>

            <!-- Tabs: Camera | Upload | Paste Text -->
            <div class="flex space-x-2 mb-4 flex-wrap gap-y-2">
                <button id="scanner-tab-camera" class="scanner-tab-btn flex items-center space-x-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white transition-colors">
                    <i data-lucide="camera" class="w-4 h-4"></i><span>Live Camera</span>
                </button>
                <button id="scanner-tab-upload" class="scanner-tab-btn flex items-center space-x-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-800/50 text-indigo-300 hover:bg-indigo-700 transition-colors">
                    <i data-lucide="upload" class="w-4 h-4"></i><span>Upload Photo</span>
                </button>
                <button id="scanner-tab-paste" class="scanner-tab-btn flex items-center space-x-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-800/50 text-indigo-300 hover:bg-indigo-700 transition-colors">
                    <i data-lucide="clipboard-paste" class="w-4 h-4"></i><span>Paste Text</span>
                </button>
            </div>

            <!-- CAMERA PANEL -->
            <div id="scanner-panel-camera">
                <div class="relative bg-black rounded-lg overflow-hidden flex flex-col items-center justify-center border-2 border-indigo-500/50 h-[260px]">
                    <div id="ai-scanning-state" class="hidden flex-col items-center text-indigo-300 absolute z-20">
                        <i data-lucide="loader-2" class="w-12 h-12 animate-spin mb-4 text-indigo-400"></i>
                        <p class="font-medium animate-pulse">AI is reading the document...</p>
                        <p class="text-xs mt-2 opacity-75">Extracting names and student numbers</p>
                    </div>
                    <div id="ai-camera-placeholder" class="absolute inset-0 w-full h-full bg-indigo-950 flex flex-col items-center justify-center z-0 hidden">
                        <i data-lucide="image" class="w-16 h-16 text-indigo-800 mb-4"></i>
                        <p class="text-indigo-400 text-sm font-medium">Camera Disabled</p>
                    </div>
                    <video id="ai-video" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover opacity-80 z-10"></video>
                    <div class="absolute inset-0 border-4 border-white/20 m-8 rounded border-dashed pointer-events-none z-10"></div>
                </div>
                <div class="mt-4 flex justify-center" id="ai-capture-btn-container">
                    <button id="ai-extract-btn" class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-full font-bold flex items-center shadow-lg transition-transform hover:scale-105">
                        <i data-lucide="camera" class="mr-2 w-5 h-5"></i> Capture & Extract Data
                    </button>
                </div>
            </div>

            <!-- UPLOAD PANEL -->
            <div id="scanner-panel-upload" class="hidden">
                <!-- Drop Zone -->
                <!-- Drop zone (hidden once photo chosen) -->
                <label for="ai-upload-input" id="ai-upload-zone"
                    class="flex flex-col items-center justify-center h-40 border-2 border-dashed border-indigo-500/60 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-800/30 transition-all group">
                    <i data-lucide="image-plus" class="w-10 h-10 text-indigo-400 mb-2 group-hover:text-indigo-300"></i>
                    <p class="text-indigo-200 font-semibold text-sm">Click to choose a photo</p>
                    <p class="text-indigo-400 text-xs mt-1">JPG, PNG, WEBP — class list or enrollment form</p>
                    <input type="file" id="ai-upload-input" accept="image/*" class="hidden" />
                </label>

                <!-- BIG photo preview (shown after upload) -->
                <div id="ai-upload-preview-container" class="hidden mt-3">
                    <!-- Image — big and full width -->
                    <div class="relative rounded-xl overflow-hidden border-2 border-indigo-500/60 bg-black">
                        <img id="ai-upload-preview" class="w-full max-h-[380px] object-contain" />
                        <!-- change photo shortcut -->
                        <label for="ai-upload-input"
                            class="absolute top-2 right-2 bg-black/60 hover:bg-black/80 text-white text-xs px-3 py-1.5 rounded-full cursor-pointer flex items-center space-x-1 transition-colors">
                            <i data-lucide="refresh-cw" class="w-3 h-3"></i><span>Change</span>
                        </label>
                    </div>

                    <!-- Right-click instruction -->
                    <p class="text-indigo-300 text-xs text-center mt-2 leading-relaxed">
                        👆 <strong class="text-white">Right-click the image above</strong> → <em>"Copy text from image"</em>
                        — then paste the copied text below.
                    </p>

                    <!-- ── PASTE + SECTION + SAVE (all-in-one, appears after photo loads) ── -->
                    <div class="mt-4 bg-indigo-800/40 rounded-xl p-4 border border-indigo-600/30">
                        <p class="text-indigo-200 text-xs font-semibold mb-2">📋 Paste the copied text here:</p>
                        <textarea id="upload-paste-input" rows="5"
                            placeholder="Paste text from Google Lens here...&#10;&#10;2024-001  Dela Cruz, Juan&#10;2024-002  Santos, Maria"
                            class="w-full bg-indigo-950/60 border border-indigo-600 rounded-lg px-3 py-2 text-white text-xs font-mono outline-none focus:border-indigo-400 resize-none placeholder-indigo-500/60 leading-relaxed"></textarea>

                        <!-- Extract button -->
                        <button id="upload-paste-parse-btn"
                            class="mt-2 w-full bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-lg font-bold text-sm flex items-center justify-center space-x-2 transition-colors">
                            <i data-lucide="list-checks" class="w-4 h-4"></i><span>Extract Students</span>
                        </button>

                        <!-- Result rows + section picker + save (hidden until parsed) -->
                        <div id="upload-paste-result" class="hidden mt-3">
                            <p class="text-indigo-200 text-xs font-semibold mb-2">
                                <span id="upload-paste-count"></span> students found — review &amp; choose section:
                            </p>
                            <div id="upload-paste-list" class="space-y-1.5 max-h-40 overflow-y-auto pr-1 mb-3"></div>
                            <div class="flex items-center space-x-2 mb-3">
                                <label class="text-xs text-indigo-300 font-medium whitespace-nowrap">Section:</label>
                                <select id="upload-paste-section"
                                    class="flex-1 bg-indigo-950/60 border border-indigo-600 rounded-lg px-3 py-2 text-white text-sm outline-none focus:border-indigo-400">
                                </select>
                            </div>
                            <button id="upload-paste-save-btn"
                                class="w-full bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl font-bold text-sm flex items-center justify-center space-x-2 transition-colors">
                                <i data-lucide="save" class="w-4 h-4"></i><span>Save All to Masterlist</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Old AI scan result (AI Gemini path — kept hidden but functional) -->
                <div id="ai-upload-scanning-state" class="hidden mt-4 flex flex-col items-center text-indigo-300 py-4">
                    <i data-lucide="loader-2" class="w-10 h-10 animate-spin mb-3 text-indigo-400"></i>
                    <p class="font-medium animate-pulse">AI is reading the document...</p>
                </div>
                <div id="ai-upload-result" class="hidden"></div>
                <input type="hidden" id="ai-upload-scan-btn" />

            </div>

            <!-- PASTE TEXT PANEL -->
            <div id="scanner-panel-paste" class="hidden">
                <p class="text-indigo-300 text-xs mb-3">
                    📋 Scan your class list with <strong>Google Lens</strong>, copy the text, then paste it below.
                </p>
                <textarea id="paste-text-input" rows="6"
                    placeholder="Paste the copied text here...&#10;&#10;Example:&#10;2024-001  Dela Cruz, Juan A.&#10;2024-002  Santos, Maria B."
                    class="w-full bg-indigo-950/60 border border-indigo-600 rounded-xl px-4 py-3 text-white text-sm outline-none focus:border-indigo-400 resize-none placeholder-indigo-500/60 font-mono leading-relaxed"></textarea>
                <div class="mt-3 flex justify-center">
                    <button id="paste-parse-btn"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-2.5 rounded-full font-bold text-sm flex items-center space-x-2 transition-colors">
                        <i data-lucide="list-checks" class="w-4 h-4"></i><span>Extract Students from Text</span>
                    </button>
                </div>
                <!-- Parsed result -->
                <div id="paste-result" class="hidden mt-4 bg-indigo-800/40 rounded-xl p-4 border border-indigo-600/30">
                    <p class="text-sm font-semibold text-indigo-200 mb-3">
                        <span id="paste-extracted-count"></span> students detected — choose their section:
                    </p>
                    <div class="flex items-center space-x-2 mb-3">
                        <label class="text-xs text-indigo-300 font-medium whitespace-nowrap">Section:</label>
                        <select id="paste-section-select"
                            class="flex-1 bg-indigo-950/60 border border-indigo-600 rounded-lg px-3 py-2 text-white text-sm outline-none focus:border-indigo-400">
                        </select>
                    </div>
                    <div id="paste-extracted-list" class="space-y-2 max-h-44 overflow-y-auto pr-1 mb-4"></div>
                    <button id="paste-save-btn"
                        class="w-full text-sm bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl font-bold flex items-center justify-center space-x-2 transition-colors">
                        <i data-lucide="save" class="w-4 h-4"></i><span>Save All to Masterlist</span>
                    </button>
                </div>
            </div>

        </div>


        <form id="add-student-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input required id="student-id" placeholder="Student ID (e.g. 2024-001)" class="border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" />
            <input required id="student-name" placeholder="Full Name" class="border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none md:col-span-2" />
            <select id="student-section" class="border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none">
                <!-- Populated via JS -->
            </select>
            <button type="submit" class="bg-green-700 text-white font-bold rounded hover:bg-green-800 transition-colors">Add & Gen QR</button>
        </form>

        <div id="generated-qr-container" class="hidden mt-6 p-6 bg-green-50 border border-green-200 rounded-xl flex items-center space-x-6">
            <img id="generated-qr-img" src="" alt="QR Code" class="w-32 h-32 border-4 border-white shadow-sm rounded-lg" />
            <div>
                <h4 class="font-bold text-green-900 text-lg">QR Generated Successfully!</h4>
                <p class="text-green-700 text-sm mt-1">Student: <strong id="generated-qr-name"></strong> (<span id="generated-qr-id"></span>)</p>
                <button id="dismiss-qr-btn" class="mt-3 text-sm bg-white text-green-700 px-4 py-1.5 rounded border border-green-300 hover:bg-green-100">Dismiss</button>
            </div>
        </div>
    </div>

    <!-- Masterlist Viewer -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 flex items-center">
                <i data-lucide="users" class="mr-2 text-blue-600 w-5 h-5"></i> Masterlist
            </h3>
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-4 h-4 text-gray-400"></i>
                <select id="student-filter" class="border-gray-200 border rounded text-sm p-1 outline-none">
                    <option value="All">All Sections</option>
                    <!-- Populated via JS -->
                </select>
            </div>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-4 font-medium">ID</th>
                    <th class="px-4 py-4 font-medium">Name</th>
                    <th class="px-4 py-4 font-medium">Section</th>
                    <th class="px-4 py-4 font-medium text-center">Absences</th>
                    <th class="px-4 py-4 font-medium text-center">QR Code</th>
                    <th class="px-4 py-4 font-medium text-center">Action</th>
                </tr>
            </thead>
            <tbody id="students-table-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>
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
        <img id="qr-modal-img" src="" alt="QR Code"
            class="w-48 h-48 border-4 border-green-100 rounded-xl shadow-md" />
        <p class="text-xs text-gray-400 mt-4 text-center">Scan on attendance event</p>
    </div>
</div>

