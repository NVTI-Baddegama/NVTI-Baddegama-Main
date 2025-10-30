<?php
// Header includes connection.php, so connection is already available
include_once('../include/header.php');

// --- NEW: Filter Logic ---
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$category_name = "All Courses"; // Default title

// Base query
$query = "SELECT * FROM course WHERE status = 'active'";

if ($category_id > 0) {
    // If a category ID is provided, filter by it
    $query .= " AND category = ?";
    
    // Also get the category name for the title
    $cat_name_stmt = $con->prepare("SELECT category_name FROM course_categories WHERE id = ?");
    $cat_name_stmt->bind_param("i", $category_id);
    $cat_name_stmt->execute();
    $cat_name_result = $cat_name_stmt->get_result();
    if ($cat_name_result->num_rows > 0) {
        $category_name = $cat_name_result->fetch_assoc()['category_name'];
    }
    $cat_name_stmt->close();
}

$query .= " ORDER BY nvq_level DESC, course_name ASC";

// Prepare and execute the main query
$stmt = $con->prepare($query);
if ($category_id > 0) {
    $stmt->bind_param("i", $category_id);
}
$stmt->execute();
$result = $stmt->get_result();
// --- END NEW: Filter Logic ---
?>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-12">
        
        <header class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                <?php echo htmlspecialchars($category_name); // Dynamic Title ?>
            </h1>
            <?php if ($category_id > 0): // Show 'Back' link if filtering ?>
                 <a href="all_course.php" class="text-indigo-600 hover:underline">&larr; Back to All Courses</a>
            <?php else: // Show default subtitle if NOT filtering ?>
                <p class="text-lg md:text-xl text-secondary-gray max-w-2xl mx-auto">
                    Find the perfect course to build your future.
                </p>
            <?php endif; ?>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">

            <?php
            // Check if there are any courses to display
            if ($result && $result->num_rows > 0) {
                
                // Loop through each course from the database
                while ($course = $result->fetch_assoc()) {
            ?>

            <div class="flex flex-col bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 group">
                
                <div class="relative overflow-hidden">
                    <?php 
                        // --- ROBUST IMAGE HANDLING ---
                        $placeholder_text = !empty($course['course_name']) ? urlencode($course['course_name']) : 'Course';
                        $default_image_url = 'https://placehold.co/600x400/1e293b/9ca3af?text=' . $placeholder_text;
                        $image_src = $default_image_url;

                        if (!empty($course['course_image']) && file_exists('../uploads/course_images/' . $course['course_image'])) {
                            $image_path = '../uploads/course_images/' . $course['course_image'];
                             $image_src = $image_path;
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($image_src); ?>" 
                         alt="<?php echo htmlspecialchars($course['course_name']); ?>" 
                         class="w-full h-36 md:h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    
                    <span class="absolute top-2 right-2 inline-block rounded-md bg-green-600 px-2 py-1 text-xs font-semibold text-white shadow-md">
                        NVQ Lvl <?php echo htmlspecialchars($course['nvq_level']); ?>
                    </span>
                </div>

                <div class="p-4 md:p-5 flex flex-col flex-grow">
                    <p class="text-xs md:text-sm font-medium text-indigo-600 mb-1">
                        <?php echo htmlspecialchars($course['course_type']); ?>
                    </p>
                    
                    <h3 class="text-base md:text-lg font-bold text-gray-900 leading-tight mb-3">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </h3>
                    
                    <div class="flex items-center gap-2 text-xs md:text-sm text-gray-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span><?php echo htmlspecialchars($course['course_duration']); ?> Months</span>
                    </div>

                    <div class="mt-auto pt-4"> <a href="course_details.php?course_no=<?php echo htmlspecialchars($course['course_no']); ?>" 
                           class="block w-full rounded-lg bg-primary-blue text-white text-center font-semibold px-4 py-2.5 text-xs md:text-sm transition duration-300 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
            <?php
                } // End of the while loop
            } else {
                // Message to show if no courses are found
                echo '<p class="text-gray-600 col-span-full text-center py-10">No courses found in this category.</p>';
            }
            // Close statement and connection
            $stmt->close();
            if(isset($con)) {
                $con->close();
            }
            ?>

        </div>
    </div>

<?php 
// Include the footer
include_once('../include/footer.php'); 
?>
</body>