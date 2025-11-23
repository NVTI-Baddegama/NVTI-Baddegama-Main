<?php
session_start();
include_once '../include/header.php';
include_once '../../include/connection.php';

// Get courses for dropdown
$courses = [];
$courses_query = "SELECT course_no, course_name FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
if ($courses_result && $courses_result->num_rows > 0) {
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<link rel="stylesheet" href="../css/staff_register.css">
<div class="register-container">
    <div class="register-header">
        <h2>Staff Registration</h2>
        <p>National Vocational Training Institute - Baddegama</p>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
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
        
        <div class="form-row">
            <div class="form-group">
                <label for="contact_no">Contact No:</label>
                <input type="tel" id="contact_no" name="contact_no" pattern="[0-9]{10}" placeholder="07XXXXXXXX" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
            </div>
        </div>

        <div class="form-group">
            <label for="position">Position:</label>
            <select id="position" name="position" required onchange="toggleCourseSection()">
                <option value="">Select Position</option>
                <option value="Assistant Director">Assistant Director</option>
                <option value="Instructor">Instructor</option>
                <option value="Assistant Instructor">Assistant Instructor</option>
                <option value="Senior Instructor">Senior Instructor</option>
                <option value="Finance Officer">Finance officer</option>
                <option value="Testing Officer">Testing Officer</option>
                <option value="Demonstrator">Demonstrator</option>
                <option value="Account Officer">Account Officer</option>
                <option value="Management Assistant">Management Assistant</option>
                <option value="Training Officer">Training Officer</option>
                <option value="Program Officer">Program Officer</option>
                <option value="Driver">Driver</option>
                <option value="Labor">Labor</option>
                <option value="Security">Security</option>
            </select>
        </div>

        <!-- Multiple Course Assignment Section for Instructors -->
        <div class="form-group" id="course_section" style="display: none;">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Courses (For Instructors/Senior Instructors):</label>
            <div id="course_assignments" class="space-y-3">
                <div class="course-assignment-item flex gap-2 items-center">
                    <select name="course_nos[]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select a Course --</option>
                        <?php
                        foreach ($courses as $course) {
                            echo '<option value="' . htmlspecialchars($course['course_no']) . '">' 
                                 . htmlspecialchars($course['course_name']) . ' (' . htmlspecialchars($course['course_no']) . ')'
                                 . '</option>';
                        }
                        ?>
                    </select>
                    <button type="button" onclick="removeCourseField(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <button type="button" onclick="addCourseField()" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-1"></i> Add Another Course
            </button>
            <p class="text-xs text-gray-600 mt-2">You can assign multiple courses during registration. Additional courses can also be assigned later from "Manage Staff".</p>
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
    const maxFileSizeMB = 1;
    const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024;

    profilePhotoInput.addEventListener('change', function(event) {
        photoError.textContent = '';
        const file = event.target.files[0];

        if (file) {
            if (file.size > maxFileSizeBytes) {
                photoError.textContent = `Error: File size exceeds ${maxFileSizeMB}MB limit. Please choose a smaller file.`;
                event.target.value = null;
            }
        }
    });

    const staffRegisterForm = profilePhotoInput.closest('form');
    if (staffRegisterForm) {
        staffRegisterForm.addEventListener('submit', function(event) {
            const file = profilePhotoInput.files[0];
            if (file && file.size > maxFileSizeBytes) {
                photoError.textContent = `Error: File size exceeds ${maxFileSizeMB}MB limit. Cannot submit.`;
                event.preventDefault();
            }
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                event.preventDefault();
            }
        });
    }

    function toggleCourseSection() {
        const positionSelect = document.getElementById('position');
        const selectedPosition = positionSelect.value;
        const courseSection = document.getElementById('course_section');

        const instructorPositions = ['Instructor', 'Senior Instructor'];

        if (instructorPositions.includes(selectedPosition)) {
            courseSection.style.display = 'block';
        } else {
            courseSection.style.display = 'none';
            // Clear all course selections
            const courseSelects = document.querySelectorAll('select[name="course_nos[]"]');
            courseSelects.forEach(select => select.value = '');
        }
    }

    function addCourseField() {
        const container = document.getElementById('course_assignments');
        const firstItem = container.querySelector('.course-assignment-item');
        const newItem = firstItem.cloneNode(true);
        
        // Reset the select value
        newItem.querySelector('select').value = '';
        
        // Show remove button
        const removeBtn = newItem.querySelector('button');
        removeBtn.style.display = 'block';
        
        container.appendChild(newItem);
        
        // Show remove buttons on all items except first
        updateRemoveButtons();
    }

    function removeCourseField(button) {
        const item = button.closest('.course-assignment-item');
        item.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.course-assignment-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('button');
            if (items.length > 1 && index > 0) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', toggleCourseSection);
</script>

<?php
if (isset($con)) {
    $con->close();
}
include_once '../include/footer.php';
?>