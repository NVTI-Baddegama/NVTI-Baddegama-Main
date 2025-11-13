<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// පොදු connection ගොනුවට Path එක
include_once(__DIR__ . '/../include/connection.php');

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Form දත්ත ලබා ගැනීම
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $position = mysqli_real_escape_string($con, $_POST['position']);
    $company_name = mysqli_real_escape_string($con, $_POST['company_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $contact_details = mysqli_real_escape_string($con, $_POST['contact_details']);
    $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
    $db_image_path = NULL;

    // 2. පින්තූරය Upload කිරීමේ ක්‍රියාවලිය
    if (isset($_FILES['story_image']) && $_FILES['story_image']['error'] == 0) {
        
        // 'uploads/success_stories/' ෆෝල්ඩරයට Path එක
        $upload_dir = __DIR__ . '/../uploads/success_stories/'; 
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['story_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['story_image']['tmp_name'], $target_file)) {
            $db_image_path = 'uploads/success_stories/' . $file_name;
        } else {
            $message = "Error uploading image. Please try again.";
            $message_type = "error";
        }
    }

    // 3. දත්ත ගබඩාවට ඇතුළත් කිරීම (INSERT)
    if (empty($message)) { 
        
        // වැදගත්ම කොටස: is_active = 0 (Pending) ලෙස සකස් කිරීම
        $sql = "INSERT INTO success_stories (name, position, company_name, description, image_path, contact_details, youtube_url, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)"; // 0 = Pending
        
        $stmt = $con->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssss", 
                $name, 
                $position, 
                $company_name, 
                $description, 
                $db_image_path, 
                $contact_details, 
                $youtube_url
            );
            
            if ($stmt->execute()) {
                $message = "Thank you! Your story has been submitted for review.";
                $message_type = "success";
            } else {
                $message = "Error submitting your story. Please try again. " . $stmt->error;
                $message_type = "error";
            }
            $stmt->close();
        } else {
            $message = "Error preparing submission. " . $con->error;
            $message_type = "error";
        }
    }
    
    // 4. පරිශීලකයා නැවත Form පිටුවට යොමු කිරීම
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type;
    header("Location: ../pages/submit_story.php");
    exit();
}
?>