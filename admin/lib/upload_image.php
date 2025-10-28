<?php
include('../../include/connection.php');

// 1. Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if file and name are set
    if (isset($_POST['image_name']) && isset($_FILES['image_file'])) {
        
        $image_name = trim($_POST['image_name']);
        $file = $_FILES['image_file'];

        // 2. Define upload directory
        $target_dir = "../uploads/gallery/";
        
        // Create a unique file name to prevent overwriting
        $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $unique_file_name = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $unique_file_name;
        
        $uploadOk = 1;
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        // 3. Perform file validation
        
        // Check if image file is a actual image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            die("Error: File is not an image.");
            $uploadOk = 0;
        }

        // Check file size (e.g., 5MB limit)
        if ($file["size"] > 5000000) {
            die("Error: Your file is too large (over 5MB).");
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($file_extension, $allowed_types)) {
            die("Error: Only JPG, JPEG, PNG & GIF files are allowed.");
            $uploadOk = 0;
        }

        // 4. Try to upload file if all checks pass
        if ($uploadOk == 1) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                
                // 5. Insert into database
                $sql = "INSERT INTO gallery (image_name, image_path) VALUES (?, ?)";
                
                // Prepare the statement using $con
                $stmt = $con->prepare($sql);
                
                if ($stmt === false) {
                    die("Database prepare error: " . $con->error);
                }
                
                // Bind parameters ('ss' means two string parameters)
                $stmt->bind_param("ss", $image_name, $target_file);
                
                // Execute and check for errors
                if ($stmt->execute()) {
                    // 6. Redirect back to the manage page
                    // This path goes UP from 'lib' and DOWN into 'pages'
                    header("Location: ../pages/manage images.php");
                    exit;
                } else {
                    die("Database execute error: " . $stmt->error);
                }
        }
    } else {
        die("Error: Form data is incomplete.");
    }
} else {
    // Not a POST request
    header("Location: manage_images.php");
    exit;
}
}
?>