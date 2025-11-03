<?php
session_start();
include '../include/connection.php';

// Check if user is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: ../log/login.php");
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $action = $_POST['action']; // 'accept' or 'reject'
    $staff_id = $_SESSION['staff_id'];
    $position = $_SESSION['position'];
    $course_no = $_SESSION['course_no'];
    
    // Get current instructor's course name
    $course_query = "SELECT course_name FROM course WHERE course_no = ?";
    $course_stmt = $con->prepare($course_query);
    $course_stmt->bind_param("s", $course_no);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    $instructor_course = $course_result->fetch_assoc();
    $instructor_course_name = $instructor_course['course_name'];
    
    // Get student details
    $student_query = "SELECT * FROM student_enrollments WHERE id = ?";
    $student_stmt = $con->prepare($student_query);
    $student_stmt->bind_param("i", $student_id);
    $student_stmt->execute();
    $student_result = $student_stmt->get_result();
    $student = $student_result->fetch_assoc();
    
    if (!$student) {
        $response['message'] = 'Student not found.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    if ($action === 'accept') {
        // Update student status to processed (accepted)
        $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("i", $student_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Student application accepted successfully.';
        } else {
            $response['message'] = 'Error accepting student application.';
        }
        
    } elseif ($action === 'reject') {
        // Check if this is rejection from Course Choice 1 or Course Choice 2
        if ($student['course_option_one'] === $instructor_course_name) {
            // This is rejection from Course Choice 1
            // Check if student has Course Choice 2
            if (!empty($student['course_option_two']) && $student['course_option_two'] !== $student['course_option_one']) {
                // Move Course Choice 2 to Course Choice 1 and clear Course Choice 2
                $update_query = "UPDATE student_enrollments SET 
                                course_option_one = course_option_two, 
                                course_option_two = NULL,
                                is_processed = 0
                                WHERE id = ?";
                $stmt = $con->prepare($update_query);
                $stmt->bind_param("i", $student_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Student rejected from Course Choice 1. Student has been moved to Course Choice 2 instructor for review.';
                } else {
                    $response['message'] = 'Error processing rejection.';
                }
            } else {
                // No Course Choice 2, remove student from database
                $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
                $stmt = $con->prepare($delete_query);
                $stmt->bind_param("i", $student_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Student rejected and removed from database (no second course choice available).';
                } else {
                    $response['message'] = 'Error removing student application.';
                }
            }
        } else {
            // This is rejection from Course Choice 2 (which is now Course Choice 1 after first rejection)
            // Remove student from database completely
            $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
            $stmt = $con->prepare($delete_query);
            $stmt->bind_param("i", $student_id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Student rejected from final course choice and removed from database.';
            } else {
                $response['message'] = 'Error removing student application.';
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>