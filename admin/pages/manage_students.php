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

// --- 3. Check for active search (NOW by NIC) ---
$search_nic = '';
if (isset($_GET['search_nic']) && !empty($_GET['search_nic'])) {
    $search_nic = trim($_GET['search_nic']);
}

// --- 4. Build main student query based on filter AND search ---
$base_query = "SELECT * FROM student_enrollments"; // Select * to get the 'id'
$where_clauses = [];
$params = [];
$types = "";

// Add course filter
if ($filter_course) {
    $where_clauses[] = "course_option_one = ?";
    $params[] = $filter_course;
    $types .= "s";
}

// Add search filter (by NIC - using LIKE for partial match)
if ($search_nic) {
    $where_clauses[] = "nic LIKE ?"; // Changed from Student_id to nic
    $params[] = "%" . $search_nic . "%"; // Add wildcards
    $types .= "s";
}

// Combine WHERE clauses
if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$base_query .= " ORDER BY application_date DESC";

// Prepare and execute the query
$stmt = $con->prepare($base_query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
     echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">';
     echo '<p class="font-bold">Error!</p>';
     echo '<p>Failed to prepare database query: ' . $con->error . '</p>';
     echo '</div>';
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
                     // Reset pointer just in case it was used elsewhere
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
            <a href="manage_students.php?search_nic=<?php echo htmlspecialchars($search_nic); // Keep search if clearing filter ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear Filter</a>
        </div>
    </form>

    <form action="manage_students.php" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4 pt-4">
        <div class="flex-grow">
            <label for="search_nic" class="block text-sm font-medium text-gray-700">Search by NIC:</label> <input type="text" id="search_nic" name="search_nic" value="<?php echo htmlspecialchars($search_nic); ?>" placeholder="Enter NIC (e.g., 90...V or 2000...)" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <?php if ($filter_course): ?>
             <input type="hidden" name="filter_course" value="<?php echo htmlspecialchars($filter_course); ?>">
         <?php endif; ?>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md">Search</button>
            <a href="manage_students.php?filter_course=<?php echo htmlspecialchars($filter_course); // Keep filter if clearing search ?>" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear Search</a>
        </div>
    </form>

</div>

<!-- Enhanced Export Section with CSV and PDF options -->
<div class="bg-white p-4 rounded-xl shadow-lg my-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Export Student Data</h3>
            <p class="text-sm text-gray-600">Export the current filtered/searched student data in your preferred format</p>
        </div>
        <div class="flex gap-3">
            <?php
            // Build export URL with current filters
            $export_params = [];
            if ($filter_course) $export_params['filter_course'] = $filter_course;
            if ($search_nic) $export_params['search_nic'] = $search_nic;
            
            // CSV Export URL
            $csv_params = array_merge($export_params, ['export' => 'csv']);
            $csv_export_url = '../lib/export_students.php?' . http_build_query($csv_params);
            
            // PDF Export URL
            $pdf_params = array_merge($export_params, ['export' => 'pdf']);
            $pdf_export_url = '../lib/export_students.php?' . http_build_query($pdf_params);
            ?>
            
            <!-- CSV Export Button -->
            <a href="<?php echo htmlspecialchars($csv_export_url); ?>" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </a>
            
            <!-- PDF Export Button -->
            <a href="<?php echo htmlspecialchars($pdf_export_url); ?>" 
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </a>
        </div>
    </div>
    <?php if ($filter_course || $search_nic): ?>
        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                <strong>Export will include:</strong>
                <?php
                $export_info = [];
                if ($filter_course) $export_info[] = "Course: " . htmlspecialchars($filter_course);
                if ($search_nic) $export_info[] = "NIC search: " . htmlspecialchars($search_nic);
                echo implode(', ', $export_info);
                ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Export Format Information -->
    <!-- <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <strong>CSV:</strong> Spreadsheet format, includes Course Choice 2 & Address
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <strong>PDF:</strong> Formatted report with complete student details
            </div>
        </div>
    </div> -->
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
        <?php
            // Display current filter/search status
            $status_parts = [];
            if ($filter_course) $status_parts[] = 'Course: <strong>' . htmlspecialchars($filter_course) . '</strong>';
            if ($search_nic) $status_parts[] = 'Searching for NIC: <strong>' . htmlspecialchars($search_nic) . '</strong>'; // Changed text

            if (!empty($status_parts)) {
                 echo 'Showing Applications (' . implode(', ', $status_parts) . ')';
            } else {
                echo 'All Applications';
            }
        ?>
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIC</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Home Address</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Choice 1</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Choice 2</th>
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
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="<?php echo htmlspecialchars($student['address'] ?? ''); ?>"><?php echo htmlspecialchars($student['address'] ?? ''); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['course_option_one']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($student['course_option_two'] ?? ''); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('Y-m-d', strtotime($student['application_date'])); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($student['is_processed'] == 1): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="view_student_details.php?id=<?php echo htmlspecialchars($student['id']); ?>"
                           class="text-indigo-600 hover:text-indigo-900">
                           View Details
                        </a>
                    </td>
                </tr>
                <?php
                    } // End while loop
                } else {
                    $no_results_message = "No student applications found.";
                    if ($filter_course || $search_nic) {
                         $no_results_message = "No student applications found matching the current criteria.";
                    }
                    echo '<tr><td colspan="10" class="px-6 py-12 text-center text-gray-500">' . $no_results_message . '</td></tr>';
                }

                // Close statement and connection
                if ($stmt) $stmt->close();
                if (isset($con)) { // Check if connection still exists before closing
                    $con->close();
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

<?php include_once('../include/footer.php'); ?>