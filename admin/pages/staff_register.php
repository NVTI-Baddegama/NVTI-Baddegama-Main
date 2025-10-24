<?php
session_start();
include_once '../include/header.php';
include_once '../../include/connection.php';


// Get courses for dropdown
$courses_query = "SELECT * FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
?>


<link rel="stylesheet" href="../css/staff_register.css">

<div class="register-container">
    <div class="register-header">
        <h2>Staff Registration</h2>
        <p>National Vocational Training Institute - Baddegama</p>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>

    <form action="../lib/register_process.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="file-input">
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
                    <input type="radio" name="gender" value="Male" required>
                    Male
                </label>
                <label>
                    <input type="radio" name="gender" value="Female" required>
                    Female
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="position">Position:</label>
            <select id="position" name="position" required onchange="toggleCourseSection()">
                <option value="">Select Position</option>
                <option value="Instructors">Instructors</option>
                <option value="Non-Academic Staff">Non-Academic Staff</option>
            </select>
        </div>

        <div class="form-group" id="course_section">
            <label for="course_no">Course: (For Academic Staff)</label>
            <select id="course_no" name="course_no">
                <option value="">Select Course</option>
                <?php
                if ($courses_result && $courses_result->num_rows > 0) {
                    // Reset result pointer just in case
                    $courses_result->data_seek(0);
                    while ($course = $courses_result->fetch_assoc()) {
                        // value එක course_no ලෙස වෙනස් කළා
                        echo '<option value="' . htmlspecialchars($course['course_no']) . '">' . htmlspecialchars($course['course_name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Create Password:</label>
                <input type="password" id="password" name="password" minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="6">
            </div>
        </div>

        <button type="submit" class="btn-register">Register</button>
    </form>


</div>

<script>
    function toggleCourseSection() {
        const position = document.getElementById('position').value;
        const courseSection = document.getElementById('course_section');
        const courseSelect = document.getElementById('course_no');

        if (position === 'Instructors') {
            courseSection.style.display = 'block';
            courseSelect.required = true;
        } else {
            courseSection.style.display = 'none';
            courseSelect.required = false;
            courseSelect.value = '';
        }
    }

</script>

<?php include_once '../include/footer.php'; ?>