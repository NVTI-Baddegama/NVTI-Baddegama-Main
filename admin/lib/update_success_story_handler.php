<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . '/../../include/connection.php');

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Form දත්ත සහ සැඟවූ ID එක ලබා ගැනීම
    $story_id = (int)$_POST['story_id'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $position = mysqli_real_escape_string($con, $_POST['position']);
    $company_name = mysqli_real_escape_string($con, $_POST['company_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $contact_details = mysqli_real_escape_string($con, $_POST['contact_details']);
    $youtube_url = mysqli_real_escape_string($con, $_POST['youtube_url']);
    
    // 2. Image Path එක: මුලින්ම පරණ Path එක default ලෙස සකස් කිරීම
    $db_image_path = mysqli_real_escape_string($con, $_POST['existing_image_path']);

    // 3. අලුත් පින්තූරයක් upload කර ඇත්දැයි පරීක්ෂා කිරීම
    if (isset($_FILES['story_image']) && $_FILES['story_image']['error'] == 0) {
        
        $upload_dir = __DIR__ . '/../../uploads/success_stories/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = uniqid() . '_' . basename($_FILES['story_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        // අලුත් ගොනුව අදාළ ස්ථානයට Move කිරීම
        if (move_uploaded_file($_FILES['story_image']['tmp_name'], $target_file)) {
            
            // (a) DB path එක අලුත් path එකට වෙනස් කිරීම
            $db_image_path = 'uploads/success_stories/' . $file_name;
            
            // (b) පැරණි පින්තූරය server එකෙන් delete කිරීම (එය $db_image_path එක නොවේ)
            $old_image_path_from_form = $_POST['existing_image_path'];
            if (!empty($old_image_path_from_form)) {
                $old_file_to_delete = __DIR__ . '/../../' . $old_image_path_from_form;
                if (file_exists($old_file_to_delete)) {
                    unlink($old_file_to_delete);
                }
            }
        } else {
            $message = "Error uploading new image.";
            $message_type = "error";
        }
    }

    // 4. දත්ත ගබඩාව UPDATE කිරීම
    if (empty($message)) { // පින්තූරය upload කිරීමේදී දෝෂයක් නොමැති නම්
        
        $sql = "UPDATE success_stories SET 
                    name = ?, 
                    position = ?, 
                    company_name = ?, 
                    description = ?, 
                    image_path = ?, 
                    contact_details = ?, 
                    youtube_url = ? 
                WHERE id = ?";
        
        $stmt = $con->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssssi", 
                $name, 
                $position, 
                $company_name, 
                $description, 
                $db_image_path, 
                $contact_details, 
                $youtube_url,
                $story_id  // ID එක අවසානයට
            );
            
            if ($stmt->execute()) {
                $message = "Success story updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating story: " . $stmt->error;
                $message_type = "error";
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $con->error;
            $message_type = "error";
        }
    }
    
    // 5. පරිශීලකයා නැවත 'Manage' පිටුවට යොමු කිරීම
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type;
    header("Location: ../pages/manage_success_stories.php");
    exit();
}
?>