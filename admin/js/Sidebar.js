document.addEventListener('DOMContentLoaded', () => {
            // --- 1. Sidebar Toggle Logic for Mobile and Desktop ---
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('page-sidebar'); 
            const backdrop = document.getElementById('sidebar-backdrop');
            const header = document.getElementById('page-header'); 
            const mainContent = document.getElementById('main-content'); 
            
            // Tailwind classes for shifting content when sidebar is open on desktop
            const SIDEBAR_WIDTH_CLASS = 'lg:ml-64';
            const HEADER_PADDING_CLASS = 'lg:pl-64';
            const SIDEBAR_OPEN_CLASS = 'translate-x-0';
            const SIDEBAR_CLOSE_CLASS = '-translate-x-full';

            const toggleSidebarClasses = (isOpen) => {
                // 1. Control sidebar visibility (translation)
                if (isOpen) {
                    sidebar.classList.add(SIDEBAR_OPEN_CLASS);
                    sidebar.classList.remove(SIDEBAR_CLOSE_CLASS);
                } else {
                    sidebar.classList.remove(SIDEBAR_OPEN_CLASS);
                    sidebar.classList.add(SIDEBAR_CLOSE_CLASS);
                }

                // 2. Control main content shift and header padding on desktop (lg breakpoint)
                if (window.innerWidth >= 1024) {
                    if (isOpen) {
                        mainContent.classList.add(SIDEBAR_WIDTH_CLASS);
                        header.classList.add(HEADER_PADDING_CLASS);
                    } else {
                        mainContent.classList.remove(SIDEBAR_WIDTH_CLASS);
                        header.classList.remove(HEADER_PADDING_CLASS);
                    }
                }
                
                // 3. Mobile-specific actions (backdrop and scrolling)
                if (window.innerWidth < 1024) {
                    if (isOpen) {
                        backdrop.classList.remove('hidden');
                        document.body.style.overflow = 'hidden'; // Lock scrolling
                    } else {
                        backdrop.classList.add('hidden');
                        document.body.style.overflow = ''; // Restore scrolling
                    }
                }
            };
            
            // Function to close the sidebar (used by backdrop and internal close button)
            const closeSidebar = () => {
                sidebarToggle.checked = false;
                toggleSidebarClasses(false);
            }

            // Toggle sidebar visibility using the checkbox change event
            sidebarToggle.addEventListener('change', (e) => {
                toggleSidebarClasses(e.target.checked);
            });

            // Close sidebar when backdrop is clicked (mobile requirement)
            backdrop.addEventListener('click', closeSidebar);

            // Handle initial state and resize events for layout correction
            const handleResize = () => {
                const isDesktop = window.innerWidth >= 1024;
                
                if (isDesktop) {
                    // On desktop, always hide mobile overlay
                    backdrop.classList.add('hidden');
                    document.body.style.overflow = '';

                    // Ensure desktop layout shifts if the sidebar is currently checked (open)
                    if (sidebarToggle.checked) {
                        mainContent.classList.add(SIDEBAR_WIDTH_CLASS);
                        header.classList.add(HEADER_PADDING_CLASS);
                    } else {
                        mainContent.classList.remove(SIDEBAR_WIDTH_CLASS);
                        header.classList.remove(HEADER_PADDING_CLASS);
                    }
                } else if (sidebarToggle.checked) {
                    // If resizing to mobile while sidebar is open, ensure mobile lock is active
                    document.body.style.overflow = 'hidden';
                    backdrop.classList.remove('hidden');
                }
            };

            // Call handleResize initially (to set the correct layout state) and on window resize
            handleResize();
            window.addEventListener('resize', handleResize);
            
        });