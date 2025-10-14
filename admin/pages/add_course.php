<?php include '../include/header.php'; ?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Add New Course</h2>

<div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 border-t-4 border-teal-500 w-full lg:w-1/2 mx-auto">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Enter Course Details</h3>

    <form action="handlers/course_handler.php" method="POST" class="mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label for="course_no" class="block text-sm font-medium text-gray-700 mb-1">Course Number</label>
                <input type="text" id="course_no" name="course_no" required maxlength="12"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., CS101">
            </div>

            <div>
                <label for="course_name" class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
                <input type="text" id="course_name" name="course_name" required maxlength="100"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., Introduction to Web Development">
            </div>

            <div>
                <label for="nvq_level" class="block text-sm font-medium text-gray-700 mb-1">NVQ Level</label>
                <select id="nvq_level" name="nvq_level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="" disabled selected>Select a level</option>
                    <option value="1">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 4</option>
                    <option value="5">Level 5</option>
                    <option value="6">Level 6</option>
                    <option value="7">Level 7</option>
                </select>
            </div>
            
            <div>
                <label for="course_type" class="block text-sm font-medium text-gray-700 mb-1">Course Type</label>
                <select id="course_type" name="course_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="" disabled selected>Select a type</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Online">Online</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-1">Qualifications</label>
                <input type="text" id="qualifications" name="qualifications" required maxlength="255"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., GCE A/L or equivalent">
            </div>

            <div>
                <label for="course_duration" class="block text-sm font-medium text-gray-700 mb-1">Course Duration (in months)</label>
                <input type="number" id="course_duration" name="course_duration" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., 6">
            </div>

            <div>
                <label for="course_fee" class="block text-sm font-medium text-gray-700 mb-1">Course Fee</label>
                <input type="text" id="course_fee" name="course_fee" required maxlength="20"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g., LKR 50,000">
            </div>

            <div class="md:col-span-2">
                <label for="course_description" class="block text-sm font-medium text-gray-700 mb-1">Course Description</label>
                <textarea id="course_description" name="course_description" rows="4" required maxlength="255"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Briefly describe the course content and objectives."></textarea>
            </div>
        </div>

        <div class="mt-8 text-right">
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Save New Course
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8 w-full">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Recently Added Courses</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NVQ Level</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">WD201</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Advanced JavaScript</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Level 5</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">6 Months</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">LKR 75,000</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">GD101</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">UI/UX Design Principles</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Level 4</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 Months</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">LKR 60,000</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PM305</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Agile Project Management</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Level 6</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 Months</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">LKR 55,000</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../include/footer.php'; ?>