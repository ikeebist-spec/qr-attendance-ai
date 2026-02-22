// ─── QR Modal ────────────────────────────────────────────────────────────────
window.openQRModal = function (student) {
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(student.student_id)}`;
    document.getElementById('qr-modal-name').innerText = student.name;
    document.getElementById('qr-modal-id').innerText = student.student_id + ' · ' + student.section;
    document.getElementById('qr-modal-img').src = qrUrl;
    document.getElementById('qr-modal').classList.remove('hidden');
};

window.closeQRModal = function () {
    document.getElementById('qr-modal').classList.add('hidden');
};

// ─── Delete Handler ───────────────────────────────────────────────────────────
window.deleteStudent = async function (id, name) {
    if (!confirm(`Delete ${name} from the masterlist? This cannot be undone.`)) return;
    const res = await window.apiDelete(`/api/students/${id}`);
    if (res.ok) {
        window.students = window.students.filter(s => s.id !== id);
        window.showToast(`${name} removed from masterlist.`);
        if (window.logActivity) window.logActivity(`Deleted student: ${name}`);
        window.renderStudents();
        if (window.runAIAnalysis) window.runAIAnalysis();
    } else {
        window.showToast('Failed to delete student.', 'error');
    }
};

// ─── Render Table ─────────────────────────────────────────────────────────────
window.renderStudents = function () {
    const tbody = document.getElementById('students-table-body');
    if (!tbody) return;
    const filtered = window.sectionFilter === 'All'
        ? window.students
        : window.students.filter(s => s.section === window.sectionFilter);

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">No students found.</td></tr>`;
        return;
    }

    tbody.innerHTML = filtered.map(student => `
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 font-mono text-xs text-gray-600">${window.escapeHTML(student.student_id)}</td>
            <td class="px-4 py-3 font-medium text-gray-900">${window.escapeHTML(student.name)}</td>
            <td class="px-4 py-3"><span class="bg-gray-100 px-2 py-1 rounded text-xs font-medium text-gray-700">${window.escapeHTML(student.section)}</span></td>
            <td class="px-4 py-3 text-center">
                <span class="px-2 py-1 rounded text-xs font-bold ${student.absences > 1 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}">${student.absences}</span>
            </td>
            <td class="px-4 py-3 text-center">
                <button onclick="window.openQRModal(${window.escapeHTML(JSON.stringify(student)).replace(/"/g, '&quot;')})"
                    class="inline-flex items-center space-x-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><path d="M14 14h2v2h-2z M18 14h2v2h-2z M14 18h2v2h-2z M18 18h2v2h-2z" stroke-width="0" fill="currentColor"/></svg>
                    <span>View QR</span>
                </button>
            </td>
            <td class="px-4 py-3 text-center">
                <button onclick="window.deleteStudent(${student.id}, '${window.escapeHTML(student.name).replace(/'/g, "\\'")}')"
                    class="inline-flex items-center space-x-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6" stroke-width="2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke-width="2"/><path d="M10 11v6M14 11v6" stroke-width="2"/></svg>
                    <span>Delete</span>
                </button>
            </td>
        </tr>
    `).join('');
};


window.aiVideoTrack = null;

window.openAICamera = function () {
    document.getElementById('ai-camera-container').classList.remove('hidden');
    const video = document.getElementById('ai-video');
    const placeholder = document.getElementById('ai-camera-placeholder');
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(stream => {
                window.aiVideoTrack = stream;
                video.srcObject = stream;
                video.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }).catch(() => {
                video.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
    } else {
        video.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
};

window.closeAICamera = function () {
    const c = document.getElementById('ai-camera-container');
    if (c) c.classList.add('hidden');
    if (window.aiVideoTrack) {
        window.aiVideoTrack.getTracks().forEach(t => t.stop());
        window.aiVideoTrack = null;
    }
    const v = document.getElementById('ai-video');
    if (v) v.srcObject = null;
};

document.addEventListener('DOMContentLoaded', () => {
    // QR Modal close
    document.getElementById('qr-modal-close')?.addEventListener('click', window.closeQRModal);
    document.getElementById('qr-modal-backdrop')?.addEventListener('click', window.closeQRModal);

    document.getElementById('student-filter')?.addEventListener('change', (e) => {
        window.sectionFilter = e.target.value;
        window.renderStudents();
    });

    document.getElementById('add-student-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const student_id = document.getElementById('student-id').value;
        const name = document.getElementById('student-name').value;
        const section = document.getElementById('student-section').value;

        const res = await window.apiPost('/api/students', { student_id, name, section });
        if (res.ok) {
            const newStudent = await res.json();
            window.students.unshift(newStudent);
            if (window.logActivity) window.logActivity(`Added new student: ${name} (${student_id})`);
            window.showToast(`Successfully added ${name}. QR Code generated!`);
            window.renderStudents();
            if (window.runAIAnalysis) window.runAIAnalysis();

            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${student_id}`;
            document.getElementById('generated-qr-img').src = qrUrl;
            document.getElementById('generated-qr-name').innerText = name;
            document.getElementById('generated-qr-id').innerText = student_id;
            document.getElementById('generated-qr-container').classList.remove('hidden');
            e.target.reset();
        } else {
            const err = await res.json();
            window.showToast(err.message || 'Student ID already exists!', 'error');
        }
    });

    document.getElementById('dismiss-qr-btn')?.addEventListener('click', () => {
        document.getElementById('generated-qr-container').classList.add('hidden');
    });

    document.getElementById('toggle-ai-camera')?.addEventListener('click', () => {
        const isHidden = document.getElementById('ai-camera-container').classList.contains('hidden');
        if (isHidden) {
            document.getElementById('ai-camera-container').classList.remove('hidden');
        } else {
            window.closeAICamera();
        }
    });

    document.getElementById('close-ai-camera')?.addEventListener('click', window.closeAICamera);

    // ── Tab switching: Camera | Upload | Paste ────────────────────────────────
    const showPanel = (active) => {
        ['camera', 'upload', 'paste'].forEach(p => {
            document.getElementById(`scanner-panel-${p}`)?.classList.toggle('hidden', active !== p);
            const btn = document.getElementById(`scanner-tab-${p}`);
            if (btn) btn.className = `scanner-tab-btn flex items-center space-x-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors ${active === p ? 'bg-indigo-600 text-white' : 'bg-indigo-800/50 text-indigo-300 hover:bg-indigo-700'}`;
        });
    };

    document.getElementById('scanner-tab-camera')?.addEventListener('click', () => { showPanel('camera'); window.openAICamera(); });
    document.getElementById('scanner-tab-upload')?.addEventListener('click', () => {
        showPanel('upload');
        window.closeAICamera();
        document.getElementById('ai-upload-input').click();
    });
    document.getElementById('scanner-tab-paste')?.addEventListener('click', () => {
        showPanel('paste');
        window.closeAICamera();
        setTimeout(() => document.getElementById('paste-text-input')?.focus(), 100);
    });

    // ── Camera capture button ────────────────────────────────────────────────
    document.getElementById('ai-extract-btn')?.addEventListener('click', () => {
        document.getElementById('ai-scanning-state').classList.remove('hidden');
        document.getElementById('ai-scanning-state').classList.add('flex');
        document.getElementById('ai-capture-btn-container').classList.add('hidden');
        setTimeout(async () => {
            const mockStudents = [
                { student_id: `2024-${Math.floor(1000 + Math.random() * 9000)}`, name: 'AI Extracted Student A', section: window.sections[0] || '1A' },
                { student_id: `2024-${Math.floor(1000 + Math.random() * 9000)}`, name: 'AI Extracted Student B', section: window.sections[0] || '1A' },
            ];
            for (const s of mockStudents) {
                const res = await window.apiPost('/api/students', s);
                if (res.ok) window.students.unshift(await res.json());
            }
            document.getElementById('ai-scanning-state').classList.add('hidden');
            document.getElementById('ai-scanning-state').classList.remove('flex');
            document.getElementById('ai-capture-btn-container').classList.remove('hidden');
            window.closeAICamera();
            window.showToast(`AI extracted ${mockStudents.length} students!`);
            if (window.logActivity) window.logActivity('Used AI Camera to extract students from Masterlist');
            window.renderStudents();
            if (window.runAIAnalysis) window.runAIAnalysis();
        }, 3000);
    });

    // ── Upload tab: file selection ───────────────────────────────────────────
    let uploadedFile = null;

    document.getElementById('ai-upload-input')?.addEventListener('change', (e) => {
        uploadedFile = e.target.files[0];
        if (!uploadedFile) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
            // Show big preview
            document.getElementById('ai-upload-preview').src = ev.target.result;
            document.getElementById('ai-upload-preview-container').classList.remove('hidden');
            // Hide the drop zone now
            document.getElementById('ai-upload-zone').classList.add('hidden');
            // Set Google Lens URL — opens lens.google.com so user can upload/drag the image
            const lensBtn = document.getElementById('ai-lens-btn');
            if (lensBtn) lensBtn.href = 'https://lens.google.com/';
            // Reset paste area
            document.getElementById('upload-paste-input').value = '';
            document.getElementById('upload-paste-result').classList.add('hidden');
            lucide.createIcons();
        };
        reader.readAsDataURL(uploadedFile);
    });

    // ── Upload panel inline paste: parse ─────────────────────────────────────
    document.getElementById('upload-paste-parse-btn')?.addEventListener('click', () => {
        const raw = document.getElementById('upload-paste-input')?.value?.trim();
        if (!raw) { window.showToast('Paste some text first.', 'error'); return; }

        const lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 2);
        const idPattern = /\b(\d{4}[-–]\d{2,5}|\d{6,10})\b/;
        const parsed = [];

        lines.forEach(line => {
            const m = line.match(idPattern);
            const student_id = m ? m[1].replace('–', '-') : 'TBD';
            const name = line.replace(idPattern, '').replace(/^\W+|\W+$/g, '').trim() || line;
            if (name.length > 1) parsed.push({ student_id, name });
        });

        if (parsed.length === 0) { window.showToast('No names found. Try again.', 'error'); return; }

        // Render rows
        document.getElementById('upload-paste-list').innerHTML = parsed.map((s, i) => `
            <div class="flex items-center space-x-2 bg-indigo-900/40 rounded-lg px-3 py-1.5 text-xs">
                <span class="text-indigo-400 font-mono w-5 flex-shrink-0">${i + 1}</span>
                <input data-field="student_id" value="${window.escapeHTML(s.student_id)}"
                    class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white w-28 flex-shrink-0 outline-none focus:border-indigo-400 font-mono" />
                <input data-field="name" value="${window.escapeHTML(s.name)}"
                    class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white flex-1 outline-none focus:border-indigo-400" />
            </div>
        `).join('');

        // Populate section dropdown
        const sel = document.getElementById('upload-paste-section');
        if (sel) sel.innerHTML = (window.sections || []).map(sec => `<option value="${window.escapeHTML(sec)}">${window.escapeHTML(sec)}</option>`).join('');

        document.getElementById('upload-paste-count').innerText = parsed.length;
        document.getElementById('upload-paste-result').classList.remove('hidden');
        lucide.createIcons();
    });

    // ── Upload panel inline paste: save ──────────────────────────────────────
    document.getElementById('upload-paste-save-btn')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const section = document.getElementById('upload-paste-section')?.value?.trim();
        if (!section) { window.showToast('Please select a section.', 'error'); return; }

        const rows = document.querySelectorAll('#upload-paste-list > div');
        if (!rows.length) { window.showToast('No students to save.', 'error'); return; }

        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Saving...';

        let tbdCounter = 0;
        const students = [];
        rows.forEach(row => {
            let sid = row.querySelector('[data-field="student_id"]').value.trim();
            const nm = row.querySelector('[data-field="name"]').value.trim();
            // Auto-number TBD IDs so they don't collide on unique constraint
            if (!sid || sid.toUpperCase().startsWith('TBD')) sid = `TBD-${Date.now()}-${++tbdCounter}`;
            if (nm) students.push({ student_id: sid, name: nm, section });
        });

        let saved = 0;
        const errors = [];
        for (const s of students) {
            const res = await window.apiPost('/api/students', s);
            if (res.ok) {
                const ns = await res.json();
                window.students.unshift(ns);
                saved++;
            } else {
                try {
                    const err = await res.json();
                    const msg = err?.errors
                        ? Object.values(err.errors).flat().join(' | ')
                        : (err?.message || `HTTP ${res.status}`);
                    errors.push(`${s.name}: ${msg}`);
                } catch { errors.push(`${s.name}: server error`); }
            }
        }

        btn.disabled = false;
        btn.innerHTML = '<span>Save All to Masterlist</span>';

        if (errors.length > 0 && saved === 0) {
            window.showToast(`❌ Failed: ${errors[0]}`, 'error');
            return;
        }

        window.showToast(`✅ Saved ${saved} student(s) to ${section}${errors.length ? `. ${errors.length} skipped (duplicate ID?).` : '!'}`);
        if (window.logActivity) window.logActivity(`Saved ${saved} student(s) to section ${section}`);

        document.getElementById('upload-paste-result').classList.add('hidden');
        document.getElementById('upload-paste-input').value = '';
        document.getElementById('ai-upload-preview-container').classList.add('hidden');
        document.getElementById('ai-upload-zone').classList.remove('hidden');
        document.getElementById('ai-upload-input').value = '';
        uploadedFile = null;
        document.getElementById('ai-camera-container').classList.add('hidden');
        window.renderStudents();
        if (window.runAIAnalysis) window.runAIAnalysis();
    });

    // ── Upload tab: scan with Gemini ─────────────────────────────────────────
    document.getElementById('ai-upload-scan-btn')?.addEventListener('click', async () => {
        if (!uploadedFile) return;

        // Show scanning state
        document.getElementById('ai-upload-scan-btn').disabled = true;
        document.getElementById('ai-upload-scanning-state').classList.remove('hidden');
        document.getElementById('ai-upload-scanning-state').classList.add('flex');
        document.getElementById('ai-upload-result').classList.add('hidden');

        const formData = new FormData();
        formData.append('photo', uploadedFile);
        formData.append('_token', window.csrfToken());

        try {
            const res = await fetch('/api/masterlist/scan', { method: 'POST', body: formData });
            const data = await res.json();

            document.getElementById('ai-upload-scanning-state').classList.add('hidden');
            document.getElementById('ai-upload-scanning-state').classList.remove('flex');

            if (!res.ok || data.error) {
                window.showToast(data.error || 'Failed to analyze photo.', 'error');
                document.getElementById('ai-upload-scan-btn').disabled = false;
                return;
            }

            const extracted = data.students || [];
            if (extracted.length === 0) {
                window.showToast('No students found in the photo. Try a clearer image.', 'error');
                document.getElementById('ai-upload-scan-btn').disabled = false;
                return;
            }

            // Show extracted list — name + ID only
            const list = document.getElementById('ai-upload-extracted-list');
            list.innerHTML = extracted.map((s, i) => `
                <div class="flex items-center space-x-2 bg-indigo-900/40 rounded-lg px-3 py-2 text-xs">
                    <span class="text-indigo-400 font-mono w-5 flex-shrink-0">${i + 1}</span>
                    <input data-field="student_id" value="${window.escapeHTML(s.student_id || 'TBD')}"
                        class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white w-28 flex-shrink-0 outline-none focus:border-indigo-400 font-mono" placeholder="ID" />
                    <input data-field="name" value="${window.escapeHTML(s.name || '')}"
                        class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white flex-1 outline-none focus:border-indigo-400" placeholder="Full Name" />
                </div>
            `).join('');

            // Populate section dropdown
            const sectionSel = document.getElementById('ai-upload-section-select');
            if (sectionSel) {
                sectionSel.innerHTML = (window.sections || []).map(sec =>
                    `<option value="${window.escapeHTML(sec)}">${window.escapeHTML(sec)}</option>`
                ).join('');
            }

            document.getElementById('ai-extracted-count').innerText = extracted.length;
            window._aiExtractedStudents = extracted;
            document.getElementById('ai-upload-result').classList.remove('hidden');
            lucide.createIcons();
            if (window.logActivity) window.logActivity(`AI scanned photo — extracted ${extracted.length} student(s)`);

        } catch (err) {
            document.getElementById('ai-upload-scanning-state').classList.add('hidden');
            document.getElementById('ai-upload-scanning-state').classList.remove('flex');
            window.showToast('Network error. Please try again.', 'error');
            document.getElementById('ai-upload-scan-btn').disabled = false;
        }
    });

    // ── Upload tab: save extracted students ──────────────────────────────────
    document.getElementById('ai-upload-save-btn')?.addEventListener('click', async () => {
        const section = document.getElementById('ai-upload-section-select')?.value?.trim();
        if (!section) { window.showToast('Please select a section first.', 'error'); return; }

        const rows = document.querySelectorAll('#ai-upload-extracted-list > div');
        const students = [];
        rows.forEach((row) => {
            students.push({
                student_id: row.querySelector('[data-field="student_id"]').value.trim(),
                name: row.querySelector('[data-field="name"]').value.trim(),
                section: section,
            });
        });

        let saved = 0, skipped = 0;
        for (const s of students) {
            if (!s.student_id || !s.name) { skipped++; continue; }
            const res = await window.apiPost('/api/students', s);
            if (res.ok) { const ns = await res.json(); window.students.unshift(ns); saved++; }
            else skipped++;
        }

        window.showToast(`✅ Saved ${saved} student(s) to ${section}${skipped > 0 ? `. ${skipped} skipped (duplicate/empty).` : '!'}`);
        if (window.logActivity) window.logActivity(`Saved ${saved} student(s) via AI scan to section ${section}`);
        document.getElementById('ai-upload-result').classList.add('hidden');
        document.getElementById('ai-camera-container').classList.add('hidden');
        uploadedFile = null;
        document.getElementById('ai-upload-input').value = '';
        document.getElementById('ai-upload-preview-container').classList.add('hidden');
        document.getElementById('ai-upload-scan-btn').disabled = true;
        window.renderStudents();
        if (window.runAIAnalysis) window.runAIAnalysis();
    });

    // ── Paste Text: parse pasted text into student rows ──────────────────────
    document.getElementById('paste-parse-btn')?.addEventListener('click', () => {
        const raw = document.getElementById('paste-text-input')?.value?.trim();
        if (!raw) { window.showToast('Please paste some text first.', 'error'); return; }

        // Smart parser: split by newlines, try to extract ID and name per line
        const lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 2);
        const parsed = [];

        // Regex patterns for student ID: digits with dash, e.g. 2024-001 or 20240001
        const idPattern = /\b(\d{4}[-–]\d{2,5}|\d{6,10})\b/;

        lines.forEach(line => {
            const idMatch = line.match(idPattern);
            const student_id = idMatch ? idMatch[1].replace('–', '-') : 'TBD';
            // Remove the ID from the line to get the name
            const name = line.replace(idPattern, '').replace(/^\W+|\W+$/g, '').trim() || line;
            if (name.length > 1) parsed.push({ student_id, name });
        });

        if (parsed.length === 0) {
            window.showToast('Could not find any student names. Try reformatting the text.', 'error');
            return;
        }

        // Populate result list
        const list = document.getElementById('paste-extracted-list');
        list.innerHTML = parsed.map((s, i) => `
            <div class="flex items-center space-x-2 bg-indigo-900/40 rounded-lg px-3 py-2 text-xs">
                <span class="text-indigo-400 font-mono w-5 flex-shrink-0">${i + 1}</span>
                <input data-field="student_id" value="${window.escapeHTML(s.student_id)}"
                    class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white w-28 flex-shrink-0 outline-none focus:border-indigo-400 font-mono" placeholder="ID" />
                <input data-field="name" value="${window.escapeHTML(s.name)}"
                    class="bg-indigo-950/60 border border-indigo-700 rounded px-2 py-1 text-white flex-1 outline-none focus:border-indigo-400" placeholder="Full Name" />
            </div>
        `).join('');

        // Populate section dropdown
        const secSel = document.getElementById('paste-section-select');
        if (secSel) secSel.innerHTML = (window.sections || []).map(sec => `<option value="${window.escapeHTML(sec)}">${window.escapeHTML(sec)}</option>`).join('');

        document.getElementById('paste-extracted-count').innerText = parsed.length;
        document.getElementById('paste-result').classList.remove('hidden');
        lucide.createIcons();
    });

    // ── Paste Text: save to masterlist ────────────────────────────────────────
    document.getElementById('paste-save-btn')?.addEventListener('click', async () => {
        const section = document.getElementById('paste-section-select')?.value?.trim();
        if (!section) { window.showToast('Please select a section.', 'error'); return; }

        const rows = document.querySelectorAll('#paste-extracted-list > div');
        const students = [];
        rows.forEach(row => {
            students.push({
                student_id: row.querySelector('[data-field="student_id"]').value.trim(),
                name: row.querySelector('[data-field="name"]').value.trim(),
                section,
            });
        });

        let saved = 0, skipped = 0;
        for (const s of students) {
            if (!s.student_id || !s.name) { skipped++; continue; }
            const res = await window.apiPost('/api/students', s);
            if (res.ok) { const ns = await res.json(); window.students.unshift(ns); saved++; }
            else skipped++;
        }

        window.showToast(`✅ Saved ${saved} student(s) to ${section}${skipped > 0 ? `. ${skipped} skipped.` : '!'}`);
        if (window.logActivity) window.logActivity(`Saved ${saved} student(s) via Paste Text to section ${section}`);
        document.getElementById('paste-result').classList.add('hidden');
        document.getElementById('paste-text-input').value = '';
        document.getElementById('ai-camera-container').classList.add('hidden');
        window.renderStudents();
        if (window.runAIAnalysis) window.runAIAnalysis();
    });
});

