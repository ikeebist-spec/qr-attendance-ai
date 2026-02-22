window.html5QrcodeScanner = null;

// ─── Voice Feedback ──────────────────────────────────────────────────────────
window.speak = function (text) {
    if (!window.speechSynthesis) return;
    // Cancel any in-progress speech first
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US';
    utterance.rate = 1.0;
    utterance.pitch = 1.1;
    window.speechSynthesis.speak(utterance);
};

window.initQRScanner = function () {
    if (!window.Html5QrcodeScanner) return;
    if (!window.html5QrcodeScanner) {
        window.html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 }, false);
        window.html5QrcodeScanner.render((decodedText) => window.simulateScan(decodedText), () => { });
    }
};

window.stopQRScanner = function () {
    if (window.html5QrcodeScanner) {
        window.html5QrcodeScanner.clear().catch(e => console.error(e));
        window.html5QrcodeScanner = null;
    }
};

window.simulateScan = async function (decodedText) {
    const student = window.students.find(s => s.student_id === decodedText.trim());
    const currentEvent = window.events.find(e => e.id === window.selectedEventId);

    if (!currentEvent) {
        window.showToast('No event selected!', 'error');
        return;
    }

    if (student) {
        const res = await window.apiPost('/api/attendance', {
            student_id: student.student_id,
            event_id: currentEvent.id,
            student_name: student.name,
            section: student.section,
        });

        if (res.status === 409) {
            window.showToast(`${student.name} is already logged for ${currentEvent.name}!`, 'error');
            window.speak('Already logged.');
        } else if (res.ok) {
            window.showToast(`Success! Logged attendance for ${student.name}.`);
            window.speak('Thank you.');
            if (window.logActivity) window.logActivity(`Scanned QR for ${student.name} (${currentEvent.name})`);
            if (window.renderRecords) window.renderRecords();
            if (window.renderDashboard) window.renderDashboard();
        } else {
            window.showToast('Failed to log attendance.', 'error');
            window.speak('Scan failed. Please try again.');
        }
    } else {
        window.showToast('Invalid QR Code: Not found in database.', 'error');
        window.speak('Student not found.');
    }

    if (window.html5QrcodeScanner) {
        try { window.html5QrcodeScanner.pause(); setTimeout(() => window.html5QrcodeScanner.resume(), 2500); } catch (e) { }
    }
};
