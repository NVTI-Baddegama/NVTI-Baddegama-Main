<?php
session_start(); // Start session for redirect messages
include '../include/connection.php'; // $con යනු mysqli object එකකි

if (!$con) {
    die("Database connection failed."); 
}

if (isset($_POST['submit'])) {

    // --- 1. දත්ත ලබා ගැනීම ---
    
    // Generate a random 6-digit Student ID
    do {
        $StudentID = "VTA_BAD" . rand(100000, 999999);
        $check_id_query = "SELECT Student_id FROM student_enrollments WHERE Student_id = ?";
        $stmt_check_id = $con->prepare($check_id_query);
        $stmt_check_id->bind_param("s", $StudentID);
        $stmt_check_id->execute();
        $check_id_result = $stmt_check_id->get_result();
        $stmt_check_id->close();
    } while ($check_id_result->num_rows > 0);
    
    $fullName = trim($_POST['fullName']);
    $nic = trim($_POST['nic']);
    $address = trim($_POST['address']);
    $dob = trim($_POST['dob']);
    $contactNo = trim($_POST['contactNo']);
    $whatsappNo = trim($_POST['whatsappNo'] ?? '');
    $olPassStatus = trim($_POST['olPassStatus']);
    $olEnglish = trim($_POST['olEnglish'] ?? '');
    $olMaths = trim($_POST['olMaths'] ?? '');
    $olScience = trim($_POST['olScience'] ?? '');
    $alCategory = trim($_POST['alCategory']);
    $courseOptionOne = trim($_POST['courseOptionOne']); 
    $courseOptionTwo = trim($_POST['courseOptionTwo'] ?? ''); 

    // --- 2. Input Validation ---
    if (empty($fullName) || empty($nic) || empty($address) || empty($dob) || empty($contactNo) || empty($olPassStatus) || empty($alCategory) || empty($courseOptionOne)) {
        header("location:../pages/register.php?error=Please_fill_all_required_fields");
        exit();
    }
    if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
        header("location:../pages/register.php?error=Invalid_NIC_format");
        exit();
    }
    if (!preg_match('/^[0-9]{10}$/', $contactNo)) {
        header("location:../pages/register.php?error=Invalid_Contact_Number_format");
        exit();
    }

    // --- 3. Duplicate NIC පරීක්ෂා කිරීම (--- ඉවත් කරන ලදී ---) ---
    /*
    $check_query = "SELECT id FROM student_enrollments WHERE nic = ?";
    $stmt_check = $con->prepare($check_query);
    // ... (rest of the check code is removed) ...
    */
    // --- END OF REMOVED BLOCK ---


    // --- 4. දත්ත සමුදායට දත්ත ඇතුළු කිරීම ---
    $insert_query = "INSERT INTO student_enrollments (
        Student_id, full_name, nic, address, dob, contact_no, whatsapp_no, 
        ol_pass_status, ol_english_grade, ol_maths_grade, ol_science_grade, 
        al_category, course_option_one, course_option_two, is_processed, application_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";

    $stmt_insert = $con->prepare($insert_query);
    if (!$stmt_insert) {
        header("location:../pages/register.php?error=Database_prepare_error_inserting_data");
        exit();
    }

    $stmt_insert->bind_param(
        "ssssssssssssss",
        $StudentID, $fullName, $nic, $address, $dob, 
        $contactNo, $whatsappNo, $olPassStatus, $olEnglish, $olMaths, 
        $olScience, $alCategory, $courseOptionOne, $courseOptionTwo
    );

    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        $con->close();
        header("location:../pages/register.php?success=Application_Submitted_Successfully");
        exit();
    } else {
        // Handle potential errors (like connection loss, etc.)
        $error_message = $stmt_insert->error; 
        $stmt_insert->close();
        $con->close();
        header("location:../pages/register.php?error=Application_Submission_Failed");
        exit();
    }

} else {
    header("location:../pages/register.php?error=Invalid_Access_Method");
    exit();
}
?>