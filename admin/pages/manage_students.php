<?php 
session_start(); // Start session to show messages
include_once('../include/header.php'); 
include_once('../../include/connection.php'); // Go back two folders

// --- 1. Get filter options (distinct courses from applications) ---
// We get the options from the applications themselves
$course_options_query = "SELECT DISTINCT course_option_one FROM student_enrollments WHERE course_option_one IS NOT NULL AND course_option_one != '' ORDER BY course_option_one ASC";
$course_options_result = $con->query($course_options_query);

// --- 2. Check for active filter ---
$filter_course = '';
if (isset($_GET['filter_course']) && !empty($_GET['filter_course'])) {
    $filter_course = trim($_GET['filter_course']);
}

// --- 3. Build main student query based on filter ---
if ($filter_course) {
    // Filtered query
    $query = "SELECT * FROM student_enrollments WHERE course_option_one = ? ORDER BY application_date DESC";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $filter_course);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Unfiltered (default) query
    $query = "SELECT * FROM student_enrollments ORDER BY application_date DESC";
    $result = $con->query($query);
}
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Student Enrollments</h2>

<?php
if (isset($_SESSION['success_msg'])) {
    echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">';
    echo '<p class="font-bold">Success!</p>';
    echo '<p>' . htmlspecialchars($_SESSION['success_msg']) . '</p>';
    echo '</div>';
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">';
    echo '<p class="font-bold">Error!</p>';
    echo '<p>' . htmlspecialchars($_SESSION['error_msg']) . '</p>';
    echo '</div>';
    unset($_SESSION['error_msg']);
}
?>

<div class="bg-white p-4 rounded-xl shadow-lg my-6">
    <form action="manage_students.php" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
        <div class="flex-grow">
            <label for="filter_course" class="block text-sm font-medium text-gray-700">Filter by Course (Choice 1):</label>
            <select id="filter_course" name="filter_course" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                <option value="">-- Show All Courses --</option>
                <?php
                if ($course_options_result && $course_options_result->num_rows > 0) {
                    while ($row = $course_options_result->fetch_assoc()) {
                        $course_val = htmlspecialchars($row['course_option_one']);
                        // Check if this is the currently selected filter
                        $selected = ($filter_course == $course_val) ? 'selected' : '';
                        echo "<option value=\"$course_val\" $selected>$course_val</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Filter</button>
            <a href="manage_students.php" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear</a>
        </div>
    </form>
</div>
<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
        <?php echo $filter_course ? 'Showing Applications for: ' . htmlspecialchars($filter_course) : 'All Applications'; ?>
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIC</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Choice 1</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                <?php
                // Check if there are any students
                if ($result && $result->num_rows > 0) {
                    // Loop through each student
                    while ($student = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($student['Student_id']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['nic']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['contact_no']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('Y-m-d', strtotime($student['application_date'])); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($student['is_processed'] == 1): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="view_student_details.php?nic=<?php echo htmlspecialchars($student['nic']); ?>" 
                           class="text-indigo-600 hover:text-indigo-900">
                           View Details
                        </a>
                    </td>
                </tr>
                <?php
                    } // End of while loop
                } else {
                    // Message if no students are found
                    echo '<tr><td colspan="8" class="px-6 py-12 text-center text-gray-500">No student applications found.</td></tr>';
                }
                
                // Close connection
                $con->close();
                ?>
                
            </tbody>
        </table>
    </div>
</div>

<?php include_once('../include/footer.php'); ?>