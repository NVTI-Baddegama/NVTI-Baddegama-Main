<?php 
include_once('../include/header.php'); // Oyage header eka include karanna
include_once('../include/connection.php'); // Database connection eka

// Database eken course okkoma ganna
$query = "SELECT * FROM course WHERE status = 'active' ORDER BY nvq_level DESC";
$result = $con->query($query);
?>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-12">
        
        <header class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                Our Courses
            </h1>
            <p class="text-xl text-secondary-gray max-w-2xl mx-auto">
                Find the perfect course to build your future.
            </p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            <?php
            // Check karanna courses thiyenawada kiyala
            if ($result && $result->num_rows > 0) {
                
                // --- PHP LOOP EKA PATAN GANNA ---
                // Eka eka course eka ganin $course variable ekata
                while ($course = $result->fetch_assoc()) {
            ?>

            <div class="relative rounded-2xl overflow-hidden shadow-xl group transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">

                <?php 
                    // Image eka thiyenawanam pennanna, naththam default ekak pennanna
                    $image_path = '../uploads/course_images/' . $course['course_image'];
                    if (empty($course['course_image']) || !file_exists($image_path)) {
                        // Default image ekak danna (placehold.co eken)
                        $image_src = 'https://placehold.co/600x400/1e293b/9ca3af?text=' . urlencode($course['course_name']);
                    } else {
                        $image_src = $image_path;
                    }
                ?>
                <img src="<?php echo $image_src; ?>" 
                     alt="<?php echo $course['course_name']; ?>" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

                <div class="relative flex flex-col justify-between h-full p-5 text-white" style="min-h: 320px;">
                    
                    <div class="flex justify-between items-center">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 backdrop-blur-sm px-3 py-1 text-xs font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            <?php echo $course['course_type']; ?>
                        </span>
                        
                        <span class="inline-block rounded-lg bg-primary-accent px-3 py-1 text-sm font-semibold text-white shadow-md">
                            NVQ Lvl <?php echo $course['nvq_level']; ?>
                        </span>
                    </div>

                    <div class="mt-auto">
                        <h3 class="text-2xl font-bold leading-tight mb-1 group-hover:text-primary-accent transition-colors">
                            <?php echo $course['course_name']; ?>
                        </h3>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-200 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            <span><?php echo $course['course_duration']; ?> Months</span>
                        </div>

                        <a href="course_details.php?id=<?php echo $course['id']; ?>" 
                           class="inline-block rounded-lg bg-white text-gray-900 font-medium px-5 py-2.5 text-sm transition duration-300 hover:bg-gray-200 focus:ring-4 focus:ring-white/50 transform hover:scale-105">
                           Learn More
                        </a>
                    </div>
                </div>
            </div>
            
            <?php
                } // --- PHP LOOP EKA IWASAI ---
            } else {
                // Courses nathi unoth pennana message eka
                echo '<p class="text-gray-600 col-span-3 text-center">No courses found at the moment. Please check back later.</p>';
            }
            ?>

        </div> </div>

<?php include_once('../include/footer.php'); // Oyage footer eka include karanna ?>
</body>