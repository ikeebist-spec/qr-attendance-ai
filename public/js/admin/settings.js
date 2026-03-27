window.renderYearAndSections = function () {
    const countEl = document.getElementById('year-and-sections-count');
    const badgesEl = document.getElementById('year-and-sections-badges');
    const secSelect = document.getElementById('student-year-and-section');
    const filterSel = document.getElementById('student-filter');

    if (!Array.isArray(window.yearAndSections)) window.yearAndSections = [];

    if (countEl) countEl.innerText = window.yearAndSections.length;

    if (badgesEl) {
        badgesEl.innerHTML = window.yearAndSections.map(sec => `
            <span class="px-2 py-1 bg-white border border-gray-200 rounded text-sm font-medium text-gray-700 flex items-center space-x-1">
                <span>${window.escapeHTML(sec)}</span>
                <button class="delete-section-btn text-red-400 hover:text-red-600 focus:outline-none" data-sec="${window.escapeHTML(sec)}">
                    <i data-lucide="x" class="w-3 h-3 pointer-events-none"></i>
                </button>
            </span>
        `).join('');
        lucide.createIcons();
        document.querySelectorAll('.delete-section-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                window.deleteYearAndSectionHandler(e.target.closest('button').dataset.sec);
            });
        });
    }

    if (secSelect) secSelect.innerHTML = window.yearAndSections.map(sec => `<option value="${window.escapeHTML(sec)}">${window.escapeHTML(sec)}</option>`).join('');
    if (filterSel) {
        filterSel.innerHTML = `<option value="All">All Year and Sections</option>` + window.yearAndSections.map(sec => `<option value="${window.escapeHTML(sec)}">Year and Section ${window.escapeHTML(sec)}</option>`).join('');
        filterSel.value = window.yearAndSectionFilter;
    }
};

window.deleteYearAndSectionHandler = async function (sec) {
    const res = await window.apiDelete(`/api/year-and-sections/${sec}`);
    if (res.ok) {
        window.yearAndSections = window.yearAndSections.filter(s => s !== sec);
        if (window.logActivity) window.logActivity(`Deleted academic year and section: ${sec}`);
        window.showToast(`Successfully deleted year and section: ${sec}`);
        window.renderYearAndSections();
    } else {
        const err = await res.json();
        window.showToast(err.error || `Cannot delete ${sec}.`, 'error');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // --- Admin Management ---
    const adminSelector = document.getElementById('admin-account-selector');
    const adminForm = document.getElementById('manage-admin-form');
    const adminUsernameInput = document.getElementById('admin-new-username');
    const adminPasswordInput = document.getElementById('admin-new-password');
    let allAdmins = [];

    const loadAdmins = async () => {
        if (window.USER_ROLE !== 'super_admin') {
            document.getElementById('settings-unauthorized-msg')?.classList.remove('hidden');
            return;
        }

        document.getElementById('super-admin-badge')?.classList.remove('hidden');
        document.getElementById('manage-admins-container')?.classList.remove('hidden');

        try {
            const res = await window.apiGet('/api/admins');
            if (res && Array.isArray(res)) {
                allAdmins = res;
                if (adminSelector) {
                    // Retain selection if exists
                     const currentVal = adminSelector.value;
                    adminSelector.innerHTML = '<option value="">Select an account to modify...</option>' + 
                        allAdmins.map(a => `<option value="${a.id}">${a.name} (${a.role === 'super_admin' ? 'Super Admin' : 'Scanner'})</option>`).join('');
                    if (currentVal) adminSelector.value = currentVal;
                }
            }
        } catch (e) {
            console.error(e);
        }
    };

    if (adminSelector) {
        adminSelector.addEventListener('change', (e) => {
            const adminId = e.target.value;
            const selected = allAdmins.find(a => a.id == adminId);
            if (selected) {
                adminUsernameInput.value = selected.username || selected.email || '';
                adminPasswordInput.value = ''; // Reset password field
            } else {
                adminUsernameInput.value = '';
                adminPasswordInput.value = '';
            }
        });
    }

    if (adminForm) {
        adminForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const adminId = adminSelector.value;
            if (!adminId) {
                window.showToast('Please select an account first.', 'error');
                return;
            }

            const username = adminUsernameInput.value.trim();
            const password = adminPasswordInput.value;
            const btn = document.getElementById('btn-save-admin');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...</span>';
            btn.disabled = true;

            const res = await window.apiPut(`/api/admins/${adminId}`, { username, password: password || null });
            
            btn.innerHTML = originalText;
            btn.disabled = false;

            if (res.ok) {
                const data = await res.json();
                window.showToast(data.message || 'Admin account successfully updated!');
                if (window.logActivity) window.logActivity(`Changed credentials for admin ID: ${adminId}`);
                await loadAdmins(); // Refresh to get updated username
                adminPasswordInput.value = '';
            } else {
                const err = await res.json().catch(() => ({}));
                window.showToast(err.error || err.message || 'Failed to update account!', 'error');
            }
        });

        // Load admins when initialized
        loadAdmins();
    }

    document.getElementById('add-year-and-section-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('new-year-and-section-input').value.trim().toUpperCase();
        if (!name) return;

        const res = await window.apiPost('/api/year-and-sections', { name });
        if (res.ok) {
            window.yearAndSections.push(name);
            window.yearAndSections.sort();
            if (window.logActivity) window.logActivity(`Added academic year and section: ${name}`);
            window.showToast(`Successfully added year and section: ${name}`);
            window.renderYearAndSections();
        } else {
            const err = await res.json().catch(() => ({}));
            window.showToast(err.error || err.message || `Failed to add year and section ${name}!`, 'error');
        }
        e.target.reset();
    });
});
