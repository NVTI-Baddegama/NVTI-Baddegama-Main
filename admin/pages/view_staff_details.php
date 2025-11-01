<?php
session_start();
include_once('../include/header.php');
include_once('../../include/connection.php');

// --- Fetch courses for the dropdown ---
$courses = [];
$courses_query = "SELECT course_no, course_name FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// 1. Check if Staff ID is provided
if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
    echo "<div class='p-6'><p class='text-red-500'>Error: No staff ID provided.</p></div>";
    include_once('../include/footer.php');
    exit();
}

// 2. Get the Staff ID and fetch data
$staff_id_to_view = trim($_GET['staff_id']);

$query = "SELECT * FROM staff WHERE staff_id = ?";
$stmt = $con->prepare($query);
if(!$stmt) {
     echo "<div class='p-6'><p class='text-red-500'>Error preparing query: " . $con->error . "</p></div>";
     include_once('../include/footer.php'); exit();
}
$stmt->bind_param("s", $staff_id_to_view);
$stmt->execute();
$result = $stmt->get_result();

// 3. Check if a staff member was found
if ($result->num_rows == 1) {
    $staff = $result->fetch_assoc();
    $old_image_name = $staff['profile_photo']; // Store old image name

    // --- Handle Profile Photo ---
    $photo_url = "https://placehold.co/150x150/a0aec0/ffffff?text=" . substr($staff['first_name'], 0, 1);
    if (!empty($staff['profile_photo']) && file_exists('../../uploads/profile_photos/' . $staff['profile_photo'])) {
        $photo_url = '../../uploads/profile_photos/' . $staff['profile_photo'];
    }
    // Enable course assignment for Instructor and Senior Instructor
    $is_instructor = in_array($staff['position'], ['Instructor', 'Senior Instructor']);
    

} else {
    echo "<div class='p-6'><p class='text-red-500'>Error: No staff member found with ID: " . htmlspecialchars($staff_id_to_view) . "</p></div>";
    $stmt->close();
    $con->close();
    include_once('../include/footer.php');
    exit();
}
$stmt->close();
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">View / Edit Staff Member Details</h2>

<div class="mb-6">
    <a href="manage_staff.php"
       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 shadow-md transition duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Staff List
    </a>
</div>

<?php
if (isset($_SESSION['staff_success_msg'])) {
    echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert"><strong class="font-bold">Success!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['staff_success_msg']) . '</span></div>';
    unset($_SESSION['staff_success_msg']);
}
if (isset($_SESSION['staff_error_msg'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['staff_error_msg']) . '</span></div>';
    unset($_SESSION['staff_error_msg']);
}
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        
        <div class="flex flex-col sm:flex-row items-center border-b pb-4 mb-6 gap-6">
            <img
                src="<?php echo $photo_url; ?>?v=<?php echo time(); // Cache buster ?>" 
                alt="<?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?>"
                class="w-32 h-32 rounded-full object-cover shadow-md flex-shrink-0 border-4 border-gray-200"
            >
            <div>
                <h3 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']); ?></h3>
                <p class="text-lg text-indigo-600 font-semibold"><?php echo htmlspecialchars($staff['position']); ?></p>
                <p class="text-sm text-gray-500 mt-1">Staff ID: <?php echo htmlspecialchars($staff['staff_id']); ?></p>
            </div>
        </div>
        
        <div class="space-y-4">
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Details</h4>
                <div class="mt-2 text-lg text-gray-800 space-y-1">
                    <p><strong>Service ID:</strong> <?php echo htmlspecialchars($staff['service_id']); ?></p>
                    <p><strong>NIC:</strong> <?php echo htmlspecialchars($staff['nic']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($staff['gender']); ?></p>
                    <p><strong>Contact No:</strong> <?php echo htmlspecialchars($staff['contact_no'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($staff['email'] ?? 'N/A'); ?></p>
                     <?php if(!empty($staff['course_no'])): ?>
                     <p><strong>Assigned Course No:</strong> <?php echo htmlspecialchars($staff['course_no']); ?></p>
                     <?php endif; ?>
                     <p><strong>Current Status:</strong>
                        <?php if ($staff['status'] == 'active'): ?>
                            <span class="font-semibold text-green-600">ACTIVE</span>
                        <?php else: ?>
                            <span class="font-semibold text-red-600">INACTIVE</span>
                        <?php endif; ?>
                     </p>
                     <p><strong>User Type:</strong> 
                        <?php if (isset($staff['type']) && $staff['type'] == 'admin'): ?>
                            <span class="font-bold text-red-600">ADMIN</span>
                        <?php else: ?>
                            <span class="font-semibold text-gray-600">Staff</span>
                        <?php endif; ?>
                     </p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white p-6 rounded-xl shadow-lg sticky top-28">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Update Staff Details</h3>

            <form action="../lib/staff_action_handler.php" method="POST" class="space-y-4" enctype="multipart/form-data">

                <input type="hidden" name="staff_id" value="<?php echo htmlspecialchars($staff['staff_id']); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="old_image_name" value="<?php echo htmlspecialchars($old_image_name ?? ''); ?>">
                <div class="form-group">

                <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position:</label>
                <select id="position" name="position" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required onchange="toggleCourseSection()">
                    <option value="">Select Position</option>
                    <option value="Assistant Director" <?php echo ($staff['position'] == 'Assistant Director') ? 'selected' : ''; ?>>Assistant Director</option>
                    <option value="Instructor" <?php echo ($staff['position'] == 'Instructor') ? 'selected' : ''; ?>>Instructor</option>
                    <option value="Senior Instructor" <?php echo ($staff['position'] == 'Senior Instructor') ? 'selected' : ''; ?>>Senior Instructor</option>
                    <option value="Demonstrator" <?php echo ($staff['position'] == 'Demonstrator') ? 'selected' : ''; ?>>Demonstrator</option>
                    <option value="Account Officer" <?php echo ($staff['position'] == 'Finance Officer') ? 'selected' : ''; ?>>Finance officer</option>
                    <option value="Testing Officer" <?php echo ($staff['position'] == 'Testing Officer') ? 'selected' : ''; ?>>Testing Officer</option>
                    <option value="Managemet Assistant" <?php echo ($staff['position'] == 'Managemet Assistant') ? 'selected' : ''; ?>>Managemet Assistant</option>
                    <option value="Training Officer" <?php echo ($staff['position'] == 'Training Officer') ? 'selected' : ''; ?>>Training Officer</option>
                    <option value="Program Officer" <?php echo ($staff['position'] == 'Program Officer') ? 'selected' : ''; ?>>Program Officer</option>
                    <option value="Driver" <?php echo ($staff['position'] == 'Driver') ? 'selected' : ''; ?>>Driver</option>
                    <option value="Labor" <?php echo ($staff['position'] == 'Labor') ? 'selected' : ''; ?>>Labor</option>
                    <option value="Security" <?php echo ($staff['position'] == 'Security') ? 'selected' : ''; ?>>Security</option>
                </select>

                <div>
                    <label for="contact_no" class="block text-sm font-medium text-gray-700 mb-1">Contact No</label>
                    <input type="tel" id="contact_no" name="contact_no" pattern="[0-9]{10}" placeholder="07XXXXXXXX" required
                           value="<?php echo htmlspecialchars($staff['contact_no'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                 <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="example@gmail.com" required
                           value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="course_no" class="block text-sm font-medium text-gray-700 mb-1">Assigned Course</label>
                    <select id="course_no" name="course_no"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 appearance-none
                            <?php if (!$is_instructor) echo 'bg-gray-100 cursor-not-allowed'; ?>"
                            <?php if (!$is_instructor) echo 'disabled'; ?> >
                        <option value="">-- No Course Assigned --</option>
                        <?php
                        foreach ($courses as $course) {
                            $selected = ($is_instructor && $staff['course_no'] == $course['course_no']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($course['course_no']) . '" ' . $selected . '>' 
                                 . htmlspecialchars($course['course_name']) . ' (' . htmlspecialchars($course['course_no']) . ')'
                                 . '</option>';
                        }
                        ?>
                    </select>
                    <?php if (!$is_instructor): ?>
                    <p class="text-xs text-red-500 mt-1">Courses can only be assigned to 'Instructors'.</p>
                    <?php endif; ?>
                </div>

                <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input id="status_active" name="status" type="radio" value="active" <?php echo ($staff['status'] == 'active') ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <label for="status_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                        </div>
                        <div class="flex items-center">
                            <input id="status_inactive" name="status" type="radio" value="inactive" <?php echo ($staff['status'] == 'inactive') ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <label for="status_inactive" class="ml-2 text-sm font-medium text-gray-700">Inactive</label>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Privileges</label>
                    <div class="flex items-center">
                        <input id="make_admin" name="admin_type" type="checkbox" value="admin"
                               <?php echo (isset($staff['type']) && $staff['type'] == 'admin') ? 'checked' : ''; ?>
                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="make_admin" class="ml-2 text-sm font-medium text-gray-900">Make this user an Admin</label>
                    </div>
                    <p class="text-xs text-red-500 mt-1">Warning: This gives the user full access to the Admin Panel.</p>
                </div>
                <div class="border-t pt-4">
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Update Profile Photo (Optional)</label>
                    <?php if (!empty($old_image_name)): ?>
                       <div class="flex items-center my-1">
                           <input type="checkbox" name="remove_image" id="remove_image" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                           <label for="remove_image" class="ml-2 text-xs text-red-600 font-medium">Remove current photo</label>
                       </div>
                    <?php endif; ?>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/webp"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                           aria-describedby="image_help">
                    <p class="mt-1 text-xs text-gray-500" id="image_help">Max 1MB. Uploading a new photo will replace the old one.</p>
                    <p id="photo_error" class="text-red-500 text-xs mt-1"></p>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Staff Details
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t pt-4">
                 <a href="../lib/staff_action_handler.php?action=delete&staff_id=<?php echo htmlspecialchars($staff['staff_id']); ?>"
                   class="block w-full text-center p-3 font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition duration-150"
                   onclick="return confirm('Are you sure you want to permanently delete this staff member? This action cannot be undone.');">
                    Delete Staff Member
                </a>
            </div>

        </div>
    </div>
</div>

<?php
if (isset($con)) { $con->close(); }
include_once('../include/footer.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePhotoInput_edit = document.getElementById('profile_photo');
    const photoError_edit = document.getElementById('photo_error');
    const maxFileSizeMB_edit = 1; // Max size in MB
    const maxFileSizeBytes_edit = maxFileSizeMB_edit * 1024 * 1024;

    // Toggle course select enabled/disabled based on position
    const positionSelect = document.getElementById('position');
    const courseSelect = document.getElementById('course_no');

    function toggleCourseSection() {
        if (!positionSelect || !courseSelect) return;
        const isInstructor = positionSelect.value === 'Instructor' || positionSelect.value === 'Senior Instructor';
        courseSelect.disabled = !isInstructor;
        if (isInstructor) {
            courseSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
            courseSelect.classList.add('bg-white');
        } else {
            courseSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            courseSelect.classList.remove('bg-white');
            // clear selection when not instructor
            if (courseSelect.value !== '') courseSelect.value = '';
        }
    }

    // Ensure initial state reflects server-side value
    if (positionSelect) {
        positionSelect.addEventListener('change', toggleCourseSection);
        // call once to set initial state (works even if server pre-disabled the control)
        toggleCourseSection();
    }

    if (profilePhotoInput_edit) {
        profilePhotoInput_edit.addEventListener('change', function(event) {
            if (photoError_edit) photoError_edit.textContent = '';
            const file = event.target.files[0];

            if (file) {
                if (file.size > maxFileSizeBytes_edit) {
                    if (photoError_edit) photoError_edit.textContent = `Error: File size exceeds ${maxFileSizeMB_edit}MB limit.`;
                    event.target.value = null; // Clear selection
                }
            }
        });

        const editStaffForm = profilePhotoInput_edit.closest('form');
        if (editStaffForm) {
            editStaffForm.addEventListener('submit', function(event) {
                const file = profilePhotoInput_edit.files[0];
                if (file && file.size > maxFileSizeBytes_edit) {
                    if (photoError_edit) photoError_edit.textContent = `Error: File size exceeds ${maxFileSizeMB_edit}MB limit. Cannot submit.`;
                    event.preventDefault(); // Stop submission
                }
            });
        }
    }
});
</script>