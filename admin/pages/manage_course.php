<?php 
include('../../include/connection.php');

session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course Dashboard</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
    <style>
        /* Apply the Inter font to the whole page */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-6 sm:p-8">

    <div class="max-w-7xl mx-auto">

        <!-- Header Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Manage Courses</h2>



        <!-- Manage Courses Table -->
        <div class="bg-white p-6 rounded-xl shadow-lg mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Manage Courses</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tl-lg">
                                Course Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider rounded-tr-lg">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Row 1 -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">JavaScript Mastery</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Anya Sharma</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$199</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 transition duration-150">Edit</button>
                                <button class="text-red-600 hover:text-red-900 transition duration-150">Delete</button>
                            </td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Cloud Computing Fundamentals</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dr. Ken Thompson</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$149</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 transition duration-150">Edit</button>
                                <button class="text-red-600 hover:text-red-900 transition duration-150">Delete</button>
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">UX/UI Design Principles</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jane Doe</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$99</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 transition duration-150">Edit</button>
                                <button class="text-red-600 hover:text-red-900 transition duration-150">Delete</button>
                            </td>
                        </tr>
                        <!-- Row 4 -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Introduction to Python</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Anya Sharma</td>
                            <td class*="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$129</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900 transition duration-150">Edit</button>
                                <button class="text-red-600 hover:text-red-900 transition duration-150">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>


</body>
</html>

