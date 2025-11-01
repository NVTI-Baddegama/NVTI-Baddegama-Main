<?php
// Start session if needed by other parts
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include connection to fetch categories
include_once(__DIR__ . '/connection.php'); // Use absolute path

// Fetch all categories for the dropdown
$categories_nav = [];
if (isset($con)) { // Check if connection exists
    $category_nav_query = "SELECT * FROM course_categories ORDER BY category_name ASC";
    $category_nav_result = $con->query($category_nav_query);
    if ($category_nav_result && $category_nav_result->num_rows > 0) {
        while($row = $category_nav_result->fetch_assoc()) {
            $categories_nav[] = $row;
        }
    }
    // We don't close $con here, as the page including this header will close it
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>NVTI Baddegama | Vocational Training & Courses</title>
    <meta name="description" content="Explore accredited vocational training programs at NVTI Baddegama. We offer courses in IT, Engineering, Hospitality, and more. Start your skilled career path today.">

    <link rel="icon" href="../images/favicon.png" type="image/png">
    
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="../css/style.css"> 
    
    <script src="../js/staff_script.js"></script> 
    <link rel="icon" href="../images/favicon.png" type="image/png">

</head>

<body>
    <nav class="nav-bg border-gray-200 sticky top-0 z-50 shadow-lg">
        <div class="max-w-screen-xl width-full flex flex-wrap items-center justify-between mx-auto p-4">
            <a href="../pages/index.php" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="../images/logo/NVTI_logo.png" class="h-8" alt="NVTI Baddegama Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">Baddegama</span>
            </a>

            <button data-collapse-toggle="navbar-dropdown" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-100 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200"
                aria-controls="navbar-dropdown" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">
                <ul
                    class="flex flex-col font-medium p-4 md:p-0 mt-4 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0">
                    <li>
                        <a href="../pages/index.php"
                            class="block py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 transition duration-300">Home</a>
                    </li>
                    <li>
                        <a href="../pages/about.php"
                            class="block py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 transition duration-300">About</a>
                    </li>
                    <li>
                        <a href="../pages/staff.php"
                            class="block py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 transition duration-300">Staff</a>
                    </li>
                    
                    <li>
                        <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar"
                            class="flex items-center justify-between w-full py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 md:w-auto transition duration-300">
                            Courses
                            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <div id="dropdownNavbar"
                            class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-56">
                            <ul class="py-2 text-sm text-gray-700"
                                aria-labelledby="dropdownNavbarLink">
                                
                                <?php if (!empty($categories_nav)): ?>
                                    <?php foreach ($categories_nav as $category): ?>
                                        <li>
                                            <a href="../pages/all_course.php?category_id=<?php echo $category['id']; ?>" class="block px-4 py-2 hover:bg-gray-100">
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                     <li><a href="#" class="block px-4 py-2 text-gray-400">No categories found</a></li>
                                <?php endif; ?>
                                
                                <li class="border-t border-gray-100 my-1"></li>
                                <li>
                                    <a href="../pages/all_course.php" class="block px-4 py-2 font-semibold text-indigo-600 hover:bg-gray-100">
                                        View All Courses
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../pages/gallery.php"
                            class="block py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 transition duration-300">Gallery</a>
                    </li>
                    <li>
                        <a href="../docs/index.php"
                            class="block py-2 px-3 text-white rounded nav-hover-border nav-link-with-border md:p-0 transition duration-300">Downloads</a>
                    </li>
                    <li>
                        <a href="../pages/register.php"
                            class="block py-2 px-3 rounded bg-white text-primary nav-hover-border nav-link-with-border md:border-0 md:p-0 transition duration-300 md:ml-2 register-cta">
                            Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<!-- 
    
-->
