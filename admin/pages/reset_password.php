<?php
session_start();
// Move connection include to the top so we can use $con immediately
include_once('../../include/connection.php');

// 1. Handle the Password Reset Submission FIRST (Before any HTML output)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $staff_id_post = $_POST['staff_id'];
    
    // SET DEFAULT PASSWORD
    $default_password = 'NVTI@staff123';

    // Hash the password securely
    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

    // Update DB: Set new hashed password and reset login_status to 0
    $update_stmt = $con->prepare("UPDATE staff SET password = ?, login_status = 0 WHERE staff_id = ?");
    $update_stmt->bind_param("ss", $hashed_password, $staff_id_post);
    
    if ($update_stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Password reset to default (NVTI@staff123) for Service ID: " . htmlspecialchars($staff_id_post);
        // Redirect to Manage Staff Page (Since this is before HTML output, it will work now)
        header("Location: manage_staff.php");
        exit();
    } else {
        $error = "Database error: " . $con->error;
    }
    $update_stmt->close();
}

// 2. Include Header (HTML Output starts here)
include_once('../include/header.php');

$staff_id = '';
$error = isset($error) ? $error : ''; // Preserve error from POST if exists
$staff_details = null;

// 3. Check if we have a staff_id to work with for display
if (isset($_GET['staff_id'])) {
    $staff_id = trim($_GET['staff_id']);
    
    // Fetch details to display the old password and user info
    $stmt = $con->prepare("SELECT first_name, last_name, service_id, password FROM staff WHERE staff_id = ?");
    $stmt->bind_param("s", $staff_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $staff_details = $res->fetch_assoc();
    } else {
        $error = "Staff member not found.";
    }
    $stmt->close();
} else {
    // Only show this error if we aren't already handling a POST error
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && empty($error)) {
         $error = "No Staff ID provided.";
    }
}
?>

<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-xl shadow-lg border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Reset Password</h2>

    <!-- Error Display -->
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
            <p class="font-bold">Error</p>
            <p><?php echo htmlspecialchars($error); ?></p>
            <?php if(!$staff_details): ?>
                <a href="manage_staff.php" class="text-sm underline mt-2 block hover:text-red-800">Return to Staff List</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($staff_details || (isset($_POST['staff_id']) && !empty($_POST['staff_id']))): ?>
        
        <!-- Staff Info Card -->
        <?php if($staff_details): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6 rounded">
            <p class="text-xs text-orange-700 uppercase font-bold tracking-wide">Selected Staff Member</p>
            <div class="mt-1">
                <p class="text-lg text-gray-800 font-semibold">
                    <?php echo htmlspecialchars($staff_details['first_name'] . ' ' . $staff_details['last_name']); ?>
                </p>
                <p class="text-gray-600 text-sm font-mono">Service ID: <?php echo htmlspecialchars($staff_details['service_id']); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <form action="reset_password.php?staff_id=<?php echo htmlspecialchars($staff_id); ?>" method="POST" class="space-y-6">
            <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff_id); ?>">
            
           

            <!-- Default Password Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 text-sm">
                    <strong>Action:</strong> Clicking the button below will reset the password for this user to the default value.
                </p>
                <p class="text-blue-900 font-mono text-lg font-bold mt-2 text-center bg-blue-100 py-2 rounded">
                    NVTI@staff123
                </p>
                <p class="text-blue-600 text-xs mt-2 text-center">Login status will also be reset to 0.</p>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="manage_staff.php" class="text-gray-500 hover:text-gray-800 font-medium transition duration-150">
                    Cancel
                </a>
                <button type="submit" name="reset_password" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-150">
                    Reset to Default
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include_once('../include/footer.php'); ?>