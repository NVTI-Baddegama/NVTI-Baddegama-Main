<?php
// 1. Include Header and Database Connection
include_once('../include/header.php');
include_once('../include/connection.php');

// 2. Check if a 'course_no' is provided in the URL
if (!isset($_GET['course_no']) || empty($_GET['course_no'])) {
    header("Location: all_course.php");
    exit();
}

// 3. Get the Course Number and fetch course data
$course_no_from_url = trim($_GET['course_no']); // Use a distinct variable name

$query_course = "SELECT * FROM course WHERE course_no = ? AND status = 'active'";
$stmt_course = $con->prepare($query_course);
if(!$stmt_course){
    echo "<div class='container mx-auto px-4 py-12'><p class='text-red-600'>Error preparing course query.</p></div>";
    include_once('../include/footer.php'); exit();
}
$stmt_course->bind_param("s", $course_no_from_url);
$stmt_course->execute();
$result_course = $stmt_course->get_result();

// 4. Check if a course was found
if ($result_course->num_rows == 1) {
    $course = $result_course->fetch_assoc();

    // --- Handle Course Image ---
    $placeholder_text = !empty($course['course_name']) ? urlencode($course['course_name']) : 'Course';
    $default_image_url = 'https://placehold.co/1200x600/1e293b/9ca3af?text=' . $placeholder_text;
    $image_src = $default_image_url;
    if (!empty($course['course_image']) && file_exists('../uploads/course_images/' . $course['course_image'])) {
        $image_src = '../uploads/course_images/' . $course['course_image'];
    }

    // --- NEW: Fetch Instructors for this course ---
    $instructors = [];
    $query_instructors = "SELECT first_name, last_name, profile_photo FROM staff WHERE course_no = ? AND position = 'Instructors' AND status = 'active'";
    $stmt_instructors = $con->prepare($query_instructors);
    if($stmt_instructors){
        $stmt_instructors->bind_param("s", $course['course_no']); // Use course_no from the fetched course data
        $stmt_instructors->execute();
        $result_instructors = $stmt_instructors->get_result();
        while($instructor = $result_instructors->fetch_assoc()){
            $instructors[] = $instructor;
        }
        $stmt_instructors->close();
    }
    // --- END NEW ---

} else {
    $course = null;
    $instructors = []; // Ensure instructors array is empty if no course found
}
$stmt_course->close();
?>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-12">

        <?php if ($course): // --- If course was found --- ?>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <img src="<?php echo htmlspecialchars($image_src); ?>"
                 alt="<?php echo htmlspecialchars($course['course_name']); ?>"
                 class="w-full h-64 md:h-96 object-cover">
            <div class="p-6 md:p-10">
                <h1 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                    <?php echo htmlspecialchars($course['course_name']); ?>
                </h1>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-lg text-secondary-gray mb-6">
                    <span class="font-bold text-primary-blue">NVQ Level: <?php echo htmlspecialchars($course['nvq_level']); ?></span>
                    <span>Duration: <?php echo htmlspecialchars($course['course_duration']); ?> Months</span>
                    <span class="font-medium"><?php echo htmlspecialchars($course['course_type']); ?></span>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            <div class="lg:w-2/3 w-full bg-white p-6 md:p-8 rounded-2xl shadow-lg space-y-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Course Description</h3>
                    <div class="text-lg text-secondary-gray leading-relaxed space-y-4">
                        <?php echo nl2br(htmlspecialchars($course['course_description'])); ?>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Entry Qualifications</h3>
                    <div class="text-lg text-secondary-gray leading-relaxed">
                        <?php echo htmlspecialchars($course['qualifications']); ?>
                    </div>
                </div>

                <?php if (!empty($instructors)): ?>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Instructors</h3>
                    <div class="flex flex-wrap gap-6">
                        <?php foreach($instructors as $instructor): ?>
                            <?php
                                // Handle instructor photo
                                $instructor_photo = "https://placehold.co/100x100/718096/ffffff?text=" . substr($instructor['first_name'], 0, 1); // Default
                                if (!empty($instructor['profile_photo']) && file_exists('../uploads/profile_photos/' . $instructor['profile_photo'])) {
                                    $instructor_photo = '../uploads/profile_photos/' . $instructor['profile_photo'];
                                }
                                $instructor_name = htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']);
                            ?>
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo $instructor_photo; ?>" alt="<?php echo $instructor_name; ?>" class="w-16 h-16 rounded-full object-cover shadow-md border-2 border-gray-200">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900"><?php echo $instructor_name; ?></p>
                                    <p class="text-sm text-indigo-600">Instructor</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                </div>

            <div class="lg:w-1/3 w-full">
                <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg sticky top-28">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Course Fee</h3>
                    <p class="text-4xl font-extrabold text-primary-blue mb-6">
                        <?php echo htmlspecialchars($course['course_fee']); ?>
                    </p>

                    <a href="register.php"
                       class="block w-full text-center bg-green-600 text-white font-bold text-lg py-4 px-6 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 transform hover:scale-105">
                        Apply Now
                    </a>
                     <a href="all_course.php"
                           class="mt-4 block w-full text-center bg-gray-500 text-white font-bold text-lg py-4 px-6 rounded-lg shadow-lg hover:bg-gray-600 transition duration-300">
                            Back to Courses
                        </a>
                </div>
            </div>

        </div>

        <?php else: // --- If no course was found --- ?>

        <div class="text-center bg-white p-12 rounded-2xl shadow-xl">
            <h1 class="text-4xl font-extrabold text-red-600 mb-4">Course Not Found</h1>
            <p class="text-xl text-secondary-gray mb-8">
                We couldn't find the course you were looking for (Course No: <?php echo htmlspecialchars($course_no_from_url); ?>).
            </p>
            <a href="all_course.php"
               class="inline-block bg-primary-blue text-white font-bold text-lg py-3 px-8 rounded-lg shadow-lg hover:bg-blue-800 transition duration-300">
                Back to All Courses
            </a>
        </div>

        <?php endif; ?>

    </div>

<?php
// 5. Include the footer
if(isset($con)) { // Close connection if it was opened
    $con->close();
}
include_once('../include/footer.php');
?>
</body>