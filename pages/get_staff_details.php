<?php
// Database Connection
require '../include/connection.php';

// Get and validate staff ID from URL
$staff_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $staff_id = (int)$_GET['id'];
} else {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Staff ID']);
    exit;
}

// Get staff basic information
$sql = "SELECT 
            s.staff_id, 
            CONCAT(s.first_name, ' ', s.last_name) AS full_name,
            s.first_name,
            s.nic, 
            s.contact_no, 
            s.email, 
            s.gender, 
            s.position, 
            c.course_name AS course_no,
            s.profile_photo 
        FROM 
            staff AS s
        LEFT JOIN 
            course AS c ON s.course_no = c.course_no
        WHERE 
            s.id = ? AND s.status = 'active'";

$stmt = $con->prepare($sql);

if ($stmt === false) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare error']);
    exit;
}

$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

$staff_data = null;
if ($result->num_rows == 1) {
    $staff_data = $result->fetch_assoc();
}

$stmt->close();

// If staff is Instructor or Senior Instructor, get all assigned courses from instructor_courses table
$assigned_courses = [];
if ($staff_data && in_array($staff_data['position'], ['Instructor', 'Senior Instructor'])) {
    $courses_query = "SELECT ic.course_no, c.course_name, ic.assigned_date, ic.status
                      FROM instructor_courses ic
                      JOIN course c ON ic.course_no = c.course_no
                      WHERE ic.staff_id = ?
                      ORDER BY ic.assigned_date DESC";
    
    $courses_stmt = $con->prepare($courses_query);
    if ($courses_stmt) {
        $courses_stmt->bind_param("s", $staff_data['staff_id']);
        $courses_stmt->execute();
        $courses_result = $courses_stmt->get_result();
        
        while ($course_row = $courses_result->fetch_assoc()) {
            $assigned_courses[] = $course_row;
        }
        
        $courses_stmt->close();
    }
}

$con->close();

// Output JSON with staff data and assigned courses
header('Content-Type: application/json');

if ($staff_data) {
    // Add assigned courses to the response
    $staff_data['assigned_courses'] = $assigned_courses;
    echo json_encode($staff_data);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Staff member not found or is inactive']);
}

exit;
?>