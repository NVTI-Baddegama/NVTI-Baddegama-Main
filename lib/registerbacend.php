<?php
// --- 1. Load PHPMailer classes ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files
require '../include/PHPMailer/Exception.php';
require '../include/PHPMailer/PHPMailer.php';
require '../include/PHPMailer/SMTP.php';

// --- NEW: Load FPDF library ---
require '../include/fpdf/fpdf.php'; 
// --- End Load ---

session_start(); 
include '../include/connection.php';

if (!$con) {
    die("Database connection failed."); 
}

if (isset($_POST['submit'])) {

    // --- 1. දත්ත ලබා ගැනීම ---
    
    // Generate Student ID
    do {
        $StudentID = "VTA_BAD" . rand(100000, 999999);
        $check_id_query = "SELECT Student_id FROM student_enrollments WHERE Student_id = ?";
        $stmt_check_id = $con->prepare($check_id_query);
        $stmt_check_id->bind_param("s", $StudentID);
        $stmt_check_id->execute();
        $check_id_result = $stmt_check_id->get_result();
        $stmt_check_id->close();
    } while ($check_id_result->num_rows > 0);
    
    $fullName = trim($_POST['fullName']);
    $nic = trim($_POST['nic']);
    $address = trim($_POST['address']);
    $dob = trim($_POST['dob']);
    $contactNo = trim($_POST['contactNo']);
    $whatsappNo = trim($_POST['whatsappNo'] ?? '');
    $olPassStatus = trim($_POST['olPassStatus']);
    $olEnglish = trim($_POST['olEnglish'] ?? '');
    $olMaths = trim($_POST['olMaths'] ?? '');
    $olScience = trim($_POST['olScience'] ?? '');
    $alCategory = trim($_POST['alCategory']);
    $courseOptionOne = trim($_POST['courseOptionOne']); 
    $courseOptionTwo = trim($_POST['courseOptionTwo'] ?? ''); 

    // --- 2. Input Validation ---
    if (empty($fullName) || empty($nic) || empty($address) || empty($dob) || empty($contactNo) || empty($olPassStatus) || empty($alCategory) || empty($courseOptionOne)) {
        header("location:../pages/register.php?error=Please_fill_all_required_fields");
        exit();
    }
    if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
        header("location:../pages/register.php?error=Invalid_NIC_format");
        exit();
    }
    if (!preg_match('/^[0-9]{10}$/', $contactNo)) {
        header("location:../pages/register.php?error=Invalid_Contact_Number_format");
        exit();
    }

    // --- 3. Duplicate NIC Check (Removed as per your request) ---
    // (NIC check block is removed)

    // --- 4. දත්ත සමුදායට දත්ත ඇතුළු කිරීම ---
    $insert_query = "INSERT INTO student_enrollments (
        Student_id, full_name, nic, address, dob, contact_no, whatsapp_no, 
        ol_pass_status, ol_english_grade, ol_maths_grade, ol_science_grade, 
        al_category, course_option_one, course_option_two, is_processed, application_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";

    $stmt_insert = $con->prepare($insert_query);
    if (!$stmt_insert) {
        header("location:../pages/register.php?error=Database_prepare_error_inserting_data");
        exit();
    }

    $stmt_insert->bind_param(
        "ssssssssssssss",
        $StudentID, $fullName, $nic, $address, $dob, 
        $contactNo, $whatsappNo, $olPassStatus, $olEnglish, $olMaths, 
        $olScience, $alCategory, $courseOptionOne, $courseOptionTwo
    );

    // Execute statement
    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        
        // --- 5. NEW: GENERATE PDF ---
        
        // Define a temporary path to save the PDF
        $pdf_filename = "Student_App_" . $nic . "_" . time() . ".pdf";
        $pdf_temp_path = "../uploads/temp_pdf/"; // Path from 'lib' folder
        
        // Create directory if it doesn't exist
        if (!is_dir($pdf_temp_path)) {
            mkdir($pdf_temp_path, 0755, true);
        }
        $pdf_file_path = $pdf_temp_path . $pdf_filename;

        // Create PDF object
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, 'New Student Application - NVTI Baddegama', 1, 1, 'C');
        $pdf->Ln(10);

        // --- Add Details to PDF ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Student ID:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $StudentID, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Full Name:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $fullName, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'NIC:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $nic, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Date of Birth:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $dob, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Contact No:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $contactNo, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'WhatsApp No:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $whatsappNo, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Address:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(140, 8, $address, 0, 1);
        $pdf->Ln(5);

        // --- Academic & Course Details ---
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 10, 'Course & Qualifications', 1, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'O/L Status:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $olPassStatus, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'A/L Stream:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $alCategory, 0, 1);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Course Choice 1:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $courseOptionOne, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 8, 'Course Choice 2:', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(140, 8, $courseOptionTwo, 0, 1);

        // Save the PDF to the temp path
        $pdf->Output('F', $pdf_file_path); 
        
        // --- END GENERATE PDF ---


        // --- 6. SEND EMAIL NOTIFICATION (with PDF) ---
        $mail = new PHPMailer(true);
        
        // === මෙතන අගයන් 3 වෙනස් කරන්න ===
        $admin_recipient_email = "infor.chamika@gmail.com"; // <-- 1. මෙතනට email එක ලැබිය යුතු ඔබේ email address එක දාන්න
        $sender_gmail_address = "infor@nvtibaddegama.site"; // <-- 2. මෙතනට App Password එක හැදූ Gmail address එක දාන්න
        $sender_app_password = "3CY+C9*etd9Qz9"; // <-- 3. මෙතනට අකුරු 16ක App Password එක දාන්න (හිස්තැන් නැතුව)
        // ==================================

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $sender_gmail_address;
            $mail->Password   = $sender_app_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom($sender_gmail_address, 'NVTI Baddegama Website');
            $mail->addAddress($admin_recipient_email, 'NVTI Admin');

            // --- NEW: Add the PDF as an attachment ---
            $mail->addAttachment($pdf_file_path, $pdf_filename);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'New Student Application (PDF Attached): ' . $fullName;
            $mail->Body    = "A new student application has been submitted.<br>"
                           . "The student's full details are attached as a PDF file for your records.<br><br>"
                           . "<b>Student Name:</b> " . htmlspecialchars($fullName) . "<br>"
                           . "<b>NIC:</b> " . htmlspecialchars($nic) . "<br>"s
                           . "<b>Course Choice 1:</b> " . htmlspecialchars($courseOptionOne) . "<br><br>"
                           . "Please log in to the admin panel to process this application.";
            
            $mail->AltBody = "New Student Application Received. Details are in the attached PDF. Name: $fullName, NIC: $nic.";

            $mail->send();
            
        } catch (Exception $e) {
            // Email failed. Log error but don't stop the user.
            error_log("PHPMailer Error (lib/registerbacend.php): {$mail->ErrorInfo}");
        }

        // --- 7. NEW: Delete the temporary PDF file ---
        if (file_exists($pdf_file_path)) {
            unlink($pdf_file_path);
        }
        // --- END DELETE ---

        $con->close();
        header("location:../pages/register.php?success=Application_Submitted_Successfully");
        exit();
        
    } else {
        // ... (Error handling - unchanged) ...
    }

} else {
    header("location:../pages/register.php?error=Invalid_Access_Method");
    exit();
}
?>