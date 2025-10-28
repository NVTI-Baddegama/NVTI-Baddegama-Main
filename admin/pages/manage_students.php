<?php
session_start(); // Start session to show messages
include_once('../include/header.php');
include_once('../../include/connection.php'); // Go back two folders

// --- 1. Get filter options (distinct courses) ---
$course_options_query = "SELECT DISTINCT course_option_one FROM student_enrollments WHERE course_option_one IS NOT NULL AND course_option_one != '' ORDER BY course_option_one ASC";
$course_options_result = $con->query($course_options_query);

// --- 2. Check for active filter ---
$filter_course = '';
if (isset($_GET['filter_course']) && !empty($_GET['filter_course'])) {
    $filter_course = trim($_GET['filter_course']);
}

// --- 3. Check for active search (by NIC) ---
$search_nic = '';
if (isset($_GET['search_nic']) && !empty($_GET['search_nic'])) {
    $search_nic = trim($_GET['search_nic']);
}

// --- 4. Build main student query based on filter AND search ---
$base_query = "SELECT * FROM student_enrollments"; // Select * to get the 'id'
$where_clauses = [];
$params = [];
$types = "";

if ($filter_course) {
    $where_clauses[] = "course_option_one = ?";
    $params[] = $filter_course;
    $types .= "s";
}
if ($search_nic) {
    $where_clauses[] = "nic LIKE ?";
    $params[] = "%" . $search_nic . "%";
    $types .= "s";
}

if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$base_query .= " ORDER BY application_date DESC";

$stmt = $con->prepare($base_query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
     echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert"><p class="font-bold">Error!</p><p>Failed to prepare database query: ' . $con->error . '</p></div>';
     $result = false;
}

?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Student Enrollments</h2>

<?php
if (isset($_SESSION['success_msg'])) {
    echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert"><p class="font-bold">Success!</p><p>' . htmlspecialchars($_SESSION['success_msg']) . '</p></div>';
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert"><p class="font-bold">Error!</p><p>' . htmlspecialchars($_SESSION['error_msg']) . '</p></div>';
    unset($_SESSION['error_msg']);
}
?>

<div class="bg-white p-4 rounded-xl shadow-lg my-6 space-y-4">
    <form action="manage_students.php" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4 border-b pb-4">
        <div class="flex-grow">
            <label for="filter_course" class="block text-sm font-medium text-gray-700">Filter by Course (Choice 1):</label>
            <select id="filter_course" name="filter_course" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                <option value="">-- Show All Courses --</option>
                <?php
                if ($course_options_result && $course_options_result->num_rows > 0) {
                     $course_options_result->data_seek(0);
                    while ($row = $course_options_result->fetch_assoc()) {
                        $course_val = htmlspecialchars($row['course_option_one']);
                        $selected = ($filter_course == $course_val) ? 'selected' : '';
                        echo "<option value=\"$course_val\" $selected>$course_val</option>";
                    }
                }
                ?>
            </select>
        </div>
         <?php if ($search_nic): ?>
             <input type="hidden" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>">
         <?php endif; ?>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md">Filter</button>
            <a href="manage_students.php?search_nic=<?php echo htmlspecialchars($search_nic); ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear Filter</a>
        </div>
    </form>
    <form action="manage_students.php" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4 pt-4">
        <div class="flex-grow">
            <label for="search_nic" class="block text-sm font-medium text-gray-700">Search by NIC:</label>
            <input type="text" id="search_nic" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>" placeholder="Enter NIC (e.g., 90...V or 2000...)"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
         <?php if ($filter_course): ?>
             <input type="hidden" name="filter_course" value="<?php echo htmlspecialchars($filter_course); ?>">
         <?php endif; ?>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md">Search</button>
            <a href="manage_students.php?filter_course=<?php echo htmlspecialchars($filter_course); ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear Search</a>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
        <?php /* ... (Status display logic remains same) ... */ ?>
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
                if ($result && $result->num_rows > 0) {
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
                        <?php /* ... (Status span code remains same) ... */ ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="view_student_details.php?id=<?php echo htmlspecialchars($student['id']); ?>" 
                           class="text-indigo-600 hover:text-indigo-900">
                           View Details
                        </a>
                    </td>
                </tr>
                <?php
                    } // End while
                } else {
                    // ... (No results message remains same) ...
                }
                if ($stmt) $stmt->close();
                $con->close();
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once('../include/footer.php'); ?>