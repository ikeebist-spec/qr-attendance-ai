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
    const list = document.getElementById('logs-list');
    const emptyState = document.getElementById('logs-empty');
    if (!list || !emptyState) return;

    const searchTerm = (document.getElementById('log-search-input')?.value || '').toLowerCase();
    
    // Filter logic
    const filteredLogs = window.activityLogs.filter(log => {
        if (!searchTerm) return true;
        return log.action.toLowerCase().includes(searchTerm) || 
               log.user.toLowerCase().includes(searchTerm);
    });

    if (filteredLogs.length === 0) {
        emptyState.classList.remove('hidden');
        list.classList.add('hidden');
        if (searchTerm) {
            emptyState.querySelector('p').innerText = `No logs found matching "${searchTerm}"`;
        } else {
            emptyState.querySelector('p').innerText = 'No recent activity to display.';
        }
    } else {
        emptyState.classList.add('hidden');
        list.classList.remove('hidden');
        
        list.innerHTML = filteredLogs.map(log => {
            // Determine styling based on action type
            let iconColor = 'bg-blue-100 text-blue-600';
            let actionText = log.action;

            if (actionText.includes('REJECTED SCAN')) iconColor = 'bg-red-100 text-red-600';
            if (actionText.includes('SUCCESSFUL SCAN')) iconColor = 'bg-green-100 text-green-600';
            if (actionText.includes('SECURITY ALERT')) iconColor = 'bg-orange-100 text-orange-600 border border-orange-200';

            return `
                <li class="px-6 py-4 hover:bg-gray-50 transition-colors flex items-start space-x-4 border-b border-gray-50">
                    <div class="mt-1 ${iconColor} p-2 rounded-full flex-shrink-0">
                        <i data-lucide="${actionText.includes('SCAN') ? 'qr-code' : 'activity'}" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800 font-medium">${window.escapeHTML(actionText)}</p>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded uppercase font-bold">${window.escapeHTML(log.user)}</span>
                            <span class="text-[10px] text-gray-400 font-mono">${log.created_at ? new Date(log.created_at).toLocaleString() : ''}</span>
                        </div>
                    </div>
                </li>
            `;
        }).join('');
        lucide.createIcons();
    }
};

// Add listener for search
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('log-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            window.renderLogs();
        });
    }
});
