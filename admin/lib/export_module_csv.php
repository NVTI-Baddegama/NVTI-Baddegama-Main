<?php
session_start();
include_once('../../include/connection.php');

// 1. Check if a course is selected
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Error: No Course Selected. Please go back and select a course first.");
}

$course_id = intval($_GET['course_id']);

// 2. Query Data
// We fetch module name, code/order_no, and join to get course name
$query = "SELECT m.module_name, m.order_no, c.course_name 
          FROM modules m 
          LEFT JOIN course c ON m.course_id = c.id 
          WHERE m.course_id = ? 
          ORDER BY m.order_no ASC, m.module_name ASC";

$stmt = $con->prepare($query);
if (!$stmt) {
    die("Database Query Failed: " . $con->error);
}

$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Prepare Filename
// We try to get the course name for the filename, otherwise default
$filename_course = "Course_Modules";
if ($result && $result->num_rows > 0) {
    // Peek at the first row to get course name, then reset pointer
    $first_row = $result->fetch_assoc();
    if($first_row && !empty($first_row['course_name'])) {
        $clean_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $first_row['course_name']); // Clean for filename
        $filename_course = $clean_name . "_Modules";
    }
    // Reset result pointer to beginning
    $result->data_seek(0);
}

$filename = $filename_course . "_" . date('Y-m-d') . ".csv";

// 4. Send Headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// 5. Output Data
$output = fopen('php://output', 'w');

fputcsv($output, array('Module Name', 'Module Code/Letter', 'Course Name'));

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['module_name'],
            $row['order_no'],
            $row['course_name']
        ));
    }
}

fclose($output);
$stmt->close();
$con->close();
exit();
?>