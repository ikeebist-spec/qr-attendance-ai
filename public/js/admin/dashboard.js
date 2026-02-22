// Global app state (loaded from DB)
window.currentUser = null;
window.activeTab = 'dashboard';
window.events = [];
window.selectedEventId = null;
window.students = [];
window.attendanceLogs = [];
window.activityLogs = [];
window.sections = [];
window.sectionFilter = 'All';
window.insights = { totalFines: 0, patternWarnings: [], atRiskStudents: [], atRiskSections: [] };

// ─── CSRF token helper for POST/DELETE ────────────────────────────────────────
window.csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

window.apiPost = async (url, data) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken() },
        body: JSON.stringify(data),
    });
    return res;
};

window.apiDelete = async (url) => {
    return fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.csrfToken() },
    });
};

// ─── Toast ───────────────────────────────────────────────────────────────────
window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 text-white animate-fade-in-down ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}`;
    toast.innerHTML = type === 'error'
        ? `<i data-lucide="x-circle" class="w-5 h-5"></i><span class="font-medium">${message}</span>`
        : `<i data-lucide="check-circle" class="w-5 h-5"></i><span class="font-medium">${message}</span>`;
    container.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
};

// ─── Tab Switcher ─────────────────────────────────────────────────────────────
window.switchTab = function (tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tabId).classList.add('active');
    window.activeTab = tabId;
    document.getElementById('header-title').innerText = tabId.replace('-', ' ');
    document.querySelectorAll('.sidebar-btn').forEach(btn => {
        btn.className = "sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium text-green-100 hover:bg-green-800/50";
        if (btn.dataset.tab === tabId)
            btn.className = "sidebar-btn w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors text-sm font-medium bg-green-800 text-white";
    });
    if (tabId === 'scan' && window.initQRScanner) {
        document.getElementById('scan-event-name').innerText = window.events.find(e => e.id === window.selectedEventId)?.name || 'None';
        window.initQRScanner();
    } else if (window.stopQRScanner) {
        window.stopQRScanner();
    }
    if (tabId !== 'students' && window.closeAICamera) window.closeAICamera();
};

// ─── Dashboard Render ────────────────────────────────────────────────────────
window.renderDashboard = async function () {
    if (!window.selectedEventId) return;
    try {
        const res = await fetch(`/api/dashboard?event_id=${window.selectedEventId}`);
        const data = await res.json();
        document.getElementById('dash-present').innerText = data.present;
        document.getElementById('dash-total').innerText = data.total_students;
        document.getElementById('dash-fine').innerText = `₱${data.total_fines}`;
        document.getElementById('dash-risk').innerText = data.at_risk;
    } catch (e) { console.error(e); }
};

// ─── App Bootstrap ────────────────────────────────────────────────────────────
async function bootstrapApp() {
    // Load all data from DB
    const [eventsRes, studentsRes, sectionsRes, logsRes] = await Promise.all([
        fetch('/api/events'),
        fetch('/api/students'),
        fetch('/api/sections'),
        fetch('/api/logs'),
    ]);
    window.events = await eventsRes.json();
    window.students = await studentsRes.json();
    window.sections = await sectionsRes.json();
    window.activityLogs = await logsRes.json();

    if (window.events.length > 0)
        window.selectedEventId = window.events[window.events.length - 1].id;

    // Render all modules
    if (window.renderEventSelector) window.renderEventSelector();
    if (window.renderSections) window.renderSections();
    if (window.renderDashboard) window.renderDashboard();
    if (window.renderEvents) window.renderEvents();
    if (window.renderRecords) window.renderRecords();
    if (window.renderStudents) window.renderStudents();
    if (window.runAIAnalysis) window.runAIAnalysis();
    if (window.renderLogs) window.renderLogs();

    lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Auth is handled server-side by Laravel — bootstrap data immediately
    bootstrapApp();

    // Sidebar tab switching
    document.querySelectorAll('.sidebar-btn').forEach(btn => {
        btn.addEventListener('click', () => window.switchTab(btn.dataset.tab));
    });
});

