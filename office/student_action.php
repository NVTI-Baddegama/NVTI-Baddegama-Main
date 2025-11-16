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
    try {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $staff_id = $_SESSION['staff_id'];
        $position = $_SESSION['position'];
        
        if (empty($action)) {
            $response['message'] = 'No action specified.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // Get current instructor's assigned courses
        $assigned_courses = [];
        $course_query = "SELECT ic.course_no, c.course_name 
                         FROM instructor_courses ic
                         JOIN course c ON ic.course_no = c.course_no
                         WHERE ic.staff_id = ? AND ic.status = 'active'";
        $course_stmt = $con->prepare($course_query);
        
        if (!$course_stmt) {
            $response['message'] = 'Database error: ' . $con->error;
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        $course_stmt->bind_param("s", $staff_id);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result();
        
        while ($course_info = $course_result->fetch_assoc()) {
            $assigned_courses[] = $course_info['course_name'];
        }
        $course_stmt->close();
        
        if (empty($assigned_courses)) {
            $response['message'] = 'No courses assigned to your profile.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
        
        // Get the active course from POST or first assigned course
        $active_course_name = $assigned_courses[0];
        if (isset($_POST['course_name']) && !empty($_POST['course_name'])) {
            $active_course_name = $_POST['course_name'];
        }
        
        // Handle bulk accept all pending
        if ($action === 'bulk_accept_all_pending') {
            // Get all pending students for this course
            $pending_query = "SELECT id FROM student_enrollments 
                             WHERE course_option_one = ? AND is_processed = 0";
            $pending_stmt = $con->prepare($pending_query);
            
            if (!$pending_stmt) {
                $response['message'] = 'Database error: ' . $con->error;
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            
            $pending_stmt->bind_param("s", $active_course_name);
            $pending_stmt->execute();
            $pending_result = $pending_stmt->get_result();
            
            $pending_count = 0;
            $accepted_count = 0;
            $errors = [];
            
            while ($pending_student = $pending_result->fetch_assoc()) {
                $pending_count++;
                $student_id = $pending_student['id'];
                
                // Accept each pending student
                $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE id = ?";
                $update_stmt = $con->prepare($update_query);
                
                if (!$update_stmt) {
                    $errors[] = "Failed to prepare statement for student ID {$student_id}";
                    continue;
                }
                
                $update_stmt->bind_param("i", $student_id);
                
                if ($update_stmt->execute()) {
                    $accepted_count++;
                } else {
                    $errors[] = "Failed to accept student ID {$student_id}: " . $update_stmt->error;
                }
                $update_stmt->close();
            }
            $pending_stmt->close();
            
            if ($pending_count === 0) {
                $response['message'] = 'No pending students found for this course.';
            } else {
                $response['success'] = true;
                $response['message'] = "Successfully accepted {$accepted_count} out of {$pending_count} pending student(s).";
                if (!empty($errors)) {
                    $response['message'] .= "\n\nErrors: " . implode(", ", $errors);
                }
            }
            
        // Handle bulk reject all pending
        } elseif ($action === 'bulk_reject_all_pending') {
            // Get all pending students for this course
            $pending_query = "SELECT * FROM student_enrollments 
                             WHERE course_option_one = ? AND is_processed = 0";
            $pending_stmt = $con->prepare($pending_query);
            
            if (!$pending_stmt) {
                $response['message'] = 'Database error: ' . $con->error;
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            
            $pending_stmt->bind_param("s", $active_course_name);
            $pending_stmt->execute();
            $pending_result = $pending_stmt->get_result();
            
            $pending_count = 0;
            $rejected_count = 0;
            $moved_count = 0;
            $deleted_count = 0;
            $errors = [];
            
            while ($student = $pending_result->fetch_assoc()) {
                $pending_count++;
                $student_id = $student['id'];
                
                // Check if student has Course Choice 2
                if (!empty($student['course_option_two']) && $student['course_option_two'] !== $student['course_option_one']) {
                    // Move Course Choice 2 to Course Choice 1 and clear Course Choice 2
                    $update_query = "UPDATE student_enrollments SET 
                                    course_option_one = course_option_two, 
                                    course_option_two = NULL,
                                    is_processed = 0
                                    WHERE id = ?";
                    $update_stmt = $con->prepare($update_query);
                    
                    if (!$update_stmt) {
                        $errors[] = "Failed to prepare update for student ID {$student_id}";
                        continue;
                    }
                    
                    $update_stmt->bind_param("i", $student_id);
                    
                    if ($update_stmt->execute()) {
                        $moved_count++;
                        $rejected_count++;
                    } else {
                        $errors[] = "Failed to move student ID {$student_id}: " . $update_stmt->error;
                    }
                    $update_stmt->close();
                } else {
                    // No Course Choice 2, remove student from database
                    $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
                    $delete_stmt = $con->prepare($delete_query);
                    
                    if (!$delete_stmt) {
                        $errors[] = "Failed to prepare delete for student ID {$student_id}";
                        continue;
                    }
                    
                    $delete_stmt->bind_param("i", $student_id);
                    
                    if ($delete_stmt->execute()) {
                        $deleted_count++;
                        $rejected_count++;
                    } else {
                        $errors[] = "Failed to delete student ID {$student_id}: " . $delete_stmt->error;
                    }
                    $delete_stmt->close();
                }
            }
            $pending_stmt->close();
            
            if ($pending_count === 0) {
                $response['message'] = 'No pending students found for this course.';
            } else {
                $response['success'] = true;
                $response['message'] = "Successfully rejected {$rejected_count} pending student(s). ";
                if ($moved_count > 0) {
                    $response['message'] .= "{$moved_count} student(s) moved to their Course Choice 2 instructor. ";
                }
                if ($deleted_count > 0) {
                    $response['message'] .= "{$deleted_count} student(s) removed from database (no second course choice).";
                }
                if (!empty($errors)) {
                    $response['message'] .= "\n\nErrors: " . implode(", ", $errors);
                }
            }
            
        // Handle bulk accept (with selection)
        } elseif ($action === 'bulk_accept') {
            if (!isset($_POST['student_ids'])) {
                $response['message'] = 'No student IDs provided.';
            } else {
                $student_ids = json_decode($_POST['student_ids'], true);
                
                if (empty($student_ids)) {
                    $response['message'] = 'No students selected.';
                } else {
                    $accepted_count = 0;
                    $errors = [];
                    
                    foreach ($student_ids as $student_id) {
                        $update_query = "UPDATE student_enrollments SET is_processed = 1 WHERE id = ?";
                        $stmt = $con->prepare($update_query);
                        
                        if (!$stmt) {
                            $errors[] = "Failed to prepare for student ID {$student_id}";
                            continue;
                        }
                        
                        $stmt->bind_param("i", $student_id);
                        
                        if ($stmt->execute()) {
                            $accepted_count++;
                        } else {
                            $errors[] = "Failed to accept student ID {$student_id}: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                    
                    $response['success'] = true;
                    $response['message'] = "Successfully accepted {$accepted_count} student(s).";
                    if (!empty($errors)) {
                        $response['message'] .= "\n\nErrors: " . implode(", ", $errors);
                    }
                }
            }
            
        // Handle single student action
        } elseif (isset($_POST['student_id'])) {
            $student_id = $_POST['student_id'];
            
            // Get student details
            $student_query = "SELECT * FROM student_enrollments WHERE id = ?";
            $student_stmt = $con->prepare($student_query);
            
            if (!$student_stmt) {
                $response['message'] = 'Database error: ' . $con->error;
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            
            $student_stmt->bind_param("i", $student_id);
            $student_stmt->execute();
            $student_result = $student_stmt->get_result();
            $student = $student_result->fetch_assoc();
            $student_stmt->close();
            
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
                
                if (!$stmt) {
                    $response['message'] = 'Database error: ' . $con->error;
                } else {
                    $stmt->bind_param("i", $student_id);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Student application accepted successfully.';
                    } else {
                        $response['message'] = 'Error accepting student application: ' . $stmt->error;
                    }
                    $stmt->close();
                }
                
            } elseif ($action === 'reject') {
                // Check if this is rejection from Course Choice 1 or Course Choice 2
                if ($student['course_option_one'] === $active_course_name) {
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
                        
                        if (!$stmt) {
                            $response['message'] = 'Database error: ' . $con->error;
                        } else {
                            $stmt->bind_param("i", $student_id);
                            
                            if ($stmt->execute()) {
                                $response['success'] = true;
                                $response['message'] = 'Student rejected from Course Choice 1. Student has been moved to Course Choice 2 instructor for review.';
                            } else {
                                $response['message'] = 'Error processing rejection: ' . $stmt->error;
                            }
                            $stmt->close();
                        }
                    } else {
                        // No Course Choice 2, remove student from database
                        $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
                        $stmt = $con->prepare($delete_query);
                        
                        if (!$stmt) {
                            $response['message'] = 'Database error: ' . $con->error;
                        } else {
                            $stmt->bind_param("i", $student_id);
                            
                            if ($stmt->execute()) {
                                $response['success'] = true;
                                $response['message'] = 'Student rejected and removed from database (no second course choice available).';
                            } else {
                                $response['message'] = 'Error removing student application: ' . $stmt->error;
                            }
                            $stmt->close();
                        }
                    }
                } else {
                    // This is rejection from Course Choice 2 (which is now Course Choice 1 after first rejection)
                    // Remove student from database completely
                    $delete_query = "DELETE FROM student_enrollments WHERE id = ?";
                    $stmt = $con->prepare($delete_query);
                    
                    if (!$stmt) {
                        $response['message'] = 'Database error: ' . $con->error;
                    } else {
                        $stmt->bind_param("i", $student_id);
                        
                        if ($stmt->execute()) {
                            $response['success'] = true;
                            $response['message'] = 'Student rejected from final course choice and removed from database.';
                        } else {
                            $response['message'] = 'Error removing student application: ' . $stmt->error;
                        }
                        $stmt->close();
                    }
                }
            }
        } else {
            $response['message'] = 'Invalid request: No student ID or valid action provided.';
        }
        
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>