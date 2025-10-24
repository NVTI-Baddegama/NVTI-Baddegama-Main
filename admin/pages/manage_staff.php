<?php
session_start(); // Start session for potential messages
include_once('../include/header.php');
include_once('../../include/connection.php'); // Go back two folders

// Fetch all staff, ordered by position then name
$query = "SELECT * FROM staff ORDER BY position ASC, first_name ASC";
$result = $con->query($query);
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Staff</h2>

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

<div class="mb-6 text-right">
     <a href="../../log/register.php" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md">
       <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
       Add New Staff
     </a>
</div>


<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">All Staff Members</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                <?php
                // Check if there are any staff
                if ($result && $result->num_rows > 0) {
                    while ($staff = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($staff['service_id']); ?></td>
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
                </tr>
                <?php
                    } // End of while loop
                } else {
                    echo '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No staff members found. Add new staff using the button above.</td></tr>';
                }
                 // Close connection
                $con->close();
                ?>

            </tbody>
        </table>
    </div>
</div>

<?php include_once('../include/footer.php'); ?>