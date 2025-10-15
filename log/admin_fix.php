<?php

// echo "<style>body { font-family: sans-serif; padding: 20px; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; }</style>";
// echo "<h1>Admin Password Fixer</h1>";

// // Database connection eka ganna
// include '../include/connection.php';

// if ($con->connect_error) {
//     die("<p class='error'>Database Connection Failed: " . $con->connect_error . "</p>");
// }

// // Admin ge details meywai
// $admin_username = 'Admin';
// $admin_password = 'Admin@123';
// $admin_email = 'Admin@gmail.com';
// $admin_serviceid = 'S001';

// // Password eke MD5 eka hadanna
// $correct_md5_hash = md5($admin_password);

// echo "<p>Correct MD5 Hash for 'Admin@123' is: <b>" . $correct_md5_hash . "</b></p>";

// // 1. Purana Admin kenawa ainkaranna (Delete)
// $delete_sql = "DELETE FROM `admin` WHERE `username` = ?";
// $stmt_delete = $con->prepare($delete_sql);
// $stmt_delete->bind_param("s", $admin_username);

// if ($stmt_delete->execute()) {
//     echo "<p>Step 1: Old admin user ('Admin') successfully deleted (if existed).</p>";
// } else {
//     echo "<p class='error'>Step 1 Error: Could not delete old admin user. " . $stmt_delete->error . "</p>";
// }

// // 2. Aluthinma, hari password eka ekka Admin kenek hadanna
// $insert_sql = "INSERT INTO `admin` (`username`, `email`, `password`, `serviceid`) VALUES (?, ?, ?, ?)";
// $stmt_insert = $con->prepare($insert_sql);
// $stmt_insert->bind_param("ssss", $admin_username, $admin_email, $correct_md5_hash, $admin_serviceid);

// if ($stmt_insert->execute()) {
//     echo "<p class='success'>Step 2: New admin user created successfully!</p>";
//     echo "<h2>DONE! You can now log in as Admin.</h2>";
// } else {
//     echo "<p class='error'>Step 2 Error: Could not create new admin user. " . $stmt_insert->error . "</p>";
// }

// $con->close();

?>