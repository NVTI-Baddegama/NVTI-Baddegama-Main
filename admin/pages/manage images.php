<?php
include_once('../include/header.php');
include('../../include/connection.php');

// Fetch all images from the database
$sql = "SELECT id, image_name, image_path, created_at FROM gallery ORDER BY created_at DESC";
$result = $con->query($sql);

if (!$result) {
    die("Could not fetch images: " . $con->error);
}

// Use fetch_all(MYSQLI_ASSOC) instead of fetchAll()
$images = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    
    <style>
        /* Simple transition for modals */
        .modal {
            transition: opacity 0.25s ease;
        }
    </style>
</head>
<body class="text-gray-900 antialiased">

<div class="container mx-auto p-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Image Gallery</h1>
        <button id="openUploadModalBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
            Upload Image
        </button>
    </div>

    <div class="rounded-lg shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-300">
                <tr>
                    <th class="p-4 text-left text-sm font-semibold uppercase">Image Name</th>
                    <th class="p-4 text-left text-sm font-semibold uppercase">Created At</th>
                    <th class="p-4 text-left text-sm font-semibold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php if (empty($images)): ?>
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-400">No images found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($images as $image): ?>
                        <tr class="hover:bg-gray-750">
                            <td class="p-4"><?php echo htmlspecialchars($image['image_name']); ?></td>
                            <td class="p-4 text-gray-400"><?php echo date('F j, Y, g:i a', strtotime($image['created_at'])); ?></td>
                            <td class="p-4">
                                <button class="preview-btn bg-green-500 hover:bg-green-600 text-white text-sm py-1 px-3 rounded"
                                        data-image-src="<?php echo htmlspecialchars($image['image_path']); ?>"
                                        data-image-name="<?php echo htmlspecialchars($image['image_name']); ?>">
                                    Preview
                                </button>

                               <a href="../lib/delete_image.php?id=<?php echo $image['id']; ?>"
                                           class="bg-red-500 hover:bg-red-600 text-white text-sm py-1 px-3 rounded"
                                           onclick="return confirm('Are you sure you want to delete this image? This action cannot be undone.');">
                                            Delete
                                        </a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<div id="uploadModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Upload New Image</h3>
            <button id="closeUploadModalBtn" class="text-gray-400 hover:text-white text-3xl">&times;</button>
        </div>
        <form action="../lib/upload_image.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="image_name" class="block text-sm font-medium mb-2">Image Name</label>
                <input type="text" name="image_name" id="image_name" class="w-full p-2 bg-gray-100 border border-gray-600 rounded" >
            </div>
            <div class="mb-6">
                <label for="image_file" class="block text-sm font-medium mb-2">Upload Image</label>
                <input type="file" name="image_file" id="image_file" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600" required>
            </div>
            <div class="text-right">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<div id="previewModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">
    <div class="bg-white rounded-lg shadow-xl p-4 max-w-3xl w-auto">
        <div class="flex justify-end">
            <button id="closePreviewModalBtn" class="text-gray-400 hover:text-white text-3xl">&times;</button>
        </div>
        <div class="mt-2">
            <img id="previewImage" src="" alt="Image preview" class="rounded-2xl max-w-full max-h-[80vh] object-contain">
            <p id="previewCaption" class="text-center text-gray-300 mt-2"></p>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // --- Upload Modal Logic ---
    const uploadModal = document.getElementById('uploadModal');
    const openUploadBtn = document.getElementById('openUploadModalBtn');
    const closeUploadBtn = document.getElementById('closeUploadModalBtn');
    
    openUploadBtn.addEventListener('click', () => uploadModal.classList.remove('hidden'));
    closeUploadBtn.addEventListener('click', () => uploadModal.classList.add('hidden'));
    
    // --- Preview Modal Logic ---
    const previewModal = document.getElementById('previewModal');
    const closePreviewBtn = document.getElementById('closePreviewModalBtn');
    const previewImage = document.getElementById('previewImage');
    const previewCaption = document.getElementById('previewCaption');
    
    // Use event delegation for all preview buttons
    document.querySelector('.w-full').addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('preview-btn')) {
            // Get data from the button
            const imgSrc = e.target.dataset.imageSrc;
            const imgName = e.target.dataset.imageName;
            
            // Set modal content
            previewImage.src = imgSrc;
            previewCaption.textContent = imgName;
            
            // Show the modal
            previewModal.classList.remove('hidden');
        }
    });
    
    closePreviewBtn.addEventListener('click', () => previewModal.classList.add('hidden'));

    // Close modals by clicking on the backdrop
    [uploadModal, previewModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

});
</script>

</body>
</html>