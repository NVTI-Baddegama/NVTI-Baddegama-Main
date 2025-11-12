<?php
session_start();
include_once '../../include/connection.php';

if (!isset($_POST['action']) || !isset($_POST['staff_id'])) {
    $_SESSION['staff_error_msg'] = "Invalid request";
    header("Location: ../pages/manage_staff.php");
    exit();
}

$action = $_POST['action'];
$staff_id = trim($_POST['staff_id']);

// Verify staff is instructor or senior instructor
$verify_query = "SELECT position FROM staff WHERE staff_id = ?";
$verify_stmt = $con->prepare($verify_query);
$verify_stmt->bind_param("s", $staff_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows == 0) {
    $_SESSION['staff_error_msg'] = "Staff member not found";
    $verify_stmt->close();
    header("Location: ../pages/manage_staff.php");
    exit();
}

$staff_data = $verify_result->fetch_assoc();
$verify_stmt->close();

if (!in_array($staff_data['position'], ['Instructor', 'Senior Instructor'])) {
    $_SESSION['staff_error_msg'] = "Course assignments are only for Instructors and Senior Instructors";
    header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
    exit();
}

// Handle ADD action
if ($action === 'add') {
    if (!isset($_POST['course_no']) || empty(trim($_POST['course_no']))) {
        $_SESSION['staff_error_msg'] = "Please select a course";
        header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
        exit();
    }
    
    $course_no = trim($_POST['course_no']);
    
    // Check if already assigned
    $check_query = "SELECT id FROM instructor_courses WHERE staff_id = ? AND course_no = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("ss", $staff_id, $course_no);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['staff_error_msg'] = "This course is already assigned to this instructor";
        $check_stmt->close();
        header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
        exit();
    }
    $check_stmt->close();
    
    // Insert new assignment
    $insert_query = "INSERT INTO instructor_courses (staff_id, course_no, assigned_date, status) VALUES (?, ?, NOW(), 'active')";
    $insert_stmt = $con->prepare($insert_query);
    $insert_stmt->bind_param("ss", $staff_id, $course_no);
    
    if ($insert_stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Course assigned successfully";
    } else {
        $_SESSION['staff_error_msg'] = "Failed to assign course: " . $insert_stmt->error;
    }
    $insert_stmt->close();
}

// Handle REMOVE action
elseif ($action === 'remove') {
    if (!isset($_POST['assignment_id']) || empty(trim($_POST['assignment_id']))) {
        $_SESSION['staff_error_msg'] = "Invalid assignment ID";
        header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
        exit();
    }
    
    $assignment_id = intval($_POST['assignment_id']);
    
    // Verify assignment belongs to this staff member
    $verify_assignment = "SELECT id FROM instructor_courses WHERE id = ? AND staff_id = ?";
    $verify_stmt = $con->prepare($verify_assignment);
    $verify_stmt->bind_param("is", $assignment_id, $staff_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows == 0) {
        $_SESSION['staff_error_msg'] = "Assignment not found or doesn't belong to this staff member";
        $verify_stmt->close();
        header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
        exit();
    }
    $verify_stmt->close();
    
    // Delete assignment
    $delete_query = "DELETE FROM instructor_courses WHERE id = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param("i", $assignment_id);
    
    if ($delete_stmt->execute()) {
        $_SESSION['staff_success_msg'] = "Course assignment removed successfully";
    } else {
        $_SESSION['staff_error_msg'] = "Failed to remove course assignment: " . $delete_stmt->error;
    }
    $delete_stmt->close();
}

else {
    $_SESSION['staff_error_msg'] = "Invalid action";
}

$con->close();
header("Location: ../pages/view_staff_details.php?staff_id=" . urlencode($staff_id));
exit();
?>