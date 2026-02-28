window.renderFines = function () {
    const tbody = document.getElementById('fines-tbody');
    if (!tbody) return;

    if (!window.students || window.students.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No students found.</td></tr>`;
        return;
    }

    // Group by year and section
    const grouped = {};
    window.students.forEach(student => {
        const sec = student.year_and_section || 'Unassigned';
        if (!grouped[sec]) grouped[sec] = [];
        grouped[sec].push(student);
    });

    const sections = Object.keys(grouped).sort();

    // Calculate average fine based on events
    const avgFine = window.events && window.events.length
        ? window.events.reduce((sum, ev) => sum + (ev.fine || 50), 0) / window.events.length
        : 50;

    let html = '';

    sections.forEach((section, index) => {
        const folderId = `fines-sec-${index}`;
        const students = grouped[section];

        let sectionTotalFines = 0;
        students.forEach(s => {
            if (s.absences > 0) sectionTotalFines += (s.absences * avgFine);
        });

        // Folder Header Row
        html += `
            <tr class="bg-purple-50/50 hover:bg-purple-50 border-b border-gray-100 cursor-pointer transition-colors" onclick="window.toggleFolder('${folderId}')">
                <td colspan="4" class="px-6 py-3 font-semibold text-purple-900 border-l-4 border-purple-500">
                    <div class="flex items-center space-x-2">
                        <svg id="folder-icon-${folderId}" class="w-4 h-4 text-purple-500 transition-transform duration-200" style="transform: rotate(0deg)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        <span>${window.escapeHTML(section)}</span>
                        <span class="bg-purple-100 text-purple-700 text-xs py-0.5 px-2 rounded-full ml-2">Total Fine: ₱${sectionTotalFines.toFixed(2)}</span>
                    </div>
                </td>
            </tr>
        `;

        // Student Rows
        students.forEach(student => {
            let fine = 0;
            if (student.absences > 0) {
                fine = student.absences * avgFine;
            }

            // Calculate present
            const totalEvents = window.events ? window.events.length : 0;
            const presents = totalEvents - student.absences;

            html += `
                <tr class="folder-row-${folderId} hidden border-b border-gray-50 hover:bg-gray-50 transition-colors bg-white">
                    <td class="px-6 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                ${window.escapeHTML(student.name.charAt(0).toUpperCase())}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">${window.escapeHTML(student.name)}</p>
                                <p class="text-xs text-gray-500">${window.escapeHTML(student.student_id)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">${presents}</span>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs font-bold ${student.absences > 1 ? 'bg-red-100 text-red-700' : (student.absences === 1 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')}">${student.absences}</span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <p class="font-bold text-gray-800">₱${fine.toFixed(2)}</p>
                    </td>
                </tr>
            `;
        });
    });

    tbody.innerHTML = html;
};
