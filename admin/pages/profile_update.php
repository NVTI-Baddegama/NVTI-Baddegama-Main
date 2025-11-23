<?php
session_start();
include_once('../include/header.php');
include_once('../../include/connection.php');

// Ensure user is logged in
if (!isset($_SESSION['admin_username'])) {
    echo "<script>window.location.href='../../log/login.php';</script>";
    exit();
}

$admin_username_session = $_SESSION['admin_username'];
$user_type = 'unknown';
$user_data = [];
$identifier_value = ''; // To store what we use to identify the user (staff_id or username)

// 1. First, check if it's a STAFF ADMIN (using admin_id from session if available and matching staff table)
if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    $stmt = $con->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->bind_param("s", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_type = 'staff_admin';
        $user_data = $result->fetch_assoc();
        $identifier_value = $user_data['staff_id'];
    }
    $stmt->close();
}

// 2. If not found in staff, check ADMIN TABLE (using username)
if ($user_type === 'unknown') {
    $stmt = $con->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $admin_username_session);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_type = 'original_admin';
        $user_data = $result->fetch_assoc();
        $identifier_value = $user_data['username']; // Use username as the identifier
    }
    $stmt->close();
}
?>

<div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-4">Profile Update</h2>

    <?php
    if (isset($_SESSION['update_success'])) {
        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">';
        echo '<p class="font-bold">Success!</p>';
        echo '<p>' . htmlspecialchars($_SESSION['update_success']) . '</p>';
        echo '</div>';
        unset($_SESSION['update_success']);
    }
    if (isset($_SESSION['update_error'])) {
        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">';
        echo '<p class="font-bold">Error!</p>';
        echo '<p>' . htmlspecialchars($_SESSION['update_error']) . '</p>';
        echo '</div>';
        unset($_SESSION['update_error']);
    }
    ?>

    <div class="bg-white p-8 rounded-xl shadow-lg">
        <form action="../lib/update_profile_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($identifier_value); ?>">

            <?php if ($user_type === 'original_admin'): ?>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                    </div>
                </div>

            <?php elseif ($user_type === 'staff_admin'): ?>
                <div class="flex flex-col md:flex-row gap-8 items-start">
                    <div class="w-full md:w-1/3 text-center">
                        <div class="relative w-40 h-40 mx-auto mb-4">
                            <?php 
                                $photo_path = !empty($user_data['profile_photo']) ? "../../uploads/profile_photos/" . $user_data['profile_photo'] : "https://placehold.co/150";
                            ?>
                            <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Profile Photo" class="w-full h-full object-cover rounded-full border-4 border-gray-200 shadow-md">
                        </div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Change Photo</label>
                        <input type="file" name="profile_photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        <input type="hidden" name="old_profile_photo" value="<?php echo htmlspecialchars($user_data['profile_photo'] ?? ''); ?>">
                    </div>

                    <div class="w-full md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                            <input type="text" name="contact_no" value="<?php echo htmlspecialchars($user_data['contact_no']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" required>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center p-10">
                    <p class="text-red-500 text-xl font-bold">User profile not found.</p>
                    <p class="text-gray-600">Please try logging in again.</p>
                </div>
            <?php endif; ?>

            <?php if ($user_type !== 'unknown'): ?>
            <hr class="my-6 border-gray-200">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Change Password <span class="text-sm font-normal text-gray-500">(Leave blank to keep current password)</span></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="••••••••">
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150">
                    Update Profile
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php include_once('../include/footer.php'); ?>