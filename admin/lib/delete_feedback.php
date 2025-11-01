<?php
// Set header to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.'
];

// 1. Include the connection file
// This path must be correct relative to *this* file
// (e.g., if this file is in 'lib', connection is in 'include')
include('../../include/connection.php'); 

// 2. Validate connection (using $con)
if (!$con || $con->connect_error) {
    $response['message'] = 'Database connection failed: ' . (isset($con->connect_error) ? $con->connect_error : 'Unknown DB error');
    echo json_encode($response);
    exit();
}

// 3. Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 4. Get and validate input
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];

        // 5. Prepare and execute the delete query
        $sql = "DELETE FROM feedback WHERE id = ?";
        
        $stmt = $con->prepare($sql); 
        
        if ($stmt) {
            $stmt->bind_param("i", $id); // "i" for integer
            
            if ($stmt->execute()) {
                // Check if any row was actually deleted
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Feedback deleted successfully.';
                } else {
                    $response['message'] = 'No feedback found with that ID.';
                }
            } else {
                $response['message'] = 'Database execute failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Database prepare failed: ' . $con->error; 
        }
    } else {
        $response['message'] = 'Missing required ID parameter.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// 6. Close the connection
$con->close(); 
echo json_encode($response);
exit();
?>