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
    document.getElementById('password-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const p1 = document.getElementById('pwd-new').value;
        const p2 = document.getElementById('pwd-confirm').value;
        if (p1 !== p2) { window.showToast('New passwords do not match!', 'error'); return; }
        if (window.logActivity) window.logActivity('Changed account password');
        window.showToast('Password successfully updated!');
        e.target.reset();
    });

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
