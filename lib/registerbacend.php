<?php

include '../include/connection.php';

if (!$con) {

    die("Database connection failed.");
}

if (isset($_POST['submit'])) {

    $fullName = trim($_POST['fullName']);
    $nic = trim($_POST['nic']);
    $address = trim($_POST['address']);
    $dob = trim($_POST['dob']);

    // Contact Information
    $contactNo = trim($_POST['contactNo']);
    $whatsappNo = trim($_POST['whatsappNo'] ?? '');

    // Educational Qualifications
    $olPassStatus = trim($_POST['olPassStatus']);
    $olEnglish = trim($_POST['olEnglish'] ?? '');
    $olMaths = trim($_POST['olMaths'] ?? '');
    $olScience = trim($_POST['olScience'] ?? '');
    $alCategory = trim($_POST['alCategory']);

    // Course Application
    $courseOptionOne = trim($_POST['courseOptionOne']);
    $courseOptionTwo = trim($_POST['courseOptionTwo'] ?? '');

    // --- 2. Input Validation and Redirects ---

    if (empty($fullName)) {
        header("location:../pages/enrollment_form.php?error=FullName_required");
        exit();
    }

    if (empty($nic)) {
        header("location:../pages/enrollment_form.php?error=NIC_required");
        exit();
    }

    if (empty($contactNo)) {
        header("location:../pages/enrollment_form.php?error=ContactNo_required");
        exit();
    }

    if (empty($courseOptionOne)) {
        header("location:../pages/enrollment_form.php?error=Course_Option_One_required");
        exit();
    }

    // --- 3. Check for Duplicate NIC ---

    // NOTE: For security, use mysqli_real_escape_string($con, $nic) here!
    $check_query = "SELECT id FROM student_enrollments WHERE nic = '$nic'";
    $result_check = mysqli_query($con, $check_query);

    if (!$result_check) {
        // Cannot use error_log()
        header("location:../pages/enrollment_form.php?error=check_failed");
        exit();
    }

    if (mysqli_num_rows($result_check) > 0) {
        // NIC found, user already registered
        header("location:../pages/enrollment_form.php?error=NIC_already_registered");
        exit();
    }

    // --- 4. Insert Data into Database ---

    // NOTE: For security, ALL variables must be escaped with mysqli_real_escape_string()

    $insert_query = "INSERT INTO student_enrollments (
        full_name, nic, address, dob, contact_no, whatsapp_no, 
        ol_pass_status, ol_english_grade, ol_maths_grade, ol_science_grade, 
        al_category, course_option_one, course_option_two
    ) VALUES (
        '$fullName', '$nic', '$address', '$dob', '$contactNo', '$whatsappNo', 
        '$olPassStatus', '$olEnglish', '$olMaths', '$olScience', 
        '$alCategory', '$courseOptionOne', '$courseOptionTwo'
    )";

    $result_insert = mysqli_query($con, $insert_query);

    if ($result_insert) {
        // Success
        header("location:../pages/enrollment_form.php?success=application_submitted");
        exit();
    } else {
        // Failure
        header("location:../pages/enrollment_form.php?error=stmt_failed");
        exit();
    }

    // NOTE: The connection remains open here because mysqli_close() was excluded.
    // In a normal script, it should be called: mysqli_close($con); 

} else {
    // If accessed without form submission
    header("location:../pages/enrollment_form.php?error=invalid_access");
    exit();
}
?>