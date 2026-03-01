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
            sidebarOverlay.classList.toggle('show');
            sidebarOverlay.classList.toggle('hidden');
        } else {
            // Desktop: Toggle collapsed width
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
        }
    };

    const closeSidebar = () => {
        sidebar.classList.remove('sidebar-open');
        sidebarOverlay.classList.remove('show');
        sidebarOverlay.classList.add('hidden');
    };

    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarClose?.addEventListener('click', closeSidebar);
    sidebarOverlay?.addEventListener('click', closeSidebar);

    // Close mobile sidebar on window resize if it's over the breakpoint
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });

    // Handle Lucide icons after state changes
    if (window.lucide) {
        window.lucide.createIcons();
    }
});
