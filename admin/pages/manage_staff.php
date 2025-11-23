<?php
session_start(); // Start session for messages
include_once('../include/header.php');
include_once('../../include/connection.php'); // Go back two folders

// --- NEW: Check for active search ---
$search_service_id = '';
if (isset($_GET['search_service_id']) && !empty($_GET['search_service_id'])) {
    $search_service_id = trim($_GET['search_service_id']);
}

// --- NEW: Build main query based on search ---
$base_query = "SELECT * FROM staff";
$params = [];
$types = "";

if ($search_service_id) {
    // Search by service_id (using LIKE for partial match)
    $base_query .= " WHERE service_id LIKE ?";
    $params[] = "%" . $search_service_id . "%";
    $types .= "s";
}

$base_query .= " ORDER BY position ASC, first_name ASC";

// --- NEW: Prepare and execute the query ---
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
    $result = false; // Set result to false to avoid errors later
}

?>

<div class="flex justify-between" >

    <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Staff</h2>
    
    <div class="mb-6 text-right">
         <a href="staff_register.php"  class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md">
           <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
           Add New Staff
         </a>
    </div>

</div>
<?php
if (isset($_SESSION['staff_success_msg'])) {
    echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">';
    echo '<p class="font-bold">Success!</p>';
    echo '<p>' . htmlspecialchars($_SESSION['staff_success_msg']) . '</p>';
    echo '</div>';
    unset($_SESSION['staff_success_msg']);
}
if (isset($_SESSION['staff_error_msg'])) {
    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">';
    echo '<p class="font-bold">Error!</p>';
    echo '<p>' . htmlspecialchars($_SESSION['staff_error_msg']) . '</p>';
    echo '</div>';
    unset($_SESSION['staff_error_msg']);
}
?>



<div class="bg-white p-4 rounded-xl shadow-lg my-6 space-y-4">
    <form action="manage_staff.php" method="GET" class="flex flex-col sm:flex-row sm:items-end sm:gap-4">
        <div class="flex-grow">
            <label for="search_service_id" class="block text-sm font-medium text-gray-700">Search by Service ID:</label>
            <input type="text" id="search_service_id" name="search_service_id" value="<?php echo htmlspecialchars($search_service_id); ?>" placeholder="Enter Service ID (e.g., S001)"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-md">Search</button>
            <a href="manage_staff.php" class="w-full sm:w-auto text-center px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 shadow-md">Clear Search</a>
        </div>
    </form>
</div>

<div >
    <div class="bg-white p-6 rounded-tr-xl rounded-tl-xl shadow-lg mt-8 space-y-15">
    <!-- Flex container for Title and Export Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-center border-b pb-2  gap-4">
        <h3 class="text-xl font-semibold text-gray-800">
            <?php
                if ($search_service_id) {
                     echo 'Showing Staff matching Service ID: <strong>' . htmlspecialchars($search_service_id) . '</strong>';
                } else {
                    echo 'All Staff Members';
                }
            ?>
        </h3>
        
        <!-- Export Buttons -->
        <div class="flex gap-2">
            <!-- CSV Export Button -->
            <a href="../lib/export_staff_csv.php" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>

            <!-- PDF Export Button -->
            <a href="../lib/export_staff_pdf.php" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
            </a>
        </div>

    </div>
    </div>

</div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    <!-- New Column for Reset Password -->
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Reset Password</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                <?php
                // Check if there are any staff
                if ($result && $result->num_rows > 0) {
                    while ($staff = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($staff['service_id']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($staff['position']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($staff['status'] == 'active'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="view_staff_details.php?staff_id=<?php echo htmlspecialchars($staff['staff_id']); ?>" class="text-indigo-600 hover:text-indigo-900">
                           View / Edit
                        </a>
                    </td>
                    <!-- New Reset Password Button -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <a href="reset_password.php?staff_id=<?php echo htmlspecialchars($staff['staff_id']); ?>" 
                           onclick="return confirm('Are you sure you want to reset the password for <?php echo htmlspecialchars($staff['first_name']); ?>?');"
                           class="text-orange-600 hover:text-orange-900 border border-orange-200 px-2 py-1 rounded hover:bg-orange-50">
                           Reset PW
                        </a>
                    </td>
                </tr>
                <?php
                    } // End of while loop
                } else {
                     $no_results_message = "No staff members found.";
                    if ($search_service_id) {
                         $no_results_message = "No staff members found matching that Service ID.";
                    }
                    echo '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">' . $no_results_message . '</td></tr>';
                }
                 // Close statement and connection
                if ($stmt) $stmt->close();
                if (isset($con)) {
                    $con->close();
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

<?php include_once('../include/footer.php'); ?>