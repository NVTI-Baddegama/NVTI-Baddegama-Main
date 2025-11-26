<?php
session_start();
// Adjust path: admin/lib -> up 2 levels -> include
include_once('../../include/connection.php'); 

// Query specific columns for Courses
$query = "SELECT course_no, course_name, course_fee, course_type, course_duration FROM course ORDER BY course_no ASC";

$stmt = $con->prepare($query);

// Safety Check
if (!$stmt) {
    die("Database Query Failed: " . $con->error);
}

$stmt->execute();
$result = $stmt->get_result();

$filename = "course_list_" . date('Y-m-d') . ".csv";

// Send Headers for Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open Output
$output = fopen('php://output', 'w');

// Add CSV Headers
fputcsv($output, array('Course No', 'Course Name', 'Fee', 'Type', 'Duration'));

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // You can add " Months" to duration if needed, e.g., $row['course_duration'] . " Months"
        fputcsv($output, $row);
    }
}

fclose($output);
$stmt->close();
$con->close();
exit();
?>