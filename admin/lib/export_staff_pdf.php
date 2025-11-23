<?php
// Adjust path: admin/lib -> up 2 levels -> include -> fpdf
require('../../include/fpdf/fpdf.php'); 
include_once('../../include/connection.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'Staff Member List',0,1,'C');
        $this->Ln(5);
        
        $this->SetFont('Arial','B',10);
        $this->SetFillColor(200,220,255); 
        
        $this->Cell(30,10,'Service ID',1,0,'C',true);
        $this->Cell(60,10,'Full Name',1,0,'C',true);
        $this->Cell(60,10,'Position',1,0,'C',true);
        $this->Cell(30,10,'Status',1,1,'C',true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$query = "SELECT service_id, first_name, last_name, position, status FROM staff ORDER BY position ASC, first_name ASC";
$stmt = $con->prepare($query);

if (!$stmt) {
    die("Database Query Failed: " . $con->error);
}

$stmt->execute();
$result = $stmt->get_result();

$pdf = new PDF();
$pdf->AliasNbPages(); 
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pdf->Cell(30,10,$row['service_id'],1);
        $pdf->Cell(60,10,$row['first_name'] . ' ' . $row['last_name'],1);
        $pdf->Cell(60,10,$row['position'],1);
        
        if (strtolower($row['status']) === 'inactive') {
            $pdf->SetTextColor(190, 0, 0);
        }
        $pdf->Cell(30,10,ucfirst($row['status']),1,1,'C');
        $pdf->SetTextColor(0); 
    }
} else {
    $pdf->Cell(0,10,'No staff records found.',1,1,'C');
}

$pdf->Output('I', 'staff_list.pdf'); 
$stmt->close();
$con->close();
?>