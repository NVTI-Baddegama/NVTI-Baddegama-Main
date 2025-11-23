<?php
// Set header to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'An unknown error occurred.'
];

// 1. Include the connection file
// This path must be correct relative to *this* file
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
    if (isset($_POST['id']) && isset($_POST['new_status'])) {
        $id = (int)$_POST['id'];
        $new_status = (int)$_POST['new_status'];

        if ($new_status !== 0 && $new_status !== 1) {
            $response['message'] = 'Invalid approval status value.';
            echo json_encode($response);
            exit();
        }

        // 5. Prepare and execute the update query (using $con)
        $sql = "UPDATE feedback SET approve = ? WHERE id = ?";
        
        // Use $con here
        $stmt = $con->prepare($sql); 
        
        if ($stmt) {
            $stmt->bind_param("ii", $new_status, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Status updated successfully.';
            } else {
                $response['message'] = 'Database execute failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            // Use $con here
            $response['message'] = 'Database prepare failed: ' . $con->error; 
        }
    } else {
        $response['message'] = 'Missing required parameters (id or new_status).';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// 6. Close the connection (using $con)
$con->close(); 
echo json_encode($response);
exit();
?>