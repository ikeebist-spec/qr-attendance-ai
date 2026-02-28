// ─── QR Modal ────────────────────────────────────────────────────────────────
window.openQRModal = function (student) {
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(student.student_id)}`;
    document.getElementById('qr-modal-name').innerText = student.name;
    document.getElementById('qr-modal-id').innerText = student.student_id + ' · ' + student.year_and_section;
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
window.toggleFolder = function (folderId) {
    const rows = document.querySelectorAll(`.folder-row-${folderId}`);
    const icon = document.getElementById(`folder-icon-${folderId}`);

    let isHidden = false;
    rows.forEach(row => {
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            isHidden = true; // Was hidden, now showing
        } else {
            row.classList.add('hidden');
        }
    });

    if (icon) {
        if (isHidden) {
            icon.style.transform = 'rotate(90deg)';
        } else {
            icon.style.transform = 'rotate(0deg)';
        }
    }
};

window.renderStudents = function () {
    const tbody = document.getElementById('students-table-body');
    if (!tbody) return;
    const filtered = window.yearAndSectionFilter === 'All'
        ? window.students
        : window.students.filter(s => s.year_and_section === window.yearAndSectionFilter);

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">No students found.</td></tr>`;
        return;
    }

    // Group by Year and Section
    const grouped = {};
    filtered.forEach(student => {
        const sec = student.year_and_section || 'Unassigned';
        if (!grouped[sec]) grouped[sec] = [];
        grouped[sec].push(student);
    });

    const sections = Object.keys(grouped).sort();

    let html = '';
    sections.forEach((section, index) => {
        const folderId = `masterlist-sec-${index}`;
        const students = grouped[section];

        // Folder Header Row (Excel Style)
        html += `
            <tr class="bg-blue-50/80 hover:bg-blue-100 border-b border-gray-200 cursor-pointer transition-colors" onclick="window.toggleFolder('${folderId}')">
                <td colspan="6" class="px-4 py-3 font-black text-blue-900 border border-gray-300">
                    <div class="flex items-center space-x-2">
                        <svg id="folder-icon-${folderId}" class="w-4 h-4 text-blue-600 transition-transform duration-200" style="transform: rotate(0deg)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span>${window.escapeHTML(section)}</span>
                        <span class="bg-blue-200 text-blue-800 text-[10px] py-0.5 px-2 rounded-full font-bold ml-2">${students.length} STUDENT(S)</span>
                    </div>
                </td>
            </tr>
        `;

        students.forEach(student => {
            html += `
                <tr class="folder-row-${folderId} hidden hover:bg-gray-50 transition-colors bg-white">
                    <td class="px-4 py-2 font-mono text-xs text-gray-500 border border-gray-200">${window.escapeHTML(student.student_id)}</td>
                    <td class="px-4 py-2 font-black text-gray-800 border border-gray-200">${window.escapeHTML(student.name)}</td>
                    <td class="px-4 py-2 text-center border border-gray-200"><span class="bg-gray-100 px-2 py-0.5 rounded text-[10px] font-bold text-gray-600 border border-gray-300">${window.escapeHTML(student.year_and_section)}</span></td>
                    <td class="px-4 py-2 text-center border border-gray-200">
                        <span class="px-2 py-0.5 rounded text-xs font-black ${student.absences > 1 ? 'text-red-600' : 'text-blue-600'}">${student.absences}</span>
                    </td>
                    <td class="px-4 py-2 text-center border border-gray-200">
                        <button onclick="window.openQRModal(${window.escapeHTML(JSON.stringify(student)).replace(/"/g, '&quot;')})"
                            class="inline-flex items-center space-x-1 font-black text-blue-600 hover:text-blue-800 text-[10px] uppercase">
                            <i data-lucide="qr-code" class="w-3 h-3"></i>
                            <span>View QR</span>
                        </button>
                    </td>
                    <td class="px-4 py-2 text-center border border-gray-200">
                        <button onclick="window.deleteStudent(${student.id}, '${window.escapeHTML(student.name).replace(/'/g, "\\'")}')"
                            class="inline-flex items-center space-x-1 font-black text-red-600 hover:text-red-800 text-[10px] uppercase">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                            <span>Del</span>
                        </button>
                    </td>
                </tr>
            `;
        });
    });

    tbody.innerHTML = html;
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
        window.yearAndSectionFilter = e.target.value;
        window.renderStudents();
    });

    document.getElementById('add-student-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const student_id = document.getElementById('student-id').value;
        const name = document.getElementById('student-name').value;
        const year_and_section = document.getElementById('student-year-and-section').value;

        const res = await window.apiPost('/api/students', { student_id, name, year_and_section });
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
            if (btn) btn.className = `scanner-tab-btn flex items-center space-x-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors ${active === p ? 'bg-purple-600 text-white' : 'bg-purple-800/50 text-blue-200 hover:bg-purple-700'}`;
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

    // ── Local OCR Engine (Tesseract.js) ────────────────────────────────────

    const runLocalOCR = async (targetAction) => {
        const previewImg = document.getElementById('ai-upload-preview');
        const loaderCont = document.getElementById('ocr-loader-container');
        const progressBar = document.getElementById('ocr-bar');
        const statusMsg = document.getElementById('ocr-status-msg');
        const percentText = document.getElementById('ocr-percent');
        const resultPanel = document.getElementById('ocr-result-panel');

        if (!previewImg || !previewImg.src || previewImg.src === window.location.href) {
            window.showToast('Please upload a photo first.', 'error');
            return;
        }

        // Hide old results and show loader
        resultPanel.classList.add('hidden');
        loaderCont.classList.remove('hidden');
        progressBar.style.width = '0%';
        percentText.innerText = '0%';
        statusMsg.innerText = 'Initializing...';

        try {
            const result = await Tesseract.recognize(
                previewImg.src,
                'eng',
                {
                    logger: m => {
                        if (m.status === 'recognizing text') {
                            const p = Math.floor(m.progress * 100);
                            progressBar.style.width = p + '%';
                            percentText.innerText = p + '%';
                            statusMsg.innerText = 'Analyzing Image...';
                        } else {
                            statusMsg.innerText = m.status.charAt(0).toUpperCase() + m.status.slice(1).replace(/_/g, ' ') + '...';
                        }
                    }
                }
            );

            const rawText = result.data.text;

            if (targetAction === 'copy') {
                await navigator.clipboard.writeText(rawText);
                window.showToast('✅ All text copied to clipboard!');
                statusMsg.innerText = 'Text Copied!';
                setTimeout(() => loaderCont.classList.add('hidden'), 2000);
            } else if (targetAction === 'extract') {
                const lines = rawText.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 5);
                // More aggressive ID pattern: matches 25-12345 or 2512345
                const idPattern = /(\d{2,4})[-–]?(\d{3,7})/;
                const students = [];

                lines.forEach(line => {
                    // 1. Find the ID
                    const idMatch = line.match(idPattern);
                    let sid = 'TBD';
                    if (idMatch) {
                        sid = idMatch[1] + '-' + idMatch[2];
                    }

                    // 2. Extract name by removing ID and other non-name junk
                    let namePart = line;
                    if (idMatch) {
                        namePart = line.replace(idMatch[0], '');
                    }

                    // Remove leading row numbers and loose digits
                    namePart = namePart.replace(/^\d+/, '');
                    namePart = namePart.replace(/\d{4,}/g, '');

                    let name = namePart.trim()
                        .replace(/^[^a-zA-Z]+|[^a-zA-Z]+$/g, '')
                        .replace(/\s\s+/g, ' ')
                        .trim();

                    if (name.length > 2) {
                        students.push({ student_id: sid, name });
                    }
                });

                if (students.length === 0) {
                    window.showToast('Could not detect student names in this photo.', 'error');
                } else {
                    renderOcrVerifiedList(students);
                    window.showToast(`✅ Successfully detected ${students.length} students!`);
                    statusMsg.innerText = 'Students Extracted!';
                    setTimeout(() => loaderCont.classList.add('hidden'), 2000);
                }
            }

        } catch (err) {
            console.error('OCR Error:', err);
            window.showToast('OCR Failed. Ensure text in photo is clear.', 'error');
            statusMsg.innerText = 'Error processing image.';
        }
    };

    const renderOcrVerifiedList = (students) => {
        const panel = document.getElementById('ocr-result-panel');
        const list = document.getElementById('ocr-students-list');
        const countSpan = document.getElementById('ocr-count');
        const sectionList = document.getElementById('ocr-sections-list');

        list.innerHTML = students.map((s, i) => `
            <div class="flex items-center space-x-2 bg-purple-950/60 rounded-xl px-4 py-2 border border-purple-800/40 group hover:border-blue-500/50 transition-colors">
                <span class="text-purple-500 font-mono text-[9px] w-5">${i + 1}</span>
                <div class="flex-1 grid grid-cols-2 gap-2">
                    <input data-field="id" value="${window.escapeHTML(s.student_id)}" class="bg-purple-900 border border-purple-700 rounded-lg px-2 py-1.5 text-[10px] text-white outline-none focus:border-blue-500 font-mono" />
                    <input data-field="name" value="${window.escapeHTML(s.name)}" class="bg-purple-900 border border-purple-700 rounded-lg px-2 py-1.5 text-[10px] text-white outline-none focus:border-blue-500 font-bold" />
                </div>
            </div>
        `).join('');

        if (sectionList) {
            sectionList.innerHTML = (window.yearAndSections || []).map(sec =>
                `<option value="${window.escapeHTML(sec)}">`
            ).join('');
        }

        countSpan.innerText = students.length;
        panel.classList.remove('hidden');
        lucide.createIcons();
    };

    // ── Photo Upload ─────────────────────────────────────────────────────────
    document.getElementById('ai-upload-input')?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (ev) => {
            document.getElementById('ai-upload-preview').src = ev.target.result;
            document.getElementById('ai-upload-preview-container').classList.remove('hidden');
            document.getElementById('ai-upload-zone').classList.add('hidden');

            // Reset OCR UI
            document.getElementById('ocr-loader-container').classList.add('hidden');
            document.getElementById('ocr-result-panel').classList.add('hidden');
            document.getElementById('ocr-bar').style.width = '0%';
            document.getElementById('ocr-percent').innerText = '0%';
        };
        reader.readAsDataURL(file);
    });

    // ── Action Buttons ───────────────────────────────────────────────────────
    document.getElementById('copy-all-text-btn')?.addEventListener('click', () => runLocalOCR('copy'));
    document.getElementById('extract-students-btn')?.addEventListener('click', () => runLocalOCR('extract'));

    // ── Enroll Button ────────────────────────────────────────────────────────
    document.getElementById('ocr-enroll-btn')?.addEventListener('click', async (e) => {
        const section = document.getElementById('ocr-section-sel').value;
        const studentRows = document.querySelectorAll('#ocr-students-list > div');
        const studentsToSave = [];
        let tbdCount = 0;

        studentRows.forEach(row => {
            let id = row.querySelector('[data-field="id"]').value.trim();
            const name = row.querySelector('[data-field="name"]').value.trim();

            if (!id || id.toUpperCase() === 'TBD') {
                id = `TBD-${Date.now()}-${++tbdCount}`;
            }

            if (name.length > 1) {
                studentsToSave.push({ student_id: id, name, year_and_section: section });
            }
        });

        if (studentsToSave.length === 0) {
            window.showToast('No students to save.', 'error');
            return;
        }

        if (await window.batchSaveStudents(studentsToSave, e.currentTarget, section, 'TEXT ACTION SCAN')) {
            document.getElementById('ai-upload-preview-container').classList.add('hidden');
            document.getElementById('ai-upload-zone').classList.remove('hidden');
            document.getElementById('ocr-result-panel').classList.add('hidden');
            document.getElementById('ocr-loader-container').classList.add('hidden');
        }
    });

    // ── Paste Logic Fallback ─────────────────────────────────────────────────
    document.getElementById('paste-parse-btn')?.addEventListener('click', () => {
        const text = document.getElementById('paste-text-input')?.value || '';
        const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 5);
        const idPattern = /\b(\d{2,4}[-–]\w{3,10})\b/;
        const students = [];

        lines.forEach(line => {
            const idMatch = line.match(idPattern);
            const sid = idMatch ? idMatch[1].replace('–', '-') : 'TBD';

            let partWithoutId = line.replace(idMatch ? idMatch[0] : '', '').trim();
            partWithoutId = partWithoutId.replace(/^\d+\s+/, '').trim();
            let name = partWithoutId.replace(/^[^a-zA-Z]+|[^a-zA-Z]+$/g, '').trim();

            if (name.length > 2) students.push({ student_id: sid, name });
        });

        if (students.length > 0) {
            renderOcrVerifiedList(students);
            document.getElementById('paste-result')?.classList.add('hidden');
        } else {
            window.showToast('Could not find student data in pasted text.', 'error');
        }
    });

    // ── Common Batch Save Helper ─────────────────────────────────────────────
    window.batchSaveStudents = async (students, btn, section, source) => {
        if (!students.length) return;

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="animate-spin mr-2"></i> Saving...';

        try {
            const res = await window.apiPost('/api/students/batch', { students });
            if (res.ok) {
                const data = await res.json();
                let msg = `✅ Successfully stored ${data.count} students!`;
                if (data.skipped_count > 0) {
                    msg += ` (${data.skipped_count} skipped - already in list)`;
                    window.showToast(`${data.skipped_count} students were already in the masterlist.`, 'warning');
                }
                window.showToast(msg);
                await window.fetchStudents();
                window.renderStudents();
                if (window.runAIAnalysis) window.runAIAnalysis();
                if (window.logActivity) window.logActivity(`Stored ${data.count} students via ${source} (Section: ${section})`);
                return true;
            } else {
                const err = await res.json();
                window.showToast(err.message || 'Batch storage failed.', 'error');
                return false;
            }
        } catch (err) {
            console.error('Batch save error:', err);
            window.showToast('Network error during batch save.', 'error');
            return false;
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    };

    // ── Upload panel inline paste: save ──────────────────────────────────────
    document.getElementById('upload-paste-save-btn')?.addEventListener('click', async (e) => {
        const section = document.getElementById('upload-paste-year-and-section')?.value?.trim();
        if (!section) { window.showToast('Select a section.', 'error'); return; }

        const rows = document.querySelectorAll('#upload-paste-list > div');
        const students = [];
        let tbd = 0;
        rows.forEach(row => {
            let sid = row.querySelector('[data-field="student_id"]').value.trim();
            const nm = row.querySelector('[data-field="name"]').value.trim();
            if (!sid || sid.toUpperCase().startsWith('TBD')) sid = `TBD-${Date.now()}-${++tbd}`;
            if (nm) students.push({ student_id: sid, name: nm, year_and_section: section });
        });

        if (await window.batchSaveStudents(students, e.currentTarget, section, 'Manual Upload')) {
            document.getElementById('upload-paste-result').classList.add('hidden');
            document.getElementById('ai-upload-preview-container').classList.add('hidden');
            document.getElementById('ai-upload-zone').classList.remove('hidden');
            document.getElementById('ai-camera-container').classList.add('hidden');
        }
    });

    // ── Upload tab: save extracted students ──────────────────────────────────
    document.getElementById('ai-upload-save-btn')?.addEventListener('click', async (e) => {
        const section = document.getElementById('ai-upload-section-select')?.value?.trim();
        if (!section) { window.showToast('Select a section.', 'error'); return; }

        const rows = document.querySelectorAll('#ai-upload-extracted-list > div');
        const students = [];
        rows.forEach(row => {
            students.push({
                student_id: row.querySelector('[data-field="student_id"]').value.trim(),
                name: row.querySelector('[data-field="name"]').value.trim(),
                year_and_section: section,
            });
        });

        if (await window.batchSaveStudents(students, e.currentTarget, section, 'AI Upload Scan')) {
            document.getElementById('ai-upload-result').classList.add('hidden');
            document.getElementById('ai-camera-container').classList.add('hidden');
            document.getElementById('ai-upload-preview-container').classList.add('hidden');
            document.getElementById('ai-upload-zone').classList.remove('hidden');
        }
    });

    // ── Paste Text: save to masterlist ────────────────────────────────────────
    document.getElementById('paste-save-btn')?.addEventListener('click', async (e) => {
        const section = document.getElementById('paste-year-and-section-select')?.value?.trim();
        if (!section) { window.showToast('Select a section.', 'error'); return; }

        const rows = document.querySelectorAll('#paste-extracted-list > div');
        const students = [];
        rows.forEach(row => {
            students.push({
                student_id: row.querySelector('[data-field="student_id"]').value.trim(),
                name: row.querySelector('[data-field="name"]').value.trim(),
                year_and_section: section,
            });
        });

        if (await window.batchSaveStudents(students, e.currentTarget, section, 'Paste Text')) {
            document.getElementById('paste-result').classList.add('hidden');
            document.getElementById('paste-text-input').value = '';
            document.getElementById('ai-camera-container').classList.add('hidden');
        }
    });
});

