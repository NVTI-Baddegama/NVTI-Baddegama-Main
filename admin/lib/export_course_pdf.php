<?php
// Adjust path: admin/lib -> up 2 levels -> include -> fpdf
require('../../include/fpdf/fpdf.php'); 
include_once('../../include/connection.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'Course List',0,1,'C');
        $this->Ln(5);
        
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200,220,255); // Light Blue
        
        // Define Column Widths (Total approx 190 for A4 Portrait)
        $this->Cell(30,10,'Course No',1,0,'C',true);
        $this->Cell(80,10,'Course Name',1,0,'C',true); // Wider for name
        $this->Cell(30,10,'Fee',1,0,'C',true);
        $this->Cell(25,10,'Type',1,0,'C',true);
        $this->Cell(25,10,'Duration',1,1,'C',true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Fetch Data
$query = "SELECT course_no, course_name, course_fee, course_type, course_duration FROM course ORDER BY course_no ASC";
$stmt = $con->prepare($query);

if (!$stmt) {
    die("Database Query Failed: " . $con->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Init PDF
$pdf = new PDF();
$pdf->AliasNbPages(); 
$pdf->AddPage();
$pdf->SetFont('Arial','',9); // Slightly smaller font to fit names

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pdf->Cell(30,10,$row['course_no'],1);
        
        // Handle long names roughly (Truncate if too long or just let it overflow)
        // Using basic Cell here. MultiCell is complex in tables. 
        // We will limit string length to keep layout clean.
        $name = $row['course_name'];
        if(strlen($name) > 45) { $name = substr($name, 0, 42) . '...'; }
        
        $pdf->Cell(80,10,$name,1);
        $pdf->Cell(30,10,$row['course_fee'],1);
        $pdf->Cell(25,10,$row['course_type'],1);
        $pdf->Cell(25,10,$row['course_duration'],1,1,'C');
    }
} else {
    $pdf->Cell(0,10,'No courses found.',1,1,'C');
}

$pdf->Output('I', 'course_list.pdf'); 
$stmt->close();
$con->close();
?>