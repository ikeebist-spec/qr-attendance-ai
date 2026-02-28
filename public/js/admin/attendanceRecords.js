window.isCompilationMode = false;

window.renderRecords = async function () {
    const emptyState = document.getElementById('records-empty');
    const table = document.getElementById('records-table');
    const tbody = document.getElementById('records-table-body');
    const compContainer = document.getElementById('compilation-container');
    const monthSelector = document.getElementById('records-month-selector');
    const eventInfo = document.getElementById('records-event-info');

    if (!emptyState || !table || !tbody || !compContainer) return;

    // Reset visibility
    emptyState.classList.add('hidden');
    table.classList.add('hidden');
    compContainer.classList.add('hidden');

    if (window.isCompilationMode) {
        if (eventInfo) eventInfo.classList.add('hidden');
        await window.renderMonthlyCompilation();
    } else {
        if (eventInfo) eventInfo.classList.remove('hidden');
        await window.renderSingleEventRecords();
    }
};

window.renderSingleEventRecords = async function () {
    if (!window.selectedEventId) return;
    const nameEl = document.getElementById('records-event-name');
    const countEl = document.getElementById('records-count');
    const emptyState = document.getElementById('records-empty');
    const table = document.getElementById('records-table');
    const tbody = document.getElementById('records-table-body');

    if (nameEl) nameEl.innerText = window.events.find(e => e.id === window.selectedEventId)?.name || '';

    try {
        const res = await fetch(`/api/attendance?event_id=${window.selectedEventId}`);
        const logs = await res.json();

        if (countEl) countEl.innerText = logs.length;

        // Group ALL students by Year and Section
        const grouped = {};
        if (window.students && window.students.length > 0) {
            window.students.forEach(student => {
                const sec = student.year_and_section || 'Unassigned';
                if (!grouped[sec]) grouped[sec] = [];
                grouped[sec].push(student);
            });
        }

        const sections = Object.keys(grouped).sort();

        if (sections.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }

        table.classList.remove('hidden');

        const presentMap = {};
        logs.forEach(log => {
            presentMap[String(log.student_id)] = log;
        });

        let html = '';
        sections.forEach((section, index) => {
            const folderId = `attend-sec-${index}`;
            const students = grouped[section];
            let sectionPresent = students.filter(s => presentMap[String(s.student_id)]).length;

            html += `
                <tr class="bg-purple-50/50 hover:bg-purple-50 border-b border-gray-100 cursor-pointer transition-colors" onclick="window.toggleFolder('${folderId}')">
                    <td colspan="4" class="px-6 py-3 font-semibold text-purple-900 border-l-4 border-purple-500">
                        <div class="flex items-center space-x-2">
                            <svg id="folder-icon-${folderId}" class="w-4 h-4 text-purple-500 transition-transform duration-200" style="transform: rotate(0deg)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            <span>${window.escapeHTML(section)}</span>
                            <span class="bg-purple-100 text-purple-700 text-xs py-0.5 px-2 rounded-full ml-2">${sectionPresent} / ${students.length} Present</span>
                        </div>
                    </td>
                </tr>
            `;

            students.forEach(student => {
                const log = presentMap[String(student.student_id)];
                const statusBadge = log
                    ? `<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider border border-green-200">Present</span>`
                    : `<span class="bg-gray-100 text-gray-400 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider border border-gray-200">Absent</span>`;

                html += `
                    <tr class="folder-row-${folderId} hidden border-b border-gray-50 hover:bg-gray-50 transition-colors bg-white">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 pl-10">${window.escapeHTML(student.student_id)}</td>
                        <td class="px-6 py-4 font-bold text-gray-800">${window.escapeHTML(student.name)}</td>
                        <td class="px-6 py-4 text-gray-500 text-xs">${window.escapeHTML(student.year_and_section)}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                ${statusBadge}
                                <span class="text-[10px] text-gray-400 font-mono">${log ? log.scanned_at : '--:--'}</span>
                            </div>
                        </td>
                    </tr>
                `;
            });
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error("Single event records error:", e);
    }
};

window.renderMonthlyCompilation = async function () {
    const monthSelector = document.getElementById('records-month-selector');
    const selectedMonth = monthSelector.value;
    const compContainer = document.getElementById('compilation-container');
    const thead = document.getElementById('compilation-thead');
    const tbody = document.getElementById('compilation-tbody');
    const countEl = document.getElementById('records-count');

    if (!selectedMonth) {
        document.getElementById('records-empty').classList.remove('hidden');
        return;
    }

    const monthEvents = window.events.filter(e => e.month === selectedMonth).sort((a, b) => new Date(a.date) - new Date(b.date));

    if (monthEvents.length === 0) {
        document.getElementById('records-empty').classList.remove('hidden');
        return;
    }

    compContainer.classList.remove('hidden');

    try {
        const res = await fetch(`/api/attendance?month=${selectedMonth}`);
        const allLogs = await res.json();

        if (countEl) countEl.innerText = allLogs.length;

        // Map logs for fast lookup: [student_id][event_id] = log
        const attendanceMap = {};
        allLogs.forEach(log => {
            if (!attendanceMap[log.student_id]) attendanceMap[log.student_id] = {};
            attendanceMap[log.student_id][log.event_id] = true;
        });

        // Build Header
        let headHtml = `
            <tr>
                <th class="border border-gray-300 px-4 py-3 bg-gray-200 sticky left-0 z-20 w-48 text-gray-700">Student Name</th>
                <th class="border border-gray-300 px-3 py-3 bg-gray-100 text-gray-600">Year & Section</th>
        `;
        monthEvents.forEach(ev => {
            headHtml += `<th class="border border-gray-300 px-3 py-3 bg-gray-100 text-gray-600 min-w-[100px] text-center">${window.escapeHTML(ev.name)}<br><span class="text-[10px] opacity-60">${ev.date}</span></th>`;
        });
        headHtml += `<th class="border border-gray-300 px-4 py-3 bg-blue-50 text-blue-700 text-center font-bold">Total</th></tr>`;
        thead.innerHTML = headHtml;

        // Build Body (Students)
        let bodyHtml = '';
        const sortedStudents = [...(window.students || [])].sort((a, b) => a.name.localeCompare(b.name));

        sortedStudents.forEach((student, idx) => {
            const rowClass = idx % 2 === 0 ? 'bg-white' : 'bg-gray-50';
            bodyHtml += `<tr class="${rowClass} hover:bg-blue-50/30 transition-colors">`;
            bodyHtml += `<td class="border border-gray-200 px-4 py-2 font-bold text-gray-800 sticky left-0 z-10 ${rowClass}">${window.escapeHTML(student.name)}</td>`;
            bodyHtml += `<td class="border border-gray-200 px-3 py-2 text-gray-500 font-medium">${window.escapeHTML(student.year_and_section)}</td>`;

            let presentCount = 0;
            monthEvents.forEach(ev => {
                const isPresent = attendanceMap[student.student_id] && attendanceMap[student.student_id][ev.id];
                if (isPresent) presentCount++;

                bodyHtml += `
                    <td class="border border-gray-200 px-3 py-2 text-center">
                        ${isPresent
                        ? '<span class="text-green-600 font-black text-sm">P</span>'
                        : '<span class="text-red-300 font-medium text-sm">A</span>'}
                    </td>
                `;
            });

            const attendanceRate = Math.round((presentCount / monthEvents.length) * 100);
            bodyHtml += `
                <td class="border border-gray-200 px-4 py-2 text-center font-black ${attendanceRate >= 75 ? 'text-green-600' : 'text-red-600'}">
                    ${presentCount} / ${monthEvents.length}
                </td>
            </tr>`;
        });
        tbody.innerHTML = bodyHtml;

    } catch (e) {
        console.error("Monthly compilation error:", e);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const monthSel = document.getElementById('records-month-selector');
    if (monthSel) {
        monthSel.addEventListener('change', () => {
            window.renderRecords();
        });
    }

    const btnComp = document.getElementById('btn-compilation-mode');
    if (btnComp) {
        btnComp.addEventListener('click', () => {
            window.isCompilationMode = !window.isCompilationMode;
            btnComp.classList.toggle('bg-green-600');
            btnComp.classList.toggle('text-white');
            btnComp.classList.toggle('border-green-600');
            window.renderRecords();
        });
    }
});
