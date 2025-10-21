<?php session_start();?>
<?php 
include_once('../include/header.php'); 
include_once('../../include/connection.php'); // Include connection here

// --- NEW: Fetch courses from database ---
$courses = []; // Initialize an empty array
$courses_query = "SELECT course_no, course_name FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row; // Add each course to the array
    }
}
// --- END NEW ---
?>

<div class="conatainer flex justify-center py-10 md:py-16">
    <div class="w-full max-w-3xl bg-white shadow-2xl rounded-xl p-8 md:p-10 border border-gray-100">

        <header class="text-center mb-8 md:mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 tracking-tight">
                New Student Enrollment Application
            </h1>
            <p class="text-gray-500 mt-2 text-md">
                Please provide all required details accurately for processing your application.
            </p>
        </header>

        <?php 
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_GET['success'])): ?>
            <div id="successMessage"
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                <p class="font-bold">Application Received!</p>
                <p><?php echo htmlspecialchars(str_replace('_', ' ', $_GET['success'])); ?></p>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div id="errorMessage"
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                <p class="font-bold">Error!</p>
                <p><?php echo htmlspecialchars(str_replace('_', ' ', $_GET['error'])); ?></p>
            </div>
        <?php endif; ?>
        
        <form id="registrationForm" class="space-y-8" action="../lib/registerbacend.php" method="POST">
            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-semibold text-gray-700 mb-4">1. Personal Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required placeholder="A. B. C. Perera"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                    <div>
                        <label for="nic" class="block text-sm font-medium text-gray-700 mb-1">NIC</label>
                        <input type="text" id="nic" name="nic" required pattern="[0-9]{9}[vVxX]|[0-9]{12}" placeholder="901234567V or 200012345678"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                        <textarea id="address" name="address" rows="2" required placeholder="No. 123, Main Street, City"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 resize-none"></textarea>
                    </div>
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-semibold text-gray-700 mb-4">2. Contact Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contactNo" class="block text-sm font-medium text-gray-700 mb-1">Contact No (Required)</label>
                        <input type="tel" id="contactNo" name="contactNo" required pattern="[0-9]{10}" placeholder="07XXXXXXXX"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                    <div>
                        <label for="whatsappNo" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp No (Optional)</label>
                        <input type="tel" id="whatsappNo" name="whatsappNo" pattern="[0-9]{10}"
                            placeholder="Same as Contact No, if applicable"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-semibold text-gray-700 mb-4">3. Educational Qualifications</legend>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Did you pass G.C.E. O/L?</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input id="olPassYes" name="olPassStatus" type="radio" value="Yes" required
                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="olPassYes" class="ml-2 text-sm font-medium text-gray-700">Yes</label>
                        </div>
                        <div class="flex items-center">
                            <input id="olPassNo" name="olPassStatus" type="radio" value="No"
                                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <label for="olPassNo" class="ml-2 text-sm font-medium text-gray-700">No / Awaiting Results</label>
                        </div>
                    </div>
                </div>
                <div id="olSubjectsContainer" class="p-4 border border-gray-200 rounded-lg bg-gray-50 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-3">If passed O/L, grades:</p>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="olEnglish" class="block text-xs font-medium text-gray-600 mb-1">English</label>
                            <select id="olEnglish" name="olEnglish" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white appearance-none">
                                <option value="" selected>Grade</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="S">S</option><option value="F">F</option>
                            </select>
                        </div>
                        <div>
                            <label for="olMaths" class="block text-xs font-medium text-gray-600 mb-1">Maths</label>
                            <select id="olMaths" name="olMaths" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white appearance-none">
                                <option value="" selected>Grade</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="S">S</option><option value="F">F</option>
                            </select>
                        </div>
                        <div>
                            <label for="olScience" class="block text-xs font-medium text-gray-600 mb-1">Science</label>
                            <select id="olScience" name="olScience" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white appearance-none">
                                <option value="" selected>Grade</option><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="S">S</option><option value="F">F</option>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="mt-6">
                    <label for="alCategory" class="block text-sm font-medium text-gray-700 mb-1">G.C.E. A/L Stream/Status</label>
                    <select id="alCategory" name="alCategory" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 transition duration-150 appearance-none">
                        <option value="" disabled selected>Select A/L Stream/Status</option>
                        <option value="science">Science Stream (Passed)</option>
                        <option value="commerce">Commerce Stream (Passed)</option>
                        <option value="arts">Arts Stream (Passed)</option>
                        <option value="tech">Technology Stream (Passed)</option>
                        <option value="awaiting">Awaiting A/L Results</option>
                        <option value="failed">Failed A/L</option>
                        <option value="not_taken">No A/L Taken</option>
                    </select>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-semibold text-gray-700 mb-4">4. Course Application</legend>
                <div>
                    <label for="courseOptionOne" class="block text-sm font-medium text-gray-700 mb-1">Apply Course (Option One - Primary Choice)</label>
                    <select id="courseOptionOne" name="courseOptionOne" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 transition duration-150 appearance-none">
                        <option value="" disabled selected>Select your 1st Choice Course</option>
                        <?php 
                        // Loop through fetched courses
                        foreach ($courses as $course) {
                            echo '<option value="' . htmlspecialchars($course['course_name']) . '">' 
                                 . htmlspecialchars($course['course_name']) 
                                 . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mt-6">
                    <label for="courseOptionTwo" class="block text-sm font-medium text-gray-700 mb-1">Apply Course (Option Two - Secondary Choice)</label>
                    <select id="courseOptionTwo" name="courseOptionTwo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 transition duration-150 appearance-none">
                        <option value="" selected>Select your 2nd Choice Course (Optional)</option>
                         <?php 
                        // Loop through fetched courses again
                        foreach ($courses as $course) {
                            echo '<option value="' . htmlspecialchars($course['course_name']) . '">' 
                                 . htmlspecialchars($course['course_name']) 
                                 . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </fieldset>

            <div class="pt-6">
                <button type="submit" name="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-[1.01] active:scale-[0.98]">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../js/passcheck.js"></script> <?php 
// Close connection if opened
if (isset($con)) {
    $con->close(); 
}
include_once('../include/footer.php'); 
?>