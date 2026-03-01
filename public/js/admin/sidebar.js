document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const body = document.body;

    // Load initial state from localStorage
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed && window.innerWidth >= 768) {
        body.classList.add('sidebar-collapsed');
    }

    const toggleSidebar = () => {
        if (window.innerWidth < 768) {
            // Mobile: Toggle off-canvas
            sidebar.classList.toggle('sidebar-open');
            if (sidebar.classList.contains('sidebar-open')) {
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => sidebarOverlay.classList.add('show'), 10);
            } else {
                closeSidebar();
            }
        } else {
            // Desktop: Toggle collapsed width
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
        }
    };

    window.closeSidebar = () => {
        sidebar.classList.remove('sidebar-open');
        sidebarOverlay.classList.remove('show');
        setTimeout(() => {
            if (!sidebarOverlay.classList.contains('show')) {
                sidebarOverlay.classList.add('hidden');
            }
        }, 300);
    };

    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarClose?.addEventListener('click', window.closeSidebar);
    sidebarOverlay?.addEventListener('click', window.closeSidebar);

    // Close mobile sidebar on window resize if it's over the breakpoint
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            window.closeSidebar();
        }
    });

    // Handle Lucide icons after state changes
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
