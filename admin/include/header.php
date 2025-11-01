<?php
// Start session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include connection using absolute path relative to this file's directory
// Do this only if needed directly in header, otherwise connection happens in pages
// include_once __DIR__ . '/../../include/connection.php'; // Usually not needed here

// --- Get Admin Details from Session ---
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : null;
$admin_display_name = 'User'; // Default display name
$admin_display_type = 'Guest'; // Default display type

// Fetch details ONLY IF username is set (Avoid DB call if not logged in)
if ($admin_username) {
    include_once __DIR__ . '/../../include/connection.php'; // Need connection here
    if (isset($con)) {
        $query_admin = "SELECT username, type FROM admin WHERE username = ?";
        $stmt_admin = $con->prepare($query_admin);
        if ($stmt_admin) {
            $stmt_admin->bind_param("s", $admin_username);
            $stmt_admin->execute();
            $result_admin = $stmt_admin->get_result();
            if ($result_admin->num_rows == 1) {
                $admin_data = $result_admin->fetch_assoc();
                $admin_display_name = htmlspecialchars($admin_data['username']);
                // Use the 'type' from DB, capitalize first letter for display
                $admin_display_type = htmlspecialchars(ucfirst($admin_data['type']));
            }
            $stmt_admin->close();
        } else {
             error_log("Admin header: Failed to prepare admin query: " . $con->error);
        }
         // Close connection only if opened here
         // $con->close(); // Better to close in footer or where appropriate
    } else {
        error_log("Admin header: Database connection not established in connection.php.");
    }
}


// --- ACCESS CHECK ---
// If NO admin username in session OR type wasn't 'admin' (assuming type check needed)
// NOTE: This check depends on the exact logic from previous step. If only login is needed, remove type check.
// Using $admin_type_from_db requires fetching it first, simplified below just checking login
if (!isset($_SESSION['admin_username'])) { // Simplified: Just check if logged in
    session_destroy();
    header("Location: ../../log/login.php?error=Please_Login");
    exit();
}
// Add type check back if strictly needed:
/*
if (!isset($_SESSION['admin_username']) || !isset($admin_type_from_db) || $admin_type_from_db !== 'admin') {
    session_destroy();
    header("Location: ../../log/login.php?error=Access_Denied_Invalid_Type");
    exit();
}
*/

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NVTI Baddegama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/webkit.css">
    <script src="../js/Theme.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <input type="checkbox" id="sidebar-toggle" class="hidden">

    <header class="fixed top-0 left-0 right-0 h-16 bg-white shadow-md z-30 flex items-center justify-between px-4 transition-all duration-300" id="page-header">
        <div class="lg:hidden text-lg font-bold text-gray-800">Admin Panel</div>

        <label for="sidebar-toggle" class="p-2 cursor-pointer text-gray-700 hover:text-green-600 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </label>

        <div class="hidden lg:block flex-grow"></div>

        <div class="flex items-center space-x-4 relative"> <button class="relative p-2 text-gray-600 hover:text-green-600 transition duration-150 rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border border-white"></span>
            </button>

            <button id="avatarButton" type="button" data-dropdown-toggle="userDropdown" data-dropdown-placement="bottom-start" class="flex items-center space-x-3 cursor-pointer group">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-green-500" src="https://placehold.co/100x100/10b981/ffffff?text=<?php echo strtoupper(substr($admin_display_name, 0, 1)); ?>" alt="Admin Profile">
                <div class="hidden sm:block text-sm text-left"> <p class="font-semibold text-gray-800"><?php echo $admin_display_name; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $admin_display_type; ?></p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg> </button>

            <div id="userDropdown" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                  <div><?php echo $admin_display_name; ?></div>
                  <div class="font-medium truncate"><?php echo $admin_display_type; ?></div>
                </div>
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="avatarButton">
                  <li>
                    <a href="add_admin.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Add New Admin</a>
                  </li>
                  <li>
                    <a href="change_password.php" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Change Password</a>
                  </li>
                </ul>
                <div class="py-1">
                  <a href="../lib/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Sign out</a>
                </div>
            </div>
            </div>
    </header>

    <div id="sidebar-wrapper">
        <nav class="fixed top-0 bottom-0 left-0 w-64 bg-slate-800 text-white z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl" id="page-sidebar">
            <div class="h-16 bg-slate-900 flex items-center justify-between px-4">
                <h1 class="text-xl font-extrabold text-white tracking-wider">Admin Panel</h1>
                <label for="sidebar-toggle" class="p-2 cursor-pointer text-white hover:bg-white/10 rounded-full transition duration-150 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </label>
            </div>

            <div class="px-4 space-y-2 h-full overflow-y-auto pt-4 pb-16">
                 <div class="space-y-1">
                    <a href="Dashboard.php" class="flex items-center p-3 text-sm font-semibold rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>

                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Students</p>
                    <a href="student_register.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 18H8a2 2 0 00-2 2v1"></path></svg>
                        Add Student
                    </a>
                    <a href="manage_students.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                         <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-5 0a2 2 0 100-4m0 4a2 2 0 110-4m-9-2h.01M7 12h.01M7 16h.01M4 20h4l-2 2h-2zm-3-4a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H3a2 2 0 01-2-2v-2z"></path></svg>
                        Manage Students
                    </a>

                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Staff</p>
                    <a href="staff_register.php"  class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm-6-4a6 6 0 00-4 0v1h8v-1m-4 0v-2"></path></svg>
                        Add New Staff
                    </a>
                    <a href="manage_staff.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-5 0a2 2 0 100-4m0 4a2 2 0 110-4m-9-2h.01M7 12h.01M7 16h.01M4 20h4l-2 2h-2zm-3-4a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H3a2 2 0 01-2-2v-2z"></path></svg>
                         Manage Staff
                    </a>

                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Courses</p>
                    <a href="add_course.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Add Course
                    </a>
                    <a href="manage_course.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM21 21L3 21a2 2 0 01-2-2V5a2 2 0 012-2h18a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>
                        Manage Courses
                    </a>
                     <a href="manage_modules.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Manage Modules
                    </a>

                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Content & Media</p>
                    <a href="manage images.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Manage Images
                    </a>
                    
                    <a href="mail_management.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Mail -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Mail Management
                    </a>

                    <a href="../lib/logout.php" class="flex items-center p-3 text-sm font-medium rounded-lg text-red-400 hover:bg-slate-700 hover:text-red-300 transition duration-150 mt-8 !pt-6">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </a>

                </div>
            </div>
        </nav>

        <label for="sidebar-toggle" class="fixed inset-0 bg-black opacity-50 z-30 hidden lg:hidden" id="sidebar-backdrop"></label>
    </div>

    <main class="pt-20 p-6 lg:ml-64 transition-all duration-300" id="main-content">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>