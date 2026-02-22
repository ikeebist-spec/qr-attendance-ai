window.renderEventSelector = function () {
    const selector = document.getElementById('event-selector');
    if (!selector) return;
    selector.innerHTML = window.events.map(ev =>
        `<option value="${ev.id}" ${ev.id === window.selectedEventId ? 'selected' : ''}>${window.escapeHTML(ev.name)}</option>`
    ).join('');
};

window.renderEvents = function () {
    const tbody = document.getElementById('events-table-body');
    if (!tbody) return;
    tbody.innerHTML = window.events.map(ev => `
        <tr class="border-b border-gray-50 hover:bg-gray-50">
            <td class="px-6 py-3 font-medium text-gray-900">${ev.id}</td>
            <td class="px-6 py-3 text-gray-700 font-bold">${window.escapeHTML(ev.name)}</td>
            <td class="px-6 py-3 text-gray-600">${window.escapeHTML(ev.date)}</td>
            <td class="px-6 py-3 text-gray-800 font-bold">₱${ev.fine || 50}</td>
            <td class="px-6 py-3">
                <span class="px-2 py-1 rounded text-xs font-bold ${ev.type === 'Mandatory' ? 'bg-red-100 text-red-700' : ev.type === 'Major' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'}">${window.escapeHTML(ev.type)}</span>
            </td>
        </tr>
    `).join('');
};

document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('event-selector');
    if (sel) {
        sel.addEventListener('change', (e) => {
            window.selectedEventId = Number(e.target.value);
            if (window.activeTab === 'scan') document.getElementById('scan-event-name').innerText = window.events.find(ev => ev.id === window.selectedEventId)?.name;
            if (window.renderDashboard) window.renderDashboard();
            if (window.renderRecords) window.renderRecords();
        });
    }

    const form = document.getElementById('add-event-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('event-name').value;
            const date = document.getElementById('event-date').value;
            const type = document.getElementById('event-type').value;
            const fine = Number(document.getElementById('event-fine').value) || 50;

            const res = await window.apiPost('/api/events', { name, date, type, fine });
            if (res.ok) {
                const newEvent = await res.json();
                window.events.push(newEvent);
                if (window.logActivity) window.logActivity(`Added new event: ${name}`);
                window.showToast(`Successfully added event: ${name}`);
                window.renderEvents();
                window.renderEventSelector();
                e.target.reset();
            } else {
                window.showToast('Failed to add event.', 'error');
            }
        });
    }
});
