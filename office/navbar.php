<?php
// Get current staff details for navbar
$staff_query = "SELECT * FROM staff WHERE staff_id = ?";
$staff_stmt = $con->prepare($staff_query);
$staff_stmt->bind_param("s", $_SESSION['staff_id']);
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();
$current_staff = $staff_result->fetch_assoc();

// Determine the correct dashboard link based on position
$dashboard_link = "dashboard.php";
$panel_title = "NVTI Baddegama";

if (isset($_SESSION['position'])) {
    if ($_SESSION['position'] === 'Instructor' || $_SESSION['position'] === 'Senior Instructor') {
        $panel_title = "NVTI Baddegama - Instructor Panel";
    } else {
        $panel_title = "NVTI Baddegama - Staff Panel";
    }
}
?>

<nav class="bg-gray-900 border-gray-700 shadow-lg sticky top-0 z-50">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-3 sm:p-4">
        <!-- Logo and Title -->
        <a href="<?php echo $dashboard_link; ?>" class="flex items-center space-x-2 sm:space-x-3">
            <img src="../images/logo/NVTI_logo.png" class="h-8 sm:h-12" alt="NVTI Logo" />
            <span class="self-center text-sm sm:text-xl font-semibold whitespace-nowrap text-white">
                <span class="hidden sm:inline"><?php echo $panel_title; ?></span>
                <span class="sm:hidden">NVTI</span>
            </span>
        </a>
        
        <!-- User Profile Section -->
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Desktop User Info (Visible on all screens) -->
            <div class="flex flex-col items-end">
                <span class="text-sm font-medium text-white truncate max-w-[150px] sm:max-w-[200px]">
                    <?php echo htmlspecialchars($current_staff['first_name'] . ' ' . $current_staff['last_name']); ?>
                </span>
                <span class="text-xs text-gray-300 truncate max-w-[150px] sm:max-w-[200px]">
                    <?php echo htmlspecialchars($current_staff['position']); ?>
                </span>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="relative">
                <button type="button" 
                        class="flex items-center text-sm bg-gray-700 rounded-full focus:ring-4 focus:ring-gray-600 transition-all duration-200 hover:ring-2 hover:ring-gray-500 border-2 border-gray-600" 
                        id="user-menu-button" 
                        aria-expanded="false">
                    <span class="sr-only">Open user menu</span>
                    <?php if (!empty($current_staff['profile_photo']) && file_exists("../uploads/profile_photos/" . $current_staff['profile_photo'])): ?>
                        <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover" 
                             src="../uploads/profile_photos/<?php echo htmlspecialchars($current_staff['profile_photo']); ?>" 
                             alt="Profile Photo">
                    <?php else: ?>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm sm:text-base">
                            <?php echo strtoupper(substr($current_staff['first_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </button>
                
                <!-- Dropdown menu -->
                <div class="z-50 hidden absolute right-0 mt-2 w-72 bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-gray-200" id="user-dropdown">
                    <!-- User Info Section with Profile Image -->
                    <div class="px-4 py-4 bg-gray-50 rounded-t-lg">
                        <div class="flex items-center space-x-4">
                            <!-- Profile Image in Dropdown -->
                            <div class="flex-shrink-0">
                                <?php if (!empty($current_staff['profile_photo']) && file_exists("../uploads/profile_photos/" . $current_staff['profile_photo'])): ?>
                                    <img class="w-16 h-16 rounded-full object-cover border-3 border-gray-300 shadow-md" 
                                         src="../uploads/profile_photos/<?php echo htmlspecialchars($current_staff['profile_photo']); ?>" 
                                         alt="Profile Photo">
                                <?php else: ?>
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-xl border-3 border-gray-300 shadow-md">
                                        <?php echo strtoupper(substr($current_staff['first_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- User Details -->
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-900 truncate">
                                    <?php echo htmlspecialchars($current_staff['first_name'] . ' ' . $current_staff['last_name']); ?>
                                </p>
                                <p class="text-sm text-gray-600 truncate">
                                    <?php echo htmlspecialchars($current_staff['email']); ?>
                                </p>
                                <p class="text-xs text-gray-500 font-medium">
                                    <?php echo htmlspecialchars($current_staff['position']); ?>
                                </p>
                                <?php if (!empty($current_staff['staff_type'])): ?>
                                <p class="text-xs text-blue-600 font-medium">
                                    <?php echo htmlspecialchars($current_staff['staff_type']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <ul class="py-2">
                        <li>
                            <a href="#" 
                               onclick="openProfileModal()" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200">
                                <i class="fas fa-user-edit mr-3 text-blue-500 w-5"></i>
                                <span class="font-medium">Profile Update</span>
                            </a>
                        </li>
                        <li>
                            <hr class="my-1 border-gray-200">
                        </li>
                        <li>
                            <a href="logout.php" 
                               class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200"
                               onclick="return confirm('Are you sure you want to sign out?')">
                                <i class="fas fa-sign-out-alt mr-3 text-red-500 w-5"></i>
                                <span class="font-medium">Sign Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Mobile menu button (if needed for future expansion) -->
            <button data-collapse-toggle="navbar-user" 
                    type="button" 
                    class="hidden items-center p-2 w-10 h-10 justify-center text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 transition-colors duration-200" 
                    aria-controls="navbar-user" 
                    aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

<script>
// Enhanced dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userMenuButton && userDropdown) {
        // Toggle dropdown on button click
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            
            // Update aria-expanded
            const isExpanded = !userDropdown.classList.contains('hidden');
            userMenuButton.setAttribute('aria-expanded', isExpanded);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
                userMenuButton.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !userDropdown.classList.contains('hidden')) {
                userDropdown.classList.add('hidden');
                userMenuButton.setAttribute('aria-expanded', 'false');
                userMenuButton.focus();
            }
        });
        
        // Handle keyboard navigation in dropdown
        userDropdown.addEventListener('keydown', function(event) {
            const focusableElements = userDropdown.querySelectorAll('a[href], button');
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (event.key === 'Tab') {
                if (event.shiftKey) {
                    if (document.activeElement === firstElement) {
                        event.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        event.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }
    
    // Mobile menu toggle (for future use)
    const mobileMenuButton = document.querySelector('[data-collapse-toggle="navbar-user"]');
    const mobileMenu = document.getElementById('navbar-user');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

// Add smooth transitions and animations
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add loading animation for profile image
    const profileImages = document.querySelectorAll('img[src*="profile_photos"]');
    profileImages.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '0';
            this.style.transition = 'opacity 0.3s ease-in-out';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 50);
        });
        
        // Handle image load errors
        img.addEventListener('error', function() {
            console.log('Failed to load profile image:', this.src);
            // The fallback div will be shown instead
        });
    });
});
</script>

<style>
/* Enhanced dark navbar styles */
.navbar-dark {
    backdrop-filter: blur(10px);
    background-color: rgba(17, 24, 39, 0.95);
}

/* Dropdown animation */
#user-dropdown {
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s ease-in-out;
    pointer-events: none;
}

#user-dropdown:not(.hidden) {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

/* Profile image hover effect */
#user-menu-button img,
#user-menu-button div {
    transition: all 0.2s ease-in-out;
}

#user-menu-button:hover img,
#user-menu-button:hover div {
    transform: scale(1.05);
}

/* Menu item hover effects */
#user-dropdown a {
    position: relative;
    overflow: hidden;
}

#user-dropdown a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: width 0.3s ease;
}

#user-dropdown a:hover::before {
    width: 100%;
}

/* Profile image border styles */
.border-3 {
    border-width: 3px;
}

/* Enhanced profile section styling */
.profile-section {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    #user-dropdown {
        width: calc(100vw - 2rem);
        right: 1rem;
        left: auto;
        margin-right: 0;
    }
    
    /* Mobile user info styling */
    .mobile-user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-right: 0.5rem;
    }
}

/* Focus styles for accessibility */
#user-menu-button:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

#user-dropdown a:focus {
    outline: 2px solid #3b82f6;
    outline-offset: -2px;
}

/* Dark theme enhancements */
.bg-gray-900 {
    background-color: #111827;
}

/* Profile image loading state */
img[src*="profile_photos"] {
    background-color: #f3f4f6;
}

/* Enhanced shadow for profile images */
.shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* User info text truncation */
.max-w-\[150px\] {
    max-width: 150px;
}
.max-w-\[200px\] {
    max-width: 200px;
}

/* Fix for mobile dropdown positioning */
@media (max-width: 640px) {
    #user-dropdown {
        position: fixed;
        top: auto;
        bottom: 0;
        left: 1rem;
        right: 1rem;
        width: auto;
        margin: 0;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    
    #user-dropdown:not(.hidden) {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>