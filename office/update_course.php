<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$staff_id = $_SESSION['staff_id'];
$position = $_SESSION['position'];

// Verify user is instructor or senior instructor
if (!in_array($position, ['Instructor', 'Senior Instructor'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get POST data
$course_no = isset($_POST['course_no']) ? trim($_POST['course_no']) : '';
$field = isset($_POST['field']) ? trim($_POST['field']) : '';
$value = isset($_POST['value']) ? trim($_POST['value']) : '';

if (empty($course_no) || empty($field)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Verify instructor is assigned to this course
$verify_query = "SELECT id FROM instructor_courses WHERE staff_id = ? AND course_no = ? AND status = 'active'";
$verify_stmt = $con->prepare($verify_query);
$verify_stmt->bind_param("ss", $staff_id, $course_no);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'You are not assigned to this course']);
    $verify_stmt->close();
    exit();
}
$verify_stmt->close();

// Define allowed fields to update
$allowed_fields = [
    'course_description' => 's',
    'qualifications' => 's',
    'course_fee' => 's',
    'course_duration' => 'i'
];

if (!array_key_exists($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit();
}

// Validate based on field type
if ($field === 'course_duration') {
    if (!is_numeric($value) || intval($value) <= 0) {
        echo json_encode(['success' => false, 'message' => 'Course duration must be a positive number']);
        exit();
    }
    $value = intval($value);
}

// Update the course
$update_query = "UPDATE course SET $field = ? WHERE course_no = ?";
$update_stmt = $con->prepare($update_query);
$update_stmt->bind_param($allowed_fields[$field] . 's', $value, $course_no);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Course updated successfully',
        'field' => $field,
        'value' => $value
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update course: ' . $update_stmt->error]);
}

$update_stmt->close();
$con->close();
?>