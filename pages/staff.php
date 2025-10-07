<?php include_once('../include/header.php'); ?>
    <!-- Load Lucide Icons for professional look -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<body class="min-h-screen" onload="lucide.createIcons()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header Section -->
        <header class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-2">
                Meet Our Staff
            </h1>
            <p class="text-lg text-gray-600">Find the experts you need to connect with.</p>
        </header>

        <!-- Staff Profiles Grid (New Horizontal Card Layout - 2 Columns on Desktop) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Profile Card 1: Alice Johnson -->
            <a href="#" class="bg-white shadow-xl rounded-xl p-6 flex space-x-6 items-start transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl cursor-pointer border border-gray-50">
                
                <!-- Profile Image (Left Side) - Large rounded box (w-32 h-32) -->
                <img 
                    src="https://placehold.co/150x150/5DADE2/ffffff?text=A" 
                    alt="Alice Johnson" 
                    class="w-32 h-32 rounded-xl object-cover shadow-md flex-shrink-0"
                    onerror="this.onerror=null;this.src='https://placehold.co/150x150/5DADE2/ffffff?text=A';"
                >
                
                <!-- Details (Right Side) -->
                <div class="flex flex-col flex-grow">
                    <!-- Name and Title -->
                    <h2 class="text-xl font-bold text-gray-900 mb-0.5">Alice Johnson</h2>
                    <p class="text-md text-indigo-600 font-semibold mb-2">CEO & Founder</p>
                    
                    <!-- Bio was here, now removed for Alice Johnson -->

                    <!-- Contact (Restored) -->
                    <span class="flex items-center space-x-2 text-sm text-gray-700 mb-4">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                        <span class="truncate">alice.j@company.com</span>
                    </span>
                    
                    <!-- Action Link (Restored) -->
                    <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 mt-auto">
                        View Full Profile
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </span>
                </div>
            </a>
            
            <!-- Profile Card 2: Bob Williams -->
            <a href="#" class="bg-white shadow-xl rounded-xl p-6 flex space-x-6 items-start transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl cursor-pointer border border-gray-50">
                <img 
                    src="https://placehold.co/150x150/F4D03F/ffffff?text=B" 
                    alt="Bob Williams" 
                    class="w-32 h-32 rounded-xl object-cover shadow-md flex-shrink-0"
                    onerror="this.onerror=null;this.src='https://placehold.co/150x150/F4D03F/ffffff?text=B';"
                >
                <div class="flex flex-col flex-grow">
                    <h2 class="text-xl font-bold text-gray-900 mb-0.5">Bob Williams</h2>
                    <p class="text-md text-indigo-600 font-semibold mb-2">Chief Technology Officer</p>
                    
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                        Architecting and managing all scalable technical solutions for our global platform.
                    </p>

                    <span class="flex items-center space-x-2 text-sm text-gray-700 mb-4">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                        <span class="truncate">bob.w@company.com</span>
                    </span>
                    
                    <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 mt-auto">
                        View Full Profile
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </span>
                </div>
            </a>

            <!-- Profile Card 3: Carol Davis -->
            <a href="#" class="bg-white shadow-xl rounded-xl p-6 flex space-x-6 items-start transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl cursor-pointer border border-gray-50">
                <img 
                    src="https://placehold.co/150x150/2ECC71/ffffff?text=C" 
                    alt="Carol Davis" 
                    class="w-32 h-32 rounded-xl object-cover shadow-md flex-shrink-0"
                    onerror="this.onerror=null;this.src='https://placehold.co/150x150/2ECC71/ffffff?text=C';"
                >
                <div class="flex flex-col flex-grow">
                    <h2 class="text-xl font-bold text-gray-900 mb-0.5">Carol Davis</h2>
                    <p class="text-md text-indigo-600 font-semibold mb-2">Lead Designer</p>
                    
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                        Focus on user-centric aesthetics and seamless experiences across all products.
                    </p>

                    <span class="flex items-center space-x-2 text-sm text-gray-700 mb-4">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                        <span class="truncate">carol.d@company.com</span>
                    </span>
                    
                    <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 mt-auto">
                        View Full Profile
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </span>
                </div>
            </a>

            <!-- Profile Card 4: David Chen -->
            <a href="#" class="bg-white shadow-xl rounded-xl p-6 flex space-x-6 items-start transform transition duration-300 hover:scale-[1.01] hover:shadow-2xl cursor-pointer border border-gray-50">
                <img 
                    src="https://placehold.co/150x150/8E44AD/ffffff?text=D" 
                    alt="David Chen" 
                    class="w-32 h-32 rounded-xl object-cover shadow-md flex-shrink-0"
                    onerror="this.onerror=null;this.src='https://placehold.co/150x150/8E44AD/ffffff?text=D';"
                >
                <div class="flex flex-col flex-grow">
                    <h2 class="text-xl font-bold text-gray-900 mb-0.5">David Chen</h2>
                    <p class="text-md text-indigo-600 font-semibold mb-2">Senior Developer</p>
                    
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">
                        Building robust, clean codebases and optimizing performance for high traffic.
                    </p>

                    <span class="flex items-center space-x-2 text-sm text-gray-700 mb-4">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                        <span class="truncate">david.c@company.com</span>
                    </span>
                    
                    <span class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition duration-150 mt-auto">
                        View Full Profile
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </span>
                </div>
            </a>

        </div>
        <!-- End Staff Profiles Grid -->

    </div>

<?php include_once('../include/footer.php'); ?>
