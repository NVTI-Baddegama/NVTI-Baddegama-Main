document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebar-toggle'); // Checkbox input
    const sidebar = document.getElementById('page-sidebar');         // The sidebar nav element
    const backdrop = document.getElementById('sidebar-backdrop');     // The overlay
    const sidebarToggleLabel = document.querySelector('label[for="sidebar-toggle"]'); // The menu button label

    // Tailwind classes for sidebar visibility
    const SIDEBAR_OPEN_CLASS = 'translate-x-0';
    const SIDEBAR_CLOSE_CLASS = '-translate-x-full';

    // Function to handle ONLY mobile sidebar toggling and backdrop
    const toggleMobileSidebar = (isOpen) => {
        if (window.innerWidth < 1024) { // Only run this logic on mobile (screens smaller than lg breakpoint)
            if (isOpen) {
                sidebar.classList.add(SIDEBAR_OPEN_CLASS);
                sidebar.classList.remove(SIDEBAR_CLOSE_CLASS);
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Lock background scroll
            } else {
                sidebar.classList.remove(SIDEBAR_OPEN_CLASS);
                sidebar.classList.add(SIDEBAR_CLOSE_CLASS); // Ensure it's hidden
                backdrop.classList.add('hidden');
                document.body.style.overflow = ''; // Restore background scroll
            }
        } else {
             // On desktop, ensure mobile backdrop is hidden and scroll is enabled
             backdrop.classList.add('hidden');
             document.body.style.overflow = '';
             // Desktop visibility is handled by CSS classes in header.php (lg:translate-x-0)
             // We ensure the base visibility class is correct in case it was toggled on mobile
             sidebar.classList.add(SIDEBAR_OPEN_CLASS); // Should be overridden by lg:translate-x-0 anyway
             sidebar.classList.remove(SIDEBAR_CLOSE_CLASS);
        }
    };

    // Function to close sidebar (used by backdrop click)
    const closeMobileSidebar = () => {
        if (window.innerWidth < 1024) { // Only relevant on mobile
           sidebarToggle.checked = false;
           toggleMobileSidebar(false);
        }
    }

    // Event listener for the checkbox (menu button controls this)
    sidebarToggle.addEventListener('change', (e) => {
        toggleMobileSidebar(e.target.checked);
    });

    // Event listener for the backdrop click
    backdrop.addEventListener('click', closeMobileSidebar);

    // Initial setup and resize handling
    const handleResize = () => {
         const isDesktop = window.innerWidth >= 1024;
         if(isDesktop) {
             // On desktop: ensure mobile styles are reset
             backdrop.classList.add('hidden');
             document.body.style.overflow = '';
             // Ensure sidebar has the base open class (CSS lg:translate-x-0 handles actual desktop state)
             sidebar.classList.add(SIDEBAR_OPEN_CLASS);
             sidebar.classList.remove(SIDEBAR_CLOSE_CLASS);
             // Ensure checkbox is unchecked to prevent weird state if resized back to mobile
             sidebarToggle.checked = false;

         } else {
             // On mobile: apply state based on current checkbox status
             toggleMobileSidebar(sidebarToggle.checked);
         }
    };

    // Run on initial load and on resize
    handleResize(); // Set initial state correctly based on screen size
    window.addEventListener('resize', handleResize);

});