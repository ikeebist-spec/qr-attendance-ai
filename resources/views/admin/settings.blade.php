<!-- 8. SETTINGS -->
<div id="tab-settings" class="tab-content">
    <div class="max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Manage Admin Accounts</h3>
                <span class="text-[10px] uppercase tracking-wider bg-purple-100 text-purple-700 font-bold px-2 py-1 rounded hidden" id="super-admin-badge">Super Admin</span>
            </div>
            <div class="p-6">
                <p class="text-sm text-yellow-600 bg-yellow-50 p-3 rounded border border-yellow-200 hidden" id="settings-unauthorized-msg">You do not have permission to manage admin accounts. Only the Super Admin can access this panel.</p>
                
                <div id="manage-admins-container" class="hidden">
                    <form id="manage-admin-form" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1 font-semibold">Select Account</label>
                            <select id="admin-account-selector" class="w-full border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none bg-gray-50">
                                <option value="">Loading accounts...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1 font-semibold">Username</label>
                            <input type="text" required id="admin-new-username" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-1 font-semibold">New Password</label>
                            <input type="password" id="admin-new-password" placeholder="Leave blank to keep current password" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none" />
                        </div>
                        <div class="pt-2">
                            <button type="submit" id="btn-save-admin" class="bg-gray-800 text-white px-6 py-2 rounded font-medium hover:bg-gray-900 transition-colors w-full md:w-auto flex justify-center items-center">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-800">Academic Settings</h3>
            </div>
            <div class="p-6">
                <h4 class="text-sm font-bold text-gray-600 mb-4 uppercase tracking-wider flex items-center">Manage Year and Sections</h4>
                <p class="text-sm text-gray-500 mb-4">Add new year and sections (e.g. 2F) to handle incoming enrollees for the next school year.</p>
                
                <form id="add-year-and-section-form" class="flex space-x-2 mb-6">
                    <input required id="new-year-and-section-input" placeholder="e.g. 2F" class="flex-1 border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none" />
                    <button type="submit" class="bg-purple-700 text-white px-4 py-2 rounded font-medium hover:bg-purple-800 transition-colors flex items-center">
                        <i data-lucide="plus" class="w-[18px] h-[18px] mr-1"></i> Add
                    </button>
                </form>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <h5 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Current Active Year and Sections (<span id="year-and-sections-count"></span>)</h5>
                    <div id="year-and-sections-badges" class="flex flex-wrap gap-2 max-h-48 overflow-y-auto">
                        <!-- Rendered via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
