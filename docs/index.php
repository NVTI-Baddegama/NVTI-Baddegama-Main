<?php
// This file is /docs/downloads.php

// 1. Include the public header (path is '../include/')
include_once('../include/header.php');

// 2. Scan this directory (docs/) for files to list
$directory = __DIR__; // Gets the full server path of the current (docs) folder
$all_files = scandir($directory);

$allowed_extensions = ['pdf', 'doc', 'docx','xlsx']; // ඔබට පෙන්වීමට අවශ්‍ය file types
$download_files = []; // Array to store valid files

foreach ($all_files as $file) {
    // Skip current directory (.), parent directory (..), and this PHP file itself
    // Also skip index.php if it exists
    if ($file == '.' || $file == '..' || $file == 'downloads.php' || $file == 'index.php') {
        continue;
    }

    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Check if the file extension is in our allowed list
    if (in_array($file_extension, $allowed_extensions)) {
        $download_files[] = $file; // Add to our list
    }
}

// Sort files alphabetically (optional, but cleaner)
sort($download_files);

?>

<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-12">

        <header class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-extrabold text-primary-blue mb-4 leading-tight">
                Downloads
            </h1>
            <p class="text-lg md:text-xl text-secondary-gray max-w-2xl mx-auto">
                Important documents and forms available for download.
            </p>
        </header>

        <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-lg border border-gray-200">
            <ul class="space-y-4">

                <?php if (!empty($download_files)): ?>
                    <?php foreach ($download_files as $file_name): ?>
                        <li class="border-b last:border-b-0 pb-4 last:pb-0">
                            <a href="<?php echo htmlspecialchars(rawurlencode($file_name)); // Use rawurlencode for filenames with spaces or Sinhala characters ?>"
                               download
                               class="group flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 transition duration-150">

                                <div class="flex items-center min-w-0"> <svg class="w-6 h-6 text-indigo-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span class="text-base font-medium text-gray-800 group-hover:text-indigo-700 truncate">
                                        <?php 
                                            // Make name cleaner (remove .pdf, replace underscores/etc)
                                            $display_name = pathinfo($file_name, PATHINFO_FILENAME);
                                            echo htmlspecialchars(str_replace(['_', '-'], ' ', $display_name));
                                        ?>
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">(<?php echo strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); ?>)</span>
                                </div>

                                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-center text-gray-500 p-4">
                        No downloadable files found in this section.
                    </li>
                <?php endif; ?>

            </ul>
        </div>

    </div>
</body>

<?php
// 3. Include the public footer
// Check if connection was opened in header and close it
if (isset($con)) {
    $con->close();
}
include_once('../include/footer.php');
?>