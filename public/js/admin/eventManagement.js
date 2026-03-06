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
    tbody.innerHTML = window.events.map(ev => {
        const start = ev.start_time ? new Date(ev.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
        const end = ev.end_time ? new Date(ev.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'N/A';
        return `
            <tr class="border-b border-gray-50 hover:bg-gray-50">
                <td class="px-6 py-3 font-medium text-gray-900">${ev.id}</td>
                <td class="px-6 py-3 text-gray-700 font-bold">${window.escapeHTML(ev.name)}</td>
                <td class="px-6 py-3 text-gray-600 font-semibold">${window.escapeHTML(ev.month || 'N/A')}</td>
                <td class="px-6 py-3 text-gray-600">${window.escapeHTML(ev.date)}</td>
                <td class="px-6 py-3 text-gray-600 font-mono text-xs">${start} - ${end}</td>
                <td class="px-6 py-3 text-gray-800 font-bold">₱${ev.fine || 50}</td>
                <td class="px-6 py-3">
                    <span class="px-2 py-1 rounded text-xs font-bold ${ev.type === 'Mandatory' ? 'bg-red-100 text-red-700' : ev.type === 'Major' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'}">${window.escapeHTML(ev.type)}</span>
                </td>
                <td class="px-6 py-3 text-center">
                    <button onclick="window.deleteEvent(${ev.id})" class="text-red-500 hover:text-red-700 p-1 rounded transition-colors" title="Delete Event">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
};

window.deleteEvent = async function (id) {
    if (!confirm('Are you sure you want to delete this event? This will also delete any attendance records associated with it and affect fine computations!')) return;

    try {
        const res = await window.apiDelete(`/api/events/${id}`);
        if (res.ok) {
            window.events = window.events.filter(e => e.id !== id);
            if (window.selectedEventId === id) {
                window.selectedEventId = window.events.length > 0 ? window.events[window.events.length - 1].id : null;
            }
            window.showToast('Event deleted successfully');
            if (window.logActivity) window.logActivity(`Deleted event ID: ${id}`);
            window.renderEvents();
            window.renderEventSelector();
            if (window.renderDashboard) window.renderDashboard();
            if (window.renderRecords) window.renderRecords();
            if (window.renderFines) window.renderFines();
        } else {
            const data = await res.json();
            window.showToast(data.error || 'Failed to delete event', 'error');
        }
    } catch (e) {
        console.error(e);
        window.showToast('Error connecting to the server', 'error');
    }
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
            const month = document.getElementById('event-month').value;
            const startTime = document.getElementById('event-start-time').value;
            const duration = Number(document.getElementById('event-duration').value);
            const type = document.getElementById('event-type').value;
            const fine = Number(document.getElementById('event-fine').value) || 50;

            const res = await window.apiPost('/api/events', {
                name, date, month, type, fine,
                start_time: startTime,
                duration
            });
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
