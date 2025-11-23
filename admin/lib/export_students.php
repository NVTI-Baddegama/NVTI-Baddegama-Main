<?php
session_start();
include_once('../../include/connection.php'); // Go back two folders to reach the root connection

// Check if export parameter is set
if (!isset($_GET['export']) || !in_array($_GET['export'], ['csv', 'pdf'])) {
    header('Location: ../pages/manage_students.php');
    exit();
}

$export_format = $_GET['export'];

// Get filter parameters
$filter_course = '';
if (isset($_GET['filter_course']) && !empty($_GET['filter_course'])) {
    $filter_course = trim($_GET['filter_course']);
}

$search_nic = '';
if (isset($_GET['search_nic']) && !empty($_GET['search_nic'])) {
    $search_nic = trim($_GET['search_nic']);
}

// Build the same query as in manage_students.php - using SELECT * to get all available columns
$base_query = "SELECT * FROM student_enrollments";
$where_clauses = [];
$params = [];
$types = "";

// Add course filter
if ($filter_course) {
    $where_clauses[] = "course_option_one = ?";
    $params[] = $filter_course;
    $types .= "s";
}

// Add search filter (by NIC)
if ($search_nic) {
    $where_clauses[] = "nic LIKE ?";
    $params[] = "%" . $search_nic . "%";
    $types .= "s";
}

// Combine WHERE clauses
if (!empty($where_clauses)) {
    $base_query .= " WHERE " . implode(" AND ", $where_clauses);
}

$base_query .= " ORDER BY application_date DESC";

// Prepare and execute the query
$stmt = $con->prepare($base_query);
if (!$stmt) {
    $_SESSION['error_msg'] = 'Failed to prepare database query for export: ' . $con->error;
    header('Location: ../pages/manage_students.php');
    exit();
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Generate filename with current date and filters
$filename = 'student_enrollments_' . date('Y-m-d_H-i-s');
if ($filter_course) {
    $filename .= '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $filter_course);
}
if ($search_nic) {
    $filename .= '_NIC_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $search_nic);
}

if ($export_format === 'csv') {
    // CSV Export
    $filename .= '.csv';
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for proper Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Define CSV headers - including new fields
    $headers = [
        'Student ID',
        'Full Name',
        'NIC',
        'Contact Number',
        'Home Address',
        'Course Choice 1',
        'Course Choice 2',
        'Application Date',
        'Status'
    ];

    // Write headers to CSV
    fputcsv($output, $headers);

    // Write data rows
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $csv_row = [
                $row['Student_id'] ?? '',
                $row['full_name'] ?? '',
                $row['nic'] ?? '',
                $row['contact_no'] ?? '',
                $row['address'] ?? '',
                $row['course_option_one'] ?? '',
                $row['course_option_two'] ?? '',
                $row['application_date'] ? date('Y-m-d H:i:s', strtotime($row['application_date'])) : '',
                ($row['is_processed'] == 1) ? 'Processed' : 'Pending'
            ];
            fputcsv($output, $csv_row);
        }
    } else {
        // If no data, add a row indicating no records found
        $no_data_row = ['No records found matching the selected criteria'];
        for ($i = 1; $i < count($headers); $i++) {
            $no_data_row[] = '';
        }
        fputcsv($output, $no_data_row);
    }

    // Close file pointer
    fclose($output);

} else if ($export_format === 'pdf') {
    // PDF Export using HTML to PDF conversion
    $filename .= '.pdf';
    
    // Collect data for PDF
    $students = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    
    // Generate HTML content for PDF
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Student Enrollments Report</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; margin: 15px; }
            .header { text-align: center; margin-bottom: 20px; }
            .header h1 { color: #333; margin: 0; font-size: 18px; }
            .header p { color: #666; margin: 5px 0; font-size: 12px; }
            .filters { background: #f5f5f5; padding: 8px; margin-bottom: 15px; border-radius: 3px; font-size: 11px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 9px; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .status-processed { color: #28a745; font-weight: bold; }
            .status-pending { color: #ffc107; font-weight: bold; }
            .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #666; }
            .address { max-width: 120px; word-wrap: break-word; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Student Enrollments Report</h1>
            <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
        </div>';
    
    // Add filter information if applicable
    if ($filter_course || $search_nic) {
        $html .= '<div class="filters">
            <strong>Applied Filters:</strong>';
        if ($filter_course) {
            $html .= ' Course: ' . htmlspecialchars($filter_course);
        }
        if ($search_nic) {
            $html .= ' | NIC Search: ' . htmlspecialchars($search_nic);
        }
        $html .= '</div>';
    }
    
    $html .= '<table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>NIC</th>
                <th>Contact No</th>
                <th>Home Address</th>
                <th>Course Choice 1</th>
                <th>Course Choice 2</th>
                <th>Application Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    if (!empty($students)) {
        foreach ($students as $student) {
            $status_class = ($student['is_processed'] == 1) ? 'status-processed' : 'status-pending';
            $status_text = ($student['is_processed'] == 1) ? 'Processed' : 'Pending';
            
            $html .= '<tr>
                <td>' . htmlspecialchars($student['Student_id'] ?? '') . '</td>
                <td>' . htmlspecialchars($student['full_name'] ?? '') . '</td>
                <td>' . htmlspecialchars($student['nic'] ?? '') . '</td>
                <td>' . htmlspecialchars($student['contact_no'] ?? '') . '</td>
                <td class="address">' . htmlspecialchars($student['address'] ?? '') . '</td>
                <td>' . htmlspecialchars($student['course_option_one'] ?? '') . '</td>
                <td>' . htmlspecialchars($student['course_option_two'] ?? '') . '</td>
                <td>' . ($student['application_date'] ? date('Y-m-d', strtotime($student['application_date'])) : '') . '</td>
                <td class="' . $status_class . '">' . $status_text . '</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="9" style="text-align: center; color: #666;">No records found matching the selected criteria</td></tr>';
    }
    
    $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Total Records: ' . count($students) . '</p>
        <p>NVTI Baddegama - Student Management System</p>
    </div>
    
    </body>
    </html>';
    
    // Simple HTML to PDF conversion using browser print functionality
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    
    echo $html;
    echo '<script>
        window.onload = function() {
            window.print();
        };
    </script>';
}

// Close database connections
if ($stmt) $stmt->close();
if (isset($con)) $con->close();

// Exit to prevent any additional output
exit();
?>