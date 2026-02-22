window.renderSections = function () {
    const countEl = document.getElementById('sections-count');
    const badgesEl = document.getElementById('sections-badges');
    const secSelect = document.getElementById('student-section');
    const filterSel = document.getElementById('student-filter');

    if (countEl) countEl.innerText = window.sections.length;

    if (badgesEl) {
        badgesEl.innerHTML = window.sections.map(sec => `
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
                window.deleteSectionHandler(e.target.closest('button').dataset.sec);
            });
        });
    }

    if (secSelect) secSelect.innerHTML = window.sections.map(sec => `<option value="${window.escapeHTML(sec)}">${window.escapeHTML(sec)}</option>`).join('');
    if (filterSel) {
        filterSel.innerHTML = `<option value="All">All Sections</option>` + window.sections.map(sec => `<option value="${window.escapeHTML(sec)}">Section ${window.escapeHTML(sec)}</option>`).join('');
        filterSel.value = window.sectionFilter;
    }
};

window.deleteSectionHandler = async function (sec) {
    const res = await window.apiDelete(`/api/sections/${sec}`);
    if (res.ok) {
        window.sections = window.sections.filter(s => s !== sec);
        if (window.logActivity) window.logActivity(`Deleted academic section: ${sec}`);
        window.showToast(`Successfully deleted section: ${sec}`);
        window.renderSections();
    } else {
        const err = await res.json();
        window.showToast(err.error || `Cannot delete ${sec}.`, 'error');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('password-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const p1 = document.getElementById('pwd-new').value;
        const p2 = document.getElementById('pwd-confirm').value;
        if (p1 !== p2) { window.showToast('New passwords do not match!', 'error'); return; }
        if (window.logActivity) window.logActivity('Changed account password');
        window.showToast('Password successfully updated!');
        e.target.reset();
    });

    document.getElementById('add-section-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('new-section-input').value.trim().toUpperCase();
        if (!name) return;

        const res = await window.apiPost('/api/sections', { name });
        if (res.ok) {
            window.sections.push(name);
            window.sections.sort();
            if (window.logActivity) window.logActivity(`Added academic section: ${name}`);
            window.showToast(`Successfully added section: ${name}`);
            window.renderSections();
        } else {
            window.showToast(`Section ${name} already exists!`, 'error');
        }
        e.target.reset();
    });
});
