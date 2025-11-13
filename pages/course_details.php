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
if (!$stmt_course) {
    echo "<div class='container mx-auto px-4 py-12'><p class='text-red-600'>Error preparing course query.</p></div>";
    include_once('../include/footer.php');
    exit();
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

    // --- Fetch Instructors for this course ---
    $instructors = [];
    $query_instructors = "SELECT s.first_name, s.last_name, s.profile_photo, s.position 
                      FROM staff s 
                      INNER JOIN instructor_courses ic ON s.staff_id = ic.staff_id 
                      WHERE ic.course_no = ? 
                      AND s.position IN ('Instructor', 'Senior Instructor') 
                      AND s.status = 'active'";
    $stmt_instructors = $con->prepare($query_instructors);
    if ($stmt_instructors) {
        $stmt_instructors->bind_param("s", $course['course_no']);
        $stmt_instructors->execute();
        $result_instructors = $stmt_instructors->get_result();
        while ($instructor = $result_instructors->fetch_assoc()) {
            $instructors[] = $instructor;
        }
        $stmt_instructors->close();
    }


    // --- Fetch Module Details for this course ---
    $modules = [];
    $query_modules = "SELECT m.module_name, m.order_no AS module_code 
                      FROM modules m
                      JOIN course c ON m.course_id = c.id
                      WHERE c.course_no = ? AND c.status = 'active'";
    $stmt_modules = $con->prepare($query_modules);
    if ($stmt_modules) {
        $stmt_modules->bind_param("s", $course['course_no']);
        $stmt_modules->execute();
        $result_modules = $stmt_modules->get_result();
        while ($module = $result_modules->fetch_assoc()) {
            $modules[] = $module;
        }
        $stmt_modules->close();
    }
} else {
    $course = null;
    $instructors = [];
    $modules = [];
}
$stmt_course->close();
?>
<?php
function getYoutubeEmbedUrl($url)
{
    if (empty(trim($url))) {
        return null;
    }

    // Check if it's already an embed link
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }

    $video_id = null;
    $host = parse_url($url, PHP_URL_HOST);

    if ($host == 'youtu.be') {
        // Handle short URL: https://youtu.be/VIDEO_ID?si=...
        // We get the PATH, not the query string
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $video_id = trim($path, '/');
        }
    } elseif (strpos($host, 'youtube.com') !== false) {
        // Handle standard URL: https://www.youtube.com/watch?v=VIDEO_ID&...
        $query_string = parse_url($url, PHP_URL_QUERY);
        if ($query_string) {
            parse_str($query_string, $query_params);
            if (isset($query_params['v'])) {
                $video_id = $query_params['v'];
            }
        }
    }

    if ($video_id) {
        // Clean up any extra parameters like &t=...
        $video_id_parts = explode('&', $video_id);
        $video_id = $video_id_parts[0];
        return 'https://www.youtube.com/embed/' . $video_id;
    }

    return null; // Not a valid YouTube link we can parse
}
?>

<?php
$sql = "SELECT 	course_video FROM course WHERE course_no = ?";
$stmt = $con->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $course_no_from_url);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();
    $embed_url = getYoutubeEmbedUrl($video['course_video']);

    $stmt->close();
}

?>

<?php
// --- ### START OF EFFICIENT LOGIC ### ---
//
// We already have the $course array from the first query.
// No need to query the database again.

$embed_url = null;
if ($course) {
    // Get the video link from the array we already fetched
    $embed_url = getYoutubeEmbedUrl($course['course_video']);
}
//
// --- ### END OF EFFICIENT LOGIC ### ---
//
?>

<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-12">

        <?php if ($course): // --- If course was found --- 
        ?>

            <div class="flex flex-col lg:flex-row gap-8">

                <div class="lg:w-2/3 w-full space-y-8">

                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                        <img src="<?php echo htmlspecialchars($image_src); ?>"
                            alt="<?php echo htmlspecialchars($course['course_name']); ?>"
                            class="w-full h-64 md:h-96 object-cover">
                    </div>

                    <?php if (!empty($modules)): ?>
                        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-900 mb-5 border-b border-gray-200 pb-3 tracking-tight">Module Details</h3>
                            <div class="space-y-4">
                                <?php foreach ($modules as $module): ?>
                                    <div class="flex justify-between items-start bg-gray-50 p-4 rounded-lg transition duration-200 hover:bg-gray-100">
                                        <div>
                                            <span class="font-medium text-indigo-600"><?php echo htmlspecialchars($module['module_code']); ?></span>
                                        </div>
                                        <div class="text-right pl-4">
                                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($module['module_name']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($instructors)): ?>
                        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-900 mb-5 border-b border-gray-200 pb-3 tracking-tight">Instructors</h3>
                            <div class="space-y-4">
                                <?php foreach ($instructors as $instructor): ?>
                                    <?php
                                    $instructor_photo = "https://placehold.co/100x100/718096/ffffff?text=" . substr($instructor['first_name'], 0, 1);
                                    if (!empty($instructor['profile_photo']) && file_exists('../uploads/profile_photos/' . $instructor['profile_photo'])) {
                                        $instructor_photo = '../uploads/profile_photos/' . $instructor['profile_photo'];
                                    }
                                    $instructor_name = htmlspecialchars($instructor['first_name'] . ' ' . $instructor['last_name']);
                                    ?>
                                    <div class="flex items-center space-x-4 p-2 rounded-lg transition duration-200 hover:bg-gray-50">
                                        <img src="<?php echo $instructor_photo; ?>" alt="<?php echo $instructor_name; ?>" class="w-16 h-16 rounded-full object-cover shadow-md border-2 border-gray-200">
                                        <div>
                                            <p class="text-lg font-semibold text-gray-800"><?php echo $instructor_name; ?></p>
                                            <p class="text-sm text-gray-500"><?= $instructor['position'] ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
                <div class="lg:w-1/3 w-full">


                    <!-- ========================================= -->
                    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg sticky top-28 border border-gray-200 space-y-6">

                        <?php if ($embed_url): ?>
                            <iframe
                                width="100%"
                                height="315"
                                src="<?php echo htmlspecialchars($embed_url); ?>"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen>
                            </iframe>
                        <?php else: ?>
                            <div class="w-full aspect-video bg-gray-200 flex items-center justify-center rounded-lg">
                                <p class="text-gray-500">Video is not available.</p>
                            </div>
                        <?php endif; ?>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-primary-blue leading-tight tracking-tight">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </h1>

                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h3 class="text-sm font-medium text-blue-700 uppercase tracking-wider mb-1">Course Fee</h3>
                            <p class="text-3xl font-extrabold text-blue-800">
                                <?php echo htmlspecialchars($course['course_fee']); ?>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Duration & Time</h3>
                            <p class="text-lg text-gray-700">
                                <?php echo htmlspecialchars($course['course_duration']); ?> Months (<?php echo htmlspecialchars($course['course_type']); ?>)
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h3>
                            <div class="text-gray-700 leading-relaxed space-y-2">
                                <?php echo nl2br(htmlspecialchars($course['course_description'])); ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Entry Qualifications</h3>
                            <div class="text-gray-700 leading-relaxed">
                                <?php echo htmlspecialchars($course['qualifications']); ?>
                            </div>
                        </div>

                        <a href="register.php"
                            class="block w-full text-center bg-green-600 text-white font-bold text-lg py-4 px-6 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 transform hover:scale-105">
                            Apply Now
                        </a>

                        <a href="all_course.php"
                            class="block w-full text-center bg-transparent text-gray-600 font-medium text-md py-3 px-6 rounded-lg border border-gray-300 hover:bg-gray-100 hover:border-gray-400 transition duration-300">
                            Back to Courses
                        </a>
                    </div>

                </div>
            </div> <?php else: // --- If no course was found --- 
                    ?>

            <div class="text-center bg-white p-12 rounded-2xl shadow-xl border border-gray-200">
                <h1 class="text-4xl font-extrabold text-red-600 mb-4">Course Not Found</h1>
                <p class="text-xl text-gray-600 mb-8">
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
    if (isset($con)) {
        $con->close();
    }
    include_once('../include/footer.php');
    ?>
</body>