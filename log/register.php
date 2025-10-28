<?php
session_start();
include '../include/connection.php';

// Get courses for dropdown
$courses_query = "SELECT * FROM course WHERE status = 'active' ORDER BY course_name";
$courses_result = $con->query($courses_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration - NVTI Baddegama</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .register-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 8px;
        }
        .file-input {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
        }
        .file-input:hover {
            border-color: #007bff;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn-register:hover {
            background: #218838;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        #course_section {
            display: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <img src="../images/logo/NVTI_logo.png" alt="NVTI Logo">
            <h2>Staff Registration</h2>
            <p>National Vocational Training Institute - Baddegama</p>
        </div>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="register_process.php" method="POST" enctype="multipart/form-data">
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

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_no">Contact Number:</label>
                    <input type="tel" id="contact_no" name="contact_no" pattern="[0-9]{10}" placeholder="07XXXXXXXX" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" required>
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
                <label for="course_no">Course: (For Instructors)</label>
                <select id="course_no" name="course_no">
                    <option value="">Select Course</option>
                    <?php
                    if ($courses_result && $courses_result->num_rows > 0) {
                        $courses_result->data_seek(0);
                        while ($course = $courses_result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($course['course_no']) . '">' . htmlspecialchars($course['course_name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Create Password:</label>
                    <input type="password" id="password" name="password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>
            </div>

            <button type="submit" class="btn-register">Register</button>
        </form>

        <div class="login-link">
            <p><a href="../index.php">Back to Home</a></p>
        </div>
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
</body>
</html>