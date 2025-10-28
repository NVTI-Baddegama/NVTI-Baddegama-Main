<?php
session_start();
include_once '../include/header.php';
include_once '../../include/connection.php';


// Get courses for dropdown
$courses = []; // Initialize
$courses_query = "SELECT course_no, course_name FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>


<link rel="stylesheet" href="../css/staff_register.css"> <div class="register-container"> <div class="register-header">
        <h2>Staff Registration</h2>
        <p>National Vocational Training Institute - Baddegama</p>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
     if (isset($_SESSION['success'])) { // Display success message if redirected back
        echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded" role="alert">';
        echo '<strong class="font-bold">Success!</strong> ';
        echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['success']) . '</span>';
        echo '</div>';
        unset($_SESSION['success']);
    }
    ?>

    <form action="../lib/register_process.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile_photo">Profile Photo (Optional, Max 1MB)</label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg, image/png, image/webp" class="file-input">
            <p id="photo_error" class="text-red-500 text-xs mt-1"></p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nic">NIC:</label>
                <input type="text" id="nic" name="nic" pattern="[0-9]{9}[vVxX]|[0-9]{12}" required>
            </div>
            <div class="form-group">
                <label for="service_id">Service ID:</label>
                <input type="text" id="service_id" name="service_id" required>
            </div>
        </div>

        <div class="form-group">
            <label>Gender:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="Male" required> Male
                </label>
                <label>
                    <input type="radio" name="gender" value="Female" required> Female
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="position">Position:</label>
            <select id="position" name="position" required onchange="toggleCourseSection()">
                <option value="">Select Position</option>
                <option value="Instructors">Instructors</option>
                <option value="Non-Academic Staff">Non-Academic Staff</option>
                <option value="Training Officer">Training Officer</option>
                <option value="Clerk">Clerk</option>
                <option value="Assistant Director">Assistant Director</option>
                </select>
        </div>

        <div class="form-group" id="course_section">
            <label for="course_no">Course (For Instructors):</label>
            <select id="course_no" name="course_no">
                <option value="">Select Course</option>
                <?php
                // Loop through fetched courses
                foreach ($courses as $course) {
                    echo '<option value="' . htmlspecialchars($course['course_no']) . '">' . htmlspecialchars($course['course_name']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                 <label for="password">Create Password (Leave blank for default: NVTI@staff123)</label>
                <input type="password" id="password" name="password" minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="6">
            </div>
        </div>

        <button type="submit" class="btn-register">Register Staff Member</button>
    </form>


</div>

<script>
    const profilePhotoInput = document.getElementById('profile_photo');
    const photoError = document.getElementById('photo_error');
    const maxFileSizeMB = 1; // Max size in MB
    const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024;

    profilePhotoInput.addEventListener('change', function(event) {
        photoError.textContent = ''; // Clear previous error
        const file = event.target.files[0];

        if (file) {
            if (file.size > maxFileSizeBytes) {
                photoError.textContent = `Error: File size exceeds ${maxFileSizeMB}MB limit. Please choose a smaller file.`;
                event.target.value = null; // Clear selection
            }
        }
    });

    // Optional: Form submission check
    const staffRegisterForm = profilePhotoInput.closest('form');
    if (staffRegisterForm) {
        staffRegisterForm.addEventListener('submit', function(event) {
            const file = profilePhotoInput.files[0];
            if (file && file.size > maxFileSizeBytes) {
                photoError.textContent = `Error: File size exceeds ${maxFileSizeMB}MB limit. Cannot submit.`;
                event.preventDefault(); // Stop submission
            }
            // Also re-validate password confirmation on submit
             const password = document.getElementById('password').value;
             const confirmPassword = document.getElementById('confirm_password').value;
             if (password !== confirmPassword) {
                 // You might want to display this error more prominently
                 alert('Passwords do not match!');
                 event.preventDefault();
             }
        });
    }


    function toggleCourseSection() {
        const positionSelect = document.getElementById('position');
        const selectedPosition = positionSelect.value;
        const courseSection = document.getElementById('course_section');
        const courseSelect = document.getElementById('course_no');

        // *** Define which positions NEED a course ***
        const academicPositions = ['Instructors']; // Add other positions like 'Training Officer' if they also need courses

        if (academicPositions.includes(selectedPosition)) {
            courseSection.style.display = 'block';
            courseSelect.required = true;
        } else {
            courseSection.style.display = 'none';
            courseSelect.required = false;
            courseSelect.value = '';
        }
    }

    // Initial call in case the page reloads with a position selected
    document.addEventListener('DOMContentLoaded', toggleCourseSection);

</script>

<?php
// Close connection
if (isset($con)) {
    $con->close();
}
include_once '../include/footer.php';
?>