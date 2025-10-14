<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Modern SaaS Panel</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Chart.js for the Enrollment Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="../css/webkit.css">
    <script src="../js/Theme.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Mobile Sidebar Toggle State (Used as the state controller for JS) -->
    <input type="checkbox" id="sidebar-toggle" class="hidden">

    <!-- HEADER (Top Navigation Bar) -->
    <!-- Removed fixed lg:pl-64 class -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white shadow-md z-30 flex items-center justify-between px-4 transition-all duration-300" id="page-header">
        <!-- Logo/Brand for Mobile -->
        <div class="lg:hidden text-lg font-bold text-secondary-dark">Admin Panel</div>
        
        <!-- Menu Button (Toggle Sidebar) -->
        <label for="sidebar-toggle" class="p-2 cursor-pointer text-gray-700 hover:text-primary-accent">
            <!-- Icon: Menu -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </label>

        <!-- Search Bar (Desktop) -->
        <div class="hidden lg:block w-full max-w-lg relative">
            <input type="text" placeholder="Search students, courses, or settings..." class="w-full py-2 pl-10 pr-4 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-accent transition duration-150">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <!-- User Profile and Settings -->
        <div class="flex items-center space-x-4">
            <!-- Notification Icon -->
            <button class="relative p-2 text-gray-600 hover:text-primary-accent transition duration-150 rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border border-white"></span>
            </button>

            <!-- User Profile Dropdown -->
            <div class="flex items-center space-x-3 cursor-pointer group">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-primary-accent" src="https://placehold.co/100x100/10b981/ffffff?text=AD" alt="Admin Profile">
                <div class="hidden sm:block text-sm">
                    <p class="font-semibold text-gray-800">Jane Doe</p>
                    <p class="text-xs text-gray-500">Administrator</p>
                </div>
                <!-- Settings Icon -->
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.568.354 1.258.426 1.836.216z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
        </div>
    </header>

    <!-- Sidebar and Backdrop -->
    <div id="sidebar-wrapper">
        <!-- Sidebar -->
        <!-- Removed lg:translate-x-0 class to hide it by default on desktop too -->
        <nav class="fixed top-0 bottom-0 left-0 w-64 bg-secondary-dark text-white z-40 transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl" id="page-sidebar">
            <!-- Sidebar Header & Close Button (Always visible inside sidebar) -->
            <div class="absolute top-0 left-0 w-full h-16 bg-slate-700 flex items-center justify-between px-4">
                <h1 class="text-xl font-extrabold text-white tracking-wider">LMS Admin</h1>
                <!-- Close Button (Mobile Only, but works on desktop when sidebar is shown) -->
                <label for="sidebar-toggle" class="p-2 cursor-pointer text-white hover:bg-white/10 rounded-full transition duration-150">
                    <!-- Icon: Close -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </label>
            </div>

            <div class="px-4 space-y-2 h-full overflow-y-auto pt-16">
                <!-- Menu Items -->
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="#" class="flex items-center p-3 text-sm font-semibold rounded-lg bg-primary-accent text-white shadow-lg shadow-primary-accent/30 transition duration-200">
                        <!-- Icon: Dashboard -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                    
                    <!-- Student Management -->
                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Students</p>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Add Student -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 18H8a2 2 0 00-2 2v1"></path></svg>
                        Add Student
                    </a>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Manage Students -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-5 0a2 2 0 100-4m0 4a2 2 0 110-4m-9-2h.01M7 12h.01M7 16h.01M4 20h4l-2 2h-2zm-3-4a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H3a2 2 0 01-2-2v-2z"></path></svg>
                        Manage Students
                    </a>

                    <!-- Instructor Management -->
                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Instructors</p>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Add Instructor -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm-6-4a6 6 0 00-4 0v1h8v-1m-4 0v-2"></path></svg>
                        Add Instructor
                    </a>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Manage Instructors -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5m-5 0a2 2 0 100-4m0 4a2 2 0 110-4m-9-2h.01M7 12h.01M7 16h.01M4 20h4l-2 2h-2zm-3-4a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H3a2 2 0 01-2-2v-2z"></path></svg>
                        Manage Instructors
                    </a>
                    
                    <!-- Course Management -->
                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Courses</p>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Add Course -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM21 21L3 21a2 2 0 01-2-2V5a2 2 0 012-2h18a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>
                        Add Course
                    </a>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Manage Courses -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM15.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM9.75 18.75a.75.75 0 110-1.5.75.75 0 010 1.5zM21 21L3 21a2 2 0 01-2-2V5a2 2 0 012-2h18a2 2 0 012 2v14a2 2 0 01-2 2z"></path></svg>
                        Manage Courses
                    </a>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Manage Modules -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Manage Modules
                    </a>
                    
                    <!-- Content & Settings -->
                    <p class="text-xs font-semibold text-gray-400 pt-3 pb-1 uppercase tracking-wider">Content & Media</p>
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-slate-700 hover:text-white transition duration-150">
                        <!-- Icon: Manage Images -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Manage Images
                    </a>

                    <!-- Logout Link -->
                    <a href="#" class="flex items-center p-3 text-sm font-medium rounded-lg text-red-400 hover:bg-slate-700 hover:text-red-300 transition duration-150 mt-8 !pt-6">
                        <!-- Icon: Logout -->
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </a>

                </div>
            </div>
        </nav>
        
        <!-- Mobile Backdrop Overlay -->
        <label for="sidebar-toggle" class="fixed inset-0 bg-black opacity-50 z-30 hidden" id="sidebar-backdrop"></label>
    </div>

    <!-- MAIN CONTENT AREA -->
    <!-- Removed lg:ml-64 class to start main content from the left -->
    <main class="pt-20 p-6 transition-all duration-300" id="main-content">