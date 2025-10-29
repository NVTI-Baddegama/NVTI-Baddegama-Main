<?php
// 1. Database Connection එක ඇතුලත් කරන්න
require '../include/connection.php';

// 2. URL එකෙන් එන ID එක (integer) ආරක්ෂිතව ගන්න
$staff_id = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $staff_id = (int)$_GET['id'];
} else {
    // ID එකක් නැත්නම් error එකක් JSON විදිහට output කරන්න
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid Staff ID']);
    exit;
}

// 3. !! Database එකෙන් දත්ත ගන්න (Prepared Statement)
//    !! ඔබේ 'staff' table එකට සහ column වලට අනුව සකස් කර ඇත
//    CONCAT මගින් first_name සහ last_name එකතු කර 'full_name' ලෙස ගනී
$sql = "SELECT 
            staff_id, 
            CONCAT(first_name, ' ', last_name) AS full_name, 
            nic, 
            contact_no, 
            email, 
            gender, 
            position, 
            course_no, 
            profile_photo 
        FROM 
            staff 
        WHERE 
            id = ? AND status = 'active'"; // අපි 'id' (integer) එකෙන් query කරනවා

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