<?php
require('../../include/fpdf/fpdf.php'); 
include_once('../../include/connection.php');

// 1. Check Course ID
if (!isset($_GET['course_id']) || empty($_GET['course_id'])) {
    die("Error: No Course Selected.");
}
$course_id = intval($_GET['course_id']);

class PDF extends FPDF
{
    // Variable to hold the course name and code for the header
    public $headerParams = [];

    function Header()
    {
        $this->SetFont('Arial','B',16);
        // UPDATED: Changed title to Unit Description
        $this->Cell(0,10,'Unit Description',0,1,'C');
        
        // UPDATED: Display Course Code + Course Name as subtitle
        if (isset($this->headerParams['course_name'])) {
            $this->SetFont('Arial','I',11);
            
            $course_display = 'Course: ';
            if (!empty($this->headerParams['course_no'])) {
                $course_display .= $this->headerParams['course_no'] . ' - ';
            }
            $course_display .= $this->headerParams['course_name'];

            $this->Cell(0,10, $course_display, 0, 1, 'C');
        }
        $this->Ln(5);
        
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200,220,255); 
        
        // Define Columns
        // UPDATED: Changed 'Module Name' to 'Unit Name'
        $this->Cell(145,10,'Unit Name',1,0,'C',true);
        $this->Cell(45,10,'Code/Letter',1,1,'C',true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// 2. Fetch Data (UPDATED to select c.course_no)
$query = "SELECT m.module_name, m.order_no, c.course_name, c.course_no 
          FROM modules m 
          LEFT JOIN course c ON m.course_id = c.id 
          WHERE m.course_id = ? 
          ORDER BY m.order_no ASC, m.module_name ASC";

$stmt = $con->prepare($query);
if (!$stmt) { die("Database Error: " . $con->error); }

$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. Setup PDF
$pdf = new PDF();
$pdf->AliasNbPages();

// Fetch data into array and capture header info
$course_name = "";
$course_no = "";
$data = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
        // Capture course info from the first valid row found
        if(empty($course_name)) {
            $course_name = $row['course_name'];
            $course_no = $row['course_no'];
        }
    }
}

// Pass info to PDF class
$pdf->headerParams['course_name'] = $course_name;
$pdf->headerParams['course_no'] = $course_no;

$pdf->AddPage();
$pdf->SetFont('Arial','',10);

if (!empty($data)) {
    foreach($data as $row) {
        // Module Name Column (Width 145)
        $name = $row['module_name'];
        if(strlen($name) > 85) { $name = substr($name, 0, 82) . '...'; }
        
        $pdf->Cell(145,10, $name, 1);
        
        // Code/Letter Column (Width 45)
        $pdf->Cell(45,10, $row['order_no'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(0,10,'No modules found for this course.',1,1,'C');
}

$pdf->Output('I', 'modules_list.pdf'); 
$stmt->close();
$con->close();
?>