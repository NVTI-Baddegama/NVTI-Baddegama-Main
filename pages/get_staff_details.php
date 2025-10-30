<?php
// 1. Database Connection එක ඇතුලත් කරන්න
require '../include/connection.php';

// 2. URL එකෙන් එන ID එක (integer) ආරක්ෂිතව ගන්න
$staff_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $staff_id = (int)$_GET['id'];
} else {
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid Staff ID']);
    exit;
}

$sql = "SELECT 
            s.staff_id, 
            CONCAT(s.first_name, ' ', s.last_name) AS full_name, 
            s.nic, 
            s.contact_no, 
            s.email, 
            s.gender, 
            s.position, 
            c.course_name AS course_no,  -- <-- මෙතන වෙනස් විය
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
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database prepare error']);
    exit;
}

// 4. !! ID එක Bind කිරීම ('i' යනු Integer)
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

$staff_data = null;
if ($result->num_rows == 1) {
    // 5. දත්ත $staff_data variable එකට ගැනීම
    $staff_data = $result->fetch_assoc();
}

// 6. Statement සහ Connection close කිරීම
$stmt->close();
$con->close();

// 7. දත්ත JSON එකක් ලෙස Output කිරීම
header('Content-Type: application/json');

if ($staff_data) {
    echo json_encode($staff_data);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Staff member not found or is inactive']);
}

exit;
?>