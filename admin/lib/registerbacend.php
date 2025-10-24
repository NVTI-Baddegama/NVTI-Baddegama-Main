<?php
session_start(); // Start session for redirect messages
include '../../include/connection.php'; // $con යනු mysqli object එකකි

if (!$con) {
    // This should ideally be handled within connection.php, but double-check
    die("Database connection failed."); 
}

if (isset($_POST['submit'])) {

    // --- 1. දත්ත ලබා ගැනීම සහ Sanitization කිරීම ---
    
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
    
    // Get POST data
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
    
    // --- UPDATED: Get actual course names ---
    $courseOptionOne = trim($_POST['courseOptionOne']); 
    $courseOptionTwo = trim($_POST['courseOptionTwo'] ?? ''); 
    // --- END UPDATED ---

    // --- 2. Input Validation ---
    if (empty($fullName) || empty($nic) || empty($address) || empty($dob) || empty($contactNo) || empty($olPassStatus) || empty($alCategory) || empty($courseOptionOne)) {
        header("location:../pages/student_register.php?error=Please_fill_all_required_fields");
        exit();
    }
    // Basic NIC validation (simple length check, adjust regex if needed)
    if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
        header("location:../pages/student_register.php?error=Invalid_NIC_format");
        exit();
    }
    // Basic phone number validation
    if (!preg_match('/^[0-9]{10}$/', $contactNo)) {
        header("location:../pages/student_register.php?error=Invalid_Contact_Number_format");
        exit();
    }
     if (!empty($whatsappNo) && !preg_match('/^[0-9]{10}$/', $whatsappNo)) {
        header("location:../pages/student_register.php?error=Invalid_WhatsApp_Number_format");
        exit();
    }

    // --- 3. Duplicate NIC පරීක්ෂා කිරීම (Prepared Statement භාවිතයෙන්) ---
    $check_query = "SELECT id FROM student_enrollments WHERE nic = ?";
    $stmt_check = $con->prepare($check_query);
    if (!$stmt_check) {
        header("location:../pages/student_register.php?error=Database_prepare_error_checking_NIC");
        exit();
    }
    $stmt_check->bind_param("s", $nic);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        $stmt_check->close();
        header("location:../pages/student_register.php?error=NIC_Already_Registered");
        exit();
    }
    $stmt_check->close();

    // --- 4. දත්ත සමුදායට දත්ත ඇතුළු කිරීම (Prepared Statement භාවිතයෙන්) ---
    $insert_query = "INSERT INTO student_enrollments (
        Student_id, full_name, nic, address, dob, contact_no, whatsapp_no, 
        ol_pass_status, ol_english_grade, ol_maths_grade, ol_science_grade, 
        al_category, course_option_one, course_option_two, is_processed, application_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())"; // Added is_processed and NOW()

    $stmt_insert = $con->prepare($insert_query);
    if (!$stmt_insert) {
        header("location:../pages/student_register.php?error=Database_prepare_error_inserting_data");
        exit();
    }

    // Bind parameters - all are strings 's'
    $stmt_insert->bind_param(
        "ssssssssssssss", // 14 strings
        $StudentID,
        $fullName,
        $nic,
        $address,
        $dob,
        $contactNo,
        $whatsappNo,
        $olPassStatus,
        $olEnglish,
        $olMaths,
        $olScience,
        $alCategory,
        $courseOptionOne, // Actual course name
        $courseOptionTwo  // Actual course name
    );

    // Execute statement
    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        $con->close();
        header("location:../pages/student_register.php?success=Application_Submitted_Successfully");
        exit();
    } else {
        $error_message = $stmt_insert->error; // Get specific error
        $stmt_insert->close();
        $con->close();
        header("location:../pages/student_register.php?error=Application_Submission_Failed_Please_Try_Again"); // Generic error for user
        // Log the specific error for debugging: error_log("Student Insert Failed: " . $error_message);
        exit();
    }

} else {
    // If accessed directly without POST
    header("location:../pages/student_register.php?error=Invalid_Access_Method");
    exit();
}
?>