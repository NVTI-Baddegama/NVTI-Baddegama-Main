<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['staff_id'])) {
    header("Location: ../office/dashboard.php");
    exit();
}
if (isset($_SESSION['admin_username'])) {
    header("Location: ../admin/pages/Dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff & Admin Login - NVTI Baddegama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Apply Poppins font to the body */
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Custom styles for the background gradient and glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="glass-effect rounded-2xl shadow-2xl p-8 space-y-6">
            
            <div class="text-center">
                <img src="../images/logo/NVTI_logo.png" alt="NVTI Logo" class="w-20 h-20 mx-auto mb-4 bg-white rounded-full p-2">
                <h2 class="text-3xl font-bold text-white">Welcome Back</h2>
                <p class="text-gray-300">Login to your account</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="bg-red-500/30 text-red-200 p-3 rounded-lg text-center text-sm">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="bg-green-500/30 text-green-200 p-3 rounded-lg text-center text-sm">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <form action="login_process.php" method="POST" class="space-y-6">
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <input type="text" id="staff_id" name="staff_id" required placeholder="Staff ID / Service ID / Username"
                           class="w-full bg-gray-700/50 text-white border-2 border-gray-600 rounded-lg py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-300">
                </div>

                <div class="relative">
                     <span class="absolute left-4 top-3.5 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="password" name="password" required placeholder="Password"
                           class="w-full bg-gray-700/50 text-white border-2 border-gray-600 rounded-lg py-3 pl-12 pr-4 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition duration-300">
                </div>

                <button type="submit"
                        class="w-full bg-sky-600 text-white font-semibold py-3 rounded-lg shadow-lg hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-500/50 transition duration-300 transform hover:scale-105">
                    Login
                </button>
            </form>
            
            <div class="text-center text-gray-400 text-sm space-y-2">
                <p><a href="../index.php" class="hover:underline">← Back to Home</a></p>
            </div>
        </div>
    </div>

</body>
</html>