window.renderRecords = async function () {
    if (!window.selectedEventId) return;
    const nameEl = document.getElementById('records-event-name');
    const countEl = document.getElementById('records-count');
    const emptyState = document.getElementById('records-empty');
    const table = document.getElementById('records-table');
    const tbody = document.getElementById('records-table-body');
    if (!emptyState || !table || !tbody) return;

    if (nameEl) nameEl.innerText = window.events.find(e => e.id === window.selectedEventId)?.name || '';

    try {
        const res = await fetch(`/api/attendance?event_id=${window.selectedEventId}`);
        const logs = await res.json();
        if (countEl) countEl.innerText = logs.length;

        if (logs.length === 0) {
            emptyState.classList.remove('hidden');
            table.classList.add('hidden');
        } else {
            emptyState.classList.add('hidden');
            table.classList.remove('hidden');
            tbody.innerHTML = logs.map(log => `
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">${log.student_id}</td>
                    <td class="px-6 py-4 text-gray-700">${log.student_name}</td>
                    <td class="px-6 py-4 text-gray-600">${log.section}</td>
                    <td class="px-6 py-4 text-gray-500">${log.scanned_at}</td>
                </tr>
            `).join('');
        }
    } catch (e) { console.error(e); }
};
