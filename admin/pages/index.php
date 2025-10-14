<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Side: Illustration & Welcome Text (Hidden on mobile) -->
        <div class="hidden md:flex w-full md:w-1/2 p-8 md:p-12 bg-slate-800 text-white flex-col items-center justify-center">
            <div class="max-w-xs text-center">
                <!-- SVG Illustration -->
                <svg class="w-24 h-24 mx-auto mb-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 14.5C14.4853 14.5 16.5 12.4853 16.5 10C16.5 7.51472 14.4853 5.5 12 5.5C9.51472 5.5 7.5 7.51472 7.5 10C7.5 12.4853 9.51472 14.5 12 14.5Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M18.5 18.5L16.5 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <h1 class="text-3xl font-bold mb-3">Admin Dashboard</h1>
                <p class="text-slate-300">Manage your application with ease and precision. Secure access for authorized personnel only.</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <div class="w-full max-w-md mx-auto">
                <!-- Logo -->
                <div class="flex items-center justify-center mb-8">
                     <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                     <h2 class="ml-3 text-2xl font-bold text-slate-800">Admin Login</h2>
                </div>

                <!-- Form -->
                <form action="#" method="POST">
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email or Username</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>

                    <!-- Password Input -->
                    <div class="mb-5">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                Forgot password?
                            </a>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-300">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>
</html>

