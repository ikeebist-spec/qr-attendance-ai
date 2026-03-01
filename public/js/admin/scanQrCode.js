window.html5QrcodeScanner = null;

// ─── Sound Feedback ────────────────────────────────────────────────────────────
// Use a singleton AudioContext to prevent hitting the browser hardware limit (max 6)
if (!window.globalAudioCtx) {
    try {
        window.globalAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    } catch (e) {
        console.warn("AudioContext not supported", e);
    }
}

window.playSuccessSound = function () {
    try {
        if (!window.globalAudioCtx) return;

        // Resume context if it was suspended (browser autoplay policy)
        if (window.globalAudioCtx.state === 'suspended') {
            window.globalAudioCtx.resume();
        }

        const oscillator = window.globalAudioCtx.createOscillator();
        const gainNode = window.globalAudioCtx.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(800, window.globalAudioCtx.currentTime); // 800 Hz
        oscillator.frequency.exponentialRampToValueAtTime(1200, window.globalAudioCtx.currentTime + 0.1); // Ramp up to 1200 Hz

        gainNode.gain.setValueAtTime(0.5, window.globalAudioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, window.globalAudioCtx.currentTime + 0.15); // Fade out quickly

        oscillator.connect(gainNode);
        gainNode.connect(window.globalAudioCtx.destination);

        oscillator.start();
        oscillator.stop(window.globalAudioCtx.currentTime + 0.15);
    } catch (e) {
        console.warn("Audio playback failed", e);
    }
};

// ─── Voice Feedback ──────────────────────────────────────────────────────────
window.speak = function (text) {
    if (!window.speechSynthesis) return;
    try {
        window.speechSynthesis.cancel(); // Cancel any in-progress speech
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 1.3; // fast feedback
        utterance.pitch = 1.1;
        window.speechSynthesis.speak(utterance);
    } catch (err) {
        console.warn("Speech synthesis error", err);
    }
};

window.initQRScanner = function () {
    if (!window.Html5QrcodeScanner) return;
    if (!window.html5QrcodeScanner) {
        // fps 15 is extremely fast but stable for low-end device cameras 
        // formats to limit processing time to standard QR codes
        window.html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
            fps: 30,
            qrbox: 300,
            rememberLastUsedCamera: true,
            aspectRatio: 1.0
        }, false);

        window.html5QrcodeScanner.render(
            (decodedText) => window.simulateScan(decodedText),
            (error) => { /* ignore normal frame failures */ }
        );
    }
};

window.stopQRScanner = function () {
    if (window.html5QrcodeScanner) {
        window.html5QrcodeScanner.clear().catch(e => console.error(e));
        window.html5QrcodeScanner = null;
    }
};

window.recentScans = new Map();
window.scanQueue = [];
window.isQueueProcessing = false;

// Background worker to process scans one by one (prevents PHP single-thread server from crashing)
window.processScanQueue = async function () {
    if (window.isQueueProcessing || window.scanQueue.length === 0) return;
    window.isQueueProcessing = true;

    while (window.scanQueue.length > 0) {
        const scanTask = window.scanQueue.shift();
        try {
            const res = await window.apiPost('/api/attendance', scanTask.payload);

            if (res.status === 409) {
                window.showToast(`${scanTask.student.name} is already logged!`, 'error');
                // Optional: distinct sound for already logged
            } else if (res.ok) {
                // Update UI asynchronously
                if (window.logActivity) window.logActivity(`Scanned QR: ${scanTask.student.name}`);
                if (window.renderRecords) window.renderRecords();
                if (window.renderDashboard) window.renderDashboard();
            } else {
                window.showToast(`Failed to log ${scanTask.student.name}.`, 'error');
                window.recentScans.delete(scanTask.qrCode);
            }
        } catch (err) {
            console.error('Scan API error:', err);
            window.showToast(`Network error for ${scanTask.student.name}.`, 'error');
            window.recentScans.delete(scanTask.qrCode);
        }
    }

    window.isQueueProcessing = false;
};

window.simulateScan = async function (decodedText) {
    const now = Date.now();
    const qrCode = decodedText.trim();

    // Prevent double scanning the exact same QR code within 2 seconds (faster turnaround)
    const lastTime = window.recentScans.get(qrCode) || 0;
    if (now - lastTime < 2000) {
        return;
    }

    // Register this scan immediately
    window.recentScans.set(qrCode, now);

    const currentEvent = window.events.find(e => e.id === window.selectedEventId);

    if (!currentEvent) {
        window.showToast('No event selected!', 'error');
        window.recentScans.delete(qrCode);
        return;
    }

    // ─── Time Window Verification ─────────────────────────────────────────────
    const nowTime = new Date();

    // Helper to parse "YYYY-MM-DD HH:mm:ss" strings from server (Asia/Manila)
    const parseServerDate = (dateStr) => {
        if (!dateStr) return null;
        // If it's already an ISO string or has timezone, new Date() works.
        // If it's a "YYYY-MM-DD HH:mm:ss" string, we assume it's Asia/Manila.
        if (dateStr.includes('T') || dateStr.includes('Z')) return new Date(dateStr);
        return new Date(dateStr.replace(' ', 'T') + '+08:00');
    };

    const startTime = parseServerDate(currentEvent.start_time);
    const endTime = parseServerDate(currentEvent.end_time);

    if (startTime && nowTime < startTime) {
        window.showToast(`Not started. Please wait until ${startTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}.`, 'error');
        window.recentScans.delete(qrCode);
        return;
    }

    if (endTime && nowTime > endTime) {
        window.showToast(`Scan failed: Attendance closed at ${endTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}.`, 'error');
        window.recentScans.delete(qrCode);
        return;
    }

    const student = window.students.find(s => String(s.student_id) === qrCode);

    if (student) {
        // INSTANT visual/audio feedback so the user can quickly move to the next person
        window.showToast(`THANK YOU! ${student.name}`);
        window.playSuccessSound();
        window.speak('THANK YOU!');

        // Capture device time for accuracy
        const deviceTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        // Add to background queue to prevent freezing the server
        window.scanQueue.push({
            qrCode,
            student,
            payload: {
                student_id: student.student_id,
                event_id: currentEvent.id,
                student_name: student.name,
                year_and_section: student.year_and_section,
                scanned_at: deviceTime
            }
        });

        // Trigger queue processor
        window.processScanQueue();

    } else {
        window.showToast('Invalid QR: Not found in database.', 'error');
        window.recentScans.set(qrCode, now - 2000);
    }
};

// Map cleanup to prevent memory bloat
setInterval(() => {
    const timeNow = Date.now();
    for (const [qr, timestamp] of window.recentScans.entries()) {
        if (timeNow - timestamp > 5000) {
            window.recentScans.delete(qr);
        }
    }
}, 10000);
