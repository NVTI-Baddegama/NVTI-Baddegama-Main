<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once(__DIR__ . '/../../include/connection.php');

$message = "";
$message_type = "";

if (isset($_GET['id']) && isset($_GET['action'])) {
    
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'toggle') {
        // 1. Toggle (Approve / Hide)
        
        // Current status එක ලබා ගැනීම
        $stmt_select = $con->prepare("SELECT is_active FROM success_stories WHERE id = ?");
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_status = $row['is_active'];
            
            // Status එක Toggle කිරීම (1 නම් 0 කිරීම, 0 නම් 1 කිරීම)
            $new_status = ($current_status == 1) ? 0 : 1;
            
            $stmt_update = $con->prepare("UPDATE success_stories SET is_active = ? WHERE id = ?");
            $stmt_update->bind_param("ii", $new_status, $id);
            
            if ($stmt_update->execute()) {
                $message = ($new_status == 1) ? "Story Approved!" : "Story Hidden!";
                $message_type = "success";
            } else {
                $message = "Error updating status.";
                $message_type = "error";
            }
            $stmt_update->close();
        }
        $stmt_select->close();

    } elseif ($action == 'delete') {
        // 2. Delete
        
        // (a) පළමුව පින්තූරය server එකෙන් delete කිරීම සඳහා path එක ලබා ගැනීම
        $stmt_img = $con->prepare("SELECT image_path FROM success_stories WHERE id = ?");
        $stmt_img->bind_param("i", $id);
        $stmt_img->execute();
        $result_img = $stmt_img->get_result();
        
        if ($result_img->num_rows > 0) {
            $row_img = $result_img->fetch_assoc();
            $image_path = $row_img['image_path'];
            
            if (!empty($image_path)) {
                // Root directory එකේ සිට path එක
                $file_to_delete = __DIR__ . '/../../' . $image_path; 
                if (file_exists($file_to_delete)) {
                    unlink($file_to_delete); // පින්තූරය delete කිරීම
                }
            }
        }
        $stmt_img->close();

        // (b) දැන් දත්ත ගබඩාවෙන් record එක delete කිරීම
        $stmt_delete = $con->prepare("DELETE FROM success_stories WHERE id = ?");
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            $message = "Story deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Error deleting record.";
            $message_type = "error";
        }
        $stmt_delete->close();
    }
}

// 3. Manage පිටුවට නැවත යොමු කිරීම
$_SESSION['message'] = $message;
$_SESSION['message_type'] = $message_type;
header("Location: ../pages/manage_success_stories.php");
exit();
?>