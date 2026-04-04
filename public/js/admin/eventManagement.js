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
        let timeInfo = '';
        if (ev.is_single_scan) {
            timeInfo = `<span class="text-[10px] uppercase font-bold text-green-600">Single: ${ev.start_time || '??'} - ${ev.end_time || '??'}</span>`;
        } else {
            const morning = (ev.morn_in_start || ev.morn_out_end) ? `${ev.morn_in_start || '??'} - ${ev.morn_out_end || '??'}` : 'None';
            const afternoon = (ev.aft_in_start || ev.aft_out_end) ? `${ev.aft_in_start || '??'} - ${ev.aft_out_end || '??'}` : 'None';
            timeInfo = `
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] uppercase font-bold text-blue-500">AM: ${morning}</span>
                    <span class="text-[10px] uppercase font-bold text-purple-500">PM: ${afternoon}</span>
                </div>
            `;
        }
        
        return `
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors text-xs">
                <td class="px-6 py-4 font-medium text-gray-400">${ev.id}</td>
                <td class="px-6 py-4">
                    <div class="flex flex-col">
                        <span class="text-gray-800 font-bold">${window.escapeHTML(ev.name)}</span>
                        <span class="text-[9px] uppercase font-black tracking-tighter ${ev.is_single_scan ? 'text-green-500' : 'text-purple-500'}">${ev.is_single_scan ? 'Single Scan' : 'Multi-Session'}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600 font-semibold">${window.escapeHTML(ev.month || 'N/A')}</td>
                <td class="px-6 py-4 text-gray-600">${window.escapeHTML(ev.date)}</td>
                <td class="px-6 py-4">${timeInfo}</td>
                <td class="px-6 py-4 text-gray-800 font-bold">₱${ev.fine || 50}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-wider ${ev.type === 'Mandatory' ? 'bg-red-100 text-red-700 border border-red-200' : ev.type === 'Major' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-700 border border-gray-200'}">${window.escapeHTML(ev.type)}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="window.deleteEvent(${ev.id})" class="text-red-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition-all" title="Delete Event">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    if (window.lucide) window.lucide.createIcons();
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
    // Mode Switching Logic
    const btnSingle = document.getElementById('btn-mode-single');
    const btnMulti = document.getElementById('btn-mode-multi');
    const secSingle = document.getElementById('section-single-scan');
    const secMulti = document.getElementById('section-multi-session');
    const inputHidden = document.getElementById('event-is-single-scan');

    if (btnSingle && btnMulti) {
        btnSingle.addEventListener('click', () => {
            btnSingle.className = "flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all bg-purple-600 text-white shadow-sm";
            btnMulti.className = "flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-gray-500 hover:bg-gray-100";
            secSingle.classList.remove('hidden');
            secMulti.classList.add('hidden');
            inputHidden.value = "1";
        });

        btnMulti.addEventListener('click', () => {
            btnMulti.className = "flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all bg-purple-600 text-white shadow-sm";
            btnSingle.className = "flex-1 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-gray-500 hover:bg-gray-100";
            secSingle.classList.add('hidden');
            secMulti.classList.remove('hidden');
            inputHidden.value = "0";
        });
    }

    const sel = document.getElementById('event-selector');
    if (sel) {
        sel.addEventListener('change', (e) => {
            window.selectedEventId = Number(e.target.value);
            if (window.activeTab === 'scan') {
                const evShow = document.getElementById('scan-event-name');
                if (evShow) evShow.innerText = window.events.find(ev => ev.id === window.selectedEventId)?.name;
                if (window.updateScannerUI) window.updateScannerUI();
            }
            if (window.renderDashboard) window.renderDashboard();
            if (window.renderRecords) window.renderRecords();
        });
    }

    const form = document.getElementById('add-event-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log('Form submission started');
            
            try {
                const nameControl = document.getElementById('event-name');
                const dateControl = document.getElementById('event-date');
                const monthControl = document.getElementById('event-month');
                const typeControl = document.getElementById('event-type');
                const fineControl = document.getElementById('event-fine');
                const isSingleControl = document.getElementById('event-is-single-scan');

                if (!nameControl || !dateControl || !monthControl || !typeControl || !fineControl || !isSingleControl) {
                    throw new Error('Some form controls are missing from the page.');
                }

                const name = nameControl.value;
                const date = dateControl.value;
                const month = monthControl.value;
                const type = typeControl.value;
                const fine = Number(fineControl.value) || 50;
                const isSingle = isSingleControl.value === "1";

                const payload = {
                    name, date, month, type, fine,
                    is_single_scan: isSingle,
                    start_time: isSingle ? (document.getElementById('event-start-time')?.value || null) : null,
                    end_time: isSingle ? (document.getElementById('event-end-time')?.value || null) : null,
                    morn_in_start: !isSingle ? (document.getElementById('morn-in-start')?.value || null) : null,
                    morn_in_end: !isSingle ? (document.getElementById('morn-in-end')?.value || null) : null,
                    morn_out_start: !isSingle ? (document.getElementById('morn-out-start')?.value || null) : null,
                    morn_out_end: !isSingle ? (document.getElementById('morn-out-end')?.value || null) : null,
                    aft_in_start: !isSingle ? (document.getElementById('aft-in-start')?.value || null) : null,
                    aft_in_end: !isSingle ? (document.getElementById('aft-in-end')?.value || null) : null,
                    aft_out_start: !isSingle ? (document.getElementById('aft-out-start')?.value || null) : null,
                    aft_out_end: !isSingle ? (document.getElementById('aft-out-end')?.value || null) : null,
                };

                console.log('Sending payload:', payload);
                const res = await window.apiPost('/api/events', payload);
                
                if (res.ok) {
                    const newEvent = await res.json();
                    window.events.push(newEvent);
                    if (window.logActivity) window.logActivity(`Added new event: ${name}`);
                    window.showToast(`Successfully added event: ${name}`);
                    window.renderEvents();
                    window.renderEventSelector();
                    form.reset();
                    if (btnSingle) btnSingle.click();
                } else {
                    const errData = await res.json();
                    window.showToast(errData.error || 'Failed to add event: ' + (JSON.stringify(errData.errors) || ''), 'error');
                }
            } catch (err) {
                console.error('Submission error:', err);
                alert('An error occurred during submission: ' + err.message);
            }
        });
    }
});
