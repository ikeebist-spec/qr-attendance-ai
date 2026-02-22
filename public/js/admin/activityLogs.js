window.logActivity = async function (action) {
    if (window.renderLogs) window.renderLogs(); // optimistic UI
    try {
        await window.apiPost('/api/logs', {
            user: window.currentUser ? window.currentUser.username : 'System',
            action: action,
        });
        // Refresh from DB
        const res = await fetch('/api/logs');
        window.activityLogs = await res.json();
        window.renderLogs();
    } catch (e) { console.error(e); }
};

window.renderLogs = function () {
    const emptyState = document.getElementById('logs-empty');
    if (!emptyState) return;
    const list = document.getElementById('logs-list');

    if (window.activityLogs.length === 0) {
        emptyState.classList.remove('hidden');
        list.classList.add('hidden');
    } else {
        emptyState.classList.add('hidden');
        list.classList.remove('hidden');
        list.innerHTML = window.activityLogs.map(log => `
            <li class="px-6 py-4 hover:bg-gray-50 transition-colors flex items-start space-x-4">
                <div class="mt-1 bg-blue-100 p-2 rounded-full text-blue-600"><i data-lucide="activity" class="w-4 h-4"></i></div>
                <div>
                    <p class="text-sm text-gray-800 font-medium">${log.action}</p>
                    <p class="text-xs text-gray-500 mt-1">By <span class="font-semibold">${log.user}</span> · ${log.created_at ? new Date(log.created_at).toLocaleString() : ''}</p>
                </div>
            </li>
        `).join('');
        lucide.createIcons();
    }
};
