<?php
// 1. Header එක include කිරීම
include_once('../include/header.php');

// -- Session Message Display --
// Handler එකෙන් redirect කළ විට පණිවිඩය පෙන්වීම
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['message']) && isset($_SESSION['message_type'])):
?>
<div class="container mx-auto px-4 mt-6">
    <div
        class="<?php echo $_SESSION['message_type'] == 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded relative"
        role="alert">
        <span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
    </div>
</div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
endif;
// -- End Session Message Display --


// --- Dropdown එක සඳහා Course ලැයිස්තුව ලබා ගැනීම ---
$courses = [];
if (isset($con)) {
    // 'course' table එකෙන් දත්ත ලබා ගැනීම
    $course_sql = "SELECT id, course_name FROM course WHERE status = 'active' ORDER BY course_name ASC";
    $course_result = $con->query($course_sql);
    if ($course_result && $course_result->num_rows > 0) {
        while ($row = $course_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}
// --- End Course Fetch ---
?>

<main class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">

        <div class="bg-white p-8 rounded-lg shadow-xl mb-12 max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-center text-primary mb-8">Submit Your Story</h2>
            
            <form action="../lib/submit_story_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">Your Position</label>
                        <input type="text" name="position" id="position"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" name="company_name" id="company_name"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course You Followed</label>
                    <select name="course_id" id="course_id"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select Your Course (Optional) --</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Your Story / Message</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label for="contact_details" class="block text-sm font-medium text-gray-700">Contact Details (Optional)</label>
                    <input type="text" name="contact_details" id="contact_details" placeholder="e.g., Phone number or Email"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube Video URL (Optional)</label>
                    <input type="url" name="youtube_url" id="youtube_url" placeholder="https://www.youtube.com/watch?v=..."
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label for="story_image" class="block text-sm font-medium text-gray-700">Your Photo (Optional)</label>
                    <input type="file" name="story_image" id="story_image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div class="text-center">
                    <button type="submit"
                        class="inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Story for Review
                    </button>
                </div>

            </form>
        </div>
        </div>
</main>

<?php
// 6. Footer එක include කිරීම
include_once('../include/footer.php');
?>