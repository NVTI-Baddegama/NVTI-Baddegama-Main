<?php

include '../include/connection.php'; // $con යනු mysqli object එකකි

if (!$con) {
    // මෙය connection.php හිදී සම්බන්ධතාවය අසාර්ථක වුවහොත් ක්‍රියාත්මක වේ.
}

if (isset($_POST['submit'])) {

    // --- 1. දත්ත ලබා ගැනීම සහ Sanitization කිරීම ---
    // Prepared statements භාවිතා කරන නිසා mysqli_real_escape_string අනවශ්‍ය වුවද, 
    // මෙහිදී දත්ත පිරිසිදු කිරීම සඳහා එය භාවිතා කරයි.

    // Generate a random 6-digit Student ID
    do {
        $StudentID = "VTA_BAD" . rand(100000, 999999);
        $check_id_query = "SELECT * FROM student_enrollments WHERE Student_id='$StudentID'";
        $check_id_result = mysqli_query($con, $check_id_query);
    } while (mysqli_num_rows($check_id_result) > 0);
    
    $fullName = mysqli_real_escape_string($con, trim($_POST['fullName']));
    $nic = mysqli_real_escape_string($con, trim($_POST['nic']));
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $dob = mysqli_real_escape_string($con, trim($_POST['dob']));

    // Contact Information
    $contactNo = mysqli_real_escape_string($con, trim($_POST['contactNo']));
    $whatsappNo = mysqli_real_escape_string($con, trim($_POST['whatsappNo'] ?? ''));

    // Educational Qualifications
    $olPassStatus = mysqli_real_escape_string($con, trim($_POST['olPassStatus']));
    $olEnglish = mysqli_real_escape_string($con, trim($_POST['olEnglish'] ?? ''));
    $olMaths = mysqli_real_escape_string($con, trim($_POST['olMaths'] ?? ''));
    $olScience = mysqli_real_escape_string($con, trim($_POST['olScience'] ?? ''));
    $alCategory = mysqli_real_escape_string($con, trim($_POST['alCategory']));

    // Course Application
    $courseOptionOne = mysqli_real_escape_string($con, trim($_POST['courseOptionOne']));
    $courseOptionTwo = mysqli_real_escape_string($con, trim($_POST['courseOptionTwo'] ?? ''));


    // --- 2. Input Validation සහ Redirection (register.php වෙතට) ---

    if (empty($fullName)) {
        header("location:../pages/register.php?error=Full_Name_Required");
        exit();
    }

    if (empty($nic)) {
        header("location:../pages/register.php?error=NIC_Required");
        exit();
    }

    if (empty($contactNo)) {
        header("location:../pages/register.php?error=Contact_No_Required");
        exit();
    }

    if (empty($courseOptionOne)) {
        header("location:../pages/register.php?error=Course_Option_One_Required");
        exit();
    }

    // --- 3. Duplicate NIC පරීක්ෂා කිරීම (Prepared Statement භාවිතයෙන්) ---
    $check_query = "SELECT id FROM student_enrollments WHERE nic = ?";

    $stmt_check = $con->prepare($check_query);

    if (!$stmt_check) {
        header("location:../pages/register.php?error=Check_Failed");
        exit();
    }

    $stmt_check->bind_param("s", $nic);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // NIC දැනටමත් ලියාපදිංචි කර ඇත
        header("location:../pages/register.php?error=NIC_Already_Registered");
        exit();
    }
    $stmt_check->close();

    // --- 4. දත්ත සමුදායට දත්ත ඇතුළු කිරීම (Prepared Statement භාවිතයෙන්) ---

    $insert_query = "INSERT INTO student_enrollments (
        Student_id, full_name, nic, address, dob, contact_no, whatsapp_no, 
        ol_pass_status, ol_english_grade, ol_maths_grade, ol_science_grade, 
        al_category, course_option_one, course_option_two
    ) VALUES (
        ?,?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, 
        ?, ?, ?
    )";

    // Insertion statement සකස් කිරීම
    $stmt_insert = $con->prepare($insert_query);

    if (!$stmt_insert) {
        header("location:../pages/register.php?error=Insertion_Preparation_Failed");
        exit();
    }

    // Parameters සම්බන්ධ කිරීම (bind_param)
    // සියලු parameters 'string' (s) ලෙස සැලකේ.
    $stmt_insert->bind_param(
        "ssssssssssssss",
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
        $courseOptionOne,
        $courseOptionTwo
    );

    // Statement එක ක්‍රියාත්මක කිරීම
    if ($stmt_insert->execute()) {
        // සාර්ථකත්වය
        header("location:../pages/register.php?success=Application_Submitted_Successfully");
        exit();
    } else {
        // අසාර්ථකත්වය
        header("location:../pages/register.php?error=Insertion_Failed");
        exit();
    }

    // Statement වසා දැමීම
    $stmt_insert->close();
    // සම්බන්ධතාවය වසා දැමීම
    $con->close();


} else {
    // Form submission එකකින් තොරව ප්‍රවේශ වුවහොත්
    header("location:../pages/register.php?error=Invalid_Access");
    exit();
}
?>