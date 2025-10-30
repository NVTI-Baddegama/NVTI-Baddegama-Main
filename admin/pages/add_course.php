<?php
// Start session for success/error messages
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once('../include/header.php'); // Include header first
include_once('../../include/connection.php'); // Need connection for fetching recent courses
// --- NEW: Fetch Categories for Dropdown ---
$categories = [];
$category_query = "SELECT * FROM course_categories ORDER BY category_name ASC";
$category_result = $con->query($category_query);
if ($category_result && $category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
// --- END NEW ---

// --- Fetch recently added courses ---
$recent_courses = [];
$query_recent = "SELECT course_no, course_name, nvq_level, course_duration, course_fee
                 FROM course
                 ORDER BY id DESC LIMIT 4";
$result_recent = $con->query($query_recent);
if ($result_recent && $result_recent->num_rows > 0) {
    while ($row = $result_recent->fetch_assoc()) {
        $recent_courses[] = $row;
    }
}
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Add New Course</h2>

<?php
if (isset($_SESSION['success'])) {
    echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert"><strong class="font-bold">Success!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['success']) . '</span></div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['error']) . '</span></div>';
    unset($_SESSION['error']);
}
?>


<div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-teal-500 w-full lg:w-3/4 mx-auto">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Enter Course Details</h3>

    <form action="../lib/course_handler.php" method="POST" class="mt-6" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label for="course_no" class="block text-sm font-medium text-gray-700 mb-1">Course Number</label>
                <input type="text" id="course_no" name="course_no" required maxlength="12"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., CS101">
            </div>

            <div>
                <label for="course_name" class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
                <input type="text" id="course_name" name="course_name" required maxlength="100"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., Introduction to Web Development">
            </div>

            <div>
                <label for="nvq_level" class="block text-sm font-medium text-gray-700 mb-1">NVQ Level</label>
                <select id="nvq_level" name="nvq_level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-white">
                    <option value="" disabled selected>Select a level</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 4</option>
                    <option value="5">Level 5</option>
                    <option value="6">Level 6</option>
                    <option value="7">Level 7</option>
                </select>
            </div>

            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-700 mb-1">Course Type</label>
                <select id="course_type" name="course_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-white">
                    <option value="" disabled selected>Select a type</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Online">Online</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>
            
            <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Course Category</label>
            <select id="category_id" name="category_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-white">
                <option value="" disabled selected>Select a category</option>
                <?php
                foreach ($categories as $category) {
                    echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['category_name']) . '</option>';
                }
                ?>
            </select>
        </div>


            <div class="md:col-span-2">
                <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-1">Qualifications Required</label>
                <input type="text" id="qualifications" name="qualifications" required maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., GCE O/L with 6 passes including Maths">
            </div>

            <div>
                <label for="course_duration" class="block text-sm font-medium text-gray-700 mb-1">Course Duration (Months)</label>
                <input type="number" id="course_duration" name="course_duration" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., 6">
            </div>

            <div>
                <label for="course_fee" class="block text-sm font-medium text-gray-700 mb-1">Course Fee</label>
                <input type="text" id="course_fee" name="course_fee" required maxlength="20"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., LKR 50,000 or Free">
            </div>

            <div class="md:col-span-2">
                <label for="course_image" class="block text-sm font-medium text-gray-700 mb-1">Course Card Image (Optional)</label>
                <input type="file" id="course_image" name="course_image" accept="image/jpeg, image/png, image/webp"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                       aria-describedby="image_help">
                <p class="mt-1 text-xs text-gray-500" id="image_help">Optional. Allowed: JPG, PNG, WEBP. Max 1MB.</p>
                <p id="course_image_error" class="text-red-500 text-xs mt-1"></p>
            </div>

            <div class="md:col-span-2">
                <label for="course_description" class="block text-sm font-medium text-gray-700 mb-1">Course Description</label>
                <textarea id="course_description" name="course_description" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Briefly describe the course content and objectives. Max 255 characters."></textarea>
            </div>
        </div>

        <div class="mt-8 text-right">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Save New Course
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8 w-full">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Recently Added Courses (Latest 4)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NVQ Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                if (!empty($recent_courses)) {
                    foreach ($recent_courses as $course) {
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($course['course_no']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Level <?php echo htmlspecialchars($course['nvq_level']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($course['course_duration']); ?> Months</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($course['course_fee']); ?></td>
                </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No courses added yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Close connection
if (isset($con)) {
    $con->close();
}
// Include footer AFTER the script
include_once('../include/footer.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseImageInput = document.getElementById('course_image');
    const courseImageError = document.getElementById('course_image_error');
    const maxFileSizeMB_Course = 1; // Max size in MB
    const maxFileSizeBytes_Course = maxFileSizeMB_Course * 1024 * 1024;

    if (courseImageInput) {
        courseImageInput.addEventListener('change', function(event) {
            if (courseImageError) courseImageError.textContent = '';
            const file = event.target.files[0];

            if (file) {
                if (file.size > maxFileSizeBytes_Course) {
                    if (courseImageError) courseImageError.textContent = `Error: File size exceeds ${maxFileSizeMB_Course}MB limit. Please choose a smaller file.`;
                    event.target.value = null; // Clear selection
                }
            }
        });

        const addCourseForm = courseImageInput.closest('form');
        if (addCourseForm) {
            addCourseForm.addEventListener('submit', function(event) {
                const file = courseImageInput.files[0];
                if (file && file.size > maxFileSizeBytes_Course) {
                    if (courseImageError) courseImageError.textContent = `Error: File size exceeds ${maxFileSizeMB_Course}MB limit. Cannot submit.`;
                    event.preventDefault(); // Stop submission
                }
            });
        }
    }
});
</script>