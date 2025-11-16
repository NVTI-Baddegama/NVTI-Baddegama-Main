<?php
session_start();
include_once('../../include/connection.php');
require_once('../../include/fpdf/fpdf.php');

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

// Build the same query as in manage_students.php
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

    // Define CSV headers - Only requested columns
    $headers = [
        'Student ID',
        'Full Name',
        'NIC',
        'Contact Number',
        'Home Address',
        'Course Choice 1',
        'Course Choice 2'
    ];

    // Write headers to CSV
    fputcsv($output, $headers);

    // Write data rows
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $csv_row = [
                $row['Student_id'] ?? 'N/A',
                $row['full_name'] ?? 'N/A',
                $row['nic'] ?? 'N/A',
                $row['contact_no'] ?? 'N/A',
                $row['address'] ?? 'N/A',
                $row['course_option_one'] ?? 'N/A',
                $row['course_option_two'] ?? 'N/A'
            ];
            fputcsv($output, $csv_row);
        }
    } else {
        // If no data, add a row indicating no records found
        fputcsv($output, ['No records found matching the selected criteria']);
    }

    // Close file pointer
    fclose($output);

} else if ($export_format === 'pdf') {
    // PDF Export using FPDF
    $filename .= '.pdf';
    
    // Collect data for PDF
    $students = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    
    // Create PDF with landscape orientation for better table fit
    class PDF extends FPDF {
        function Header() {
            // Logo
            if (file_exists('../../images/logo/NVTI_logo.png')) {
                $this->Image('../../images/logo/NVTI_logo.png', 10, 6, 20);
            }
            
            // Title
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'NVTI Baddegama - Student Applications Report', 0, 1, 'C');
            
            // Date and info
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 6, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
            $this->Ln(5);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' | NVTI Baddegama - Student Management System', 0, 0, 'C');
        }
        
        // Improved table row with text wrapping for long content
        function ImprovedCell($w, $h, $txt, $border=0, $ln=0, $align='L', $fill=false, $maxLen=0) {
            if ($maxLen > 0 && strlen($txt) > $maxLen) {
                $txt = substr($txt, 0, $maxLen) . '...';
            }
            $this->Cell($w, $h, $txt, $border, $ln, $align, $fill);
        }
    }
    
    $pdf = new PDF('L', 'mm', 'A4'); // Landscape orientation
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    
    // Add filter information if applicable
    if ($filter_course || $search_nic) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, 'Applied Filters:', 0, 1);
        $pdf->SetFont('Arial', '', 10);
        if ($filter_course) {
            $pdf->Cell(0, 5, 'Course: ' . $filter_course, 0, 1);
        }
        if ($search_nic) {
            $pdf->Cell(0, 5, 'NIC Search: ' . $search_nic, 0, 1);
        }
        $pdf->Ln(5);
    }
    
    // Table header - Only requested columns
    $pdf->SetFillColor(59, 89, 152); // Professional blue color
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 10);
    
    // Adjusted column widths for better visibility and fit
    $w = array(25, 45, 30, 25, 65, 50, 50); 
    // Total: 25+45+30+25+65+50+50 = 290mm (fits well in landscape A4)
    
    $headers = array('Student ID', 'Full Name', 'NIC', 'Contact', 'Address', 'Course 1', 'Course 2');
    
    for($i = 0; $i < count($headers); $i++) {
        $pdf->Cell($w[$i], 8, $headers[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Table data
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $fill = false;
    
    if (!empty($students)) {
        foreach ($students as $student) {
            // Alternate row background for better readability
            $fill = !$fill;
            if ($fill) {
                $pdf->SetFillColor(245, 245, 245);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            
            // Student ID
            $pdf->ImprovedCell($w[0], 7, $student['Student_id'] ?? 'N/A', 1, 0, 'L', $fill);
            
            // Full Name
            $pdf->ImprovedCell($w[1], 7, $student['full_name'] ?? 'N/A', 1, 0, 'L', $fill, 30);
            
            // NIC
            $pdf->ImprovedCell($w[2], 7, $student['nic'] ?? 'N/A', 1, 0, 'L', $fill);
            
            // Contact
            $pdf->ImprovedCell($w[3], 7, $student['contact_no'] ?? 'N/A', 1, 0, 'L', $fill);
            
            // Address
            $pdf->ImprovedCell($w[4], 7, $student['address'] ?? 'N/A', 1, 0, 'L', $fill, 35);
            
            // Course 1
            $pdf->ImprovedCell($w[5], 7, $student['course_option_one'] ?? 'N/A', 1, 0, 'L', $fill, 30);
            
            // Course 2
            $pdf->ImprovedCell($w[6], 7, $student['course_option_two'] ?? 'N/A', 1, 0, 'L', $fill, 30);
            
            $pdf->Ln();
        }
    } else {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(array_sum($w), 10, 'No records found matching the selected criteria', 1, 1, 'C');
    }
    
    // Summary
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(60, 8, 'Total Records: ' . count($students), 1, 0, 'C', true);
    $pdf->Cell(0, 8, '', 0, 1); // Empty cell for proper line break
    
    // Output PDF as download
    $pdf->Output('D', $filename);
}

// Close database connections
if ($stmt) $stmt->close();
if (isset($con)) $con->close();

// Exit to prevent any additional output
exit();
?>