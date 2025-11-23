<?php
session_start();
// Adjust path to connection.php based on your structure (../../ goes back to root from admin/staff/)
include_once('../../include/connection.php'); 

// --- 1. Query Data ---
// We select specific columns appropriate for a spreadsheet
$query = "SELECT service_id, first_name, last_name, position, email, contact_no, status FROM staff ORDER BY position ASC, first_name ASC";

$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// --- 2. Set Headers for Download ---
$filename = "staff_list_" . date('Y-m-d') . ".csv";

// Force the browser to download the file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// --- 3. Generate Output ---
// Open the output stream
$output = fopen('php://output', 'w');

// Add the Column Headers (First row of the CSV)
fputcsv($output, array('Service ID', 'First Name', 'Last Name', 'Position', 'Email', 'Phone', 'Status'));

// Loop through the database results and add them to the CSV
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Optional: Clean status text (e.g., capitalize)
        $row['status'] = ucfirst($row['status']);
        fputcsv($output, $row);
    }
}

// Close the stream
fclose($output);
$stmt->close();
$con->close();
exit();
?>