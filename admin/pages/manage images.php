<?php
include_once('../include/header.php');
include('../../include/connection.php');

// --- Logic for TOAST messages ---
$toast_message = '';
$toast_type = ''; // 'success' or 'error'

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'uploaded') {
        $toast_message = 'Image uploaded successfully.';
        $toast_type = 'success';
    }
    if ($_GET['status'] == 'deleted') {
        $toast_message = 'Image deleted successfully.';
        $toast_type = 'success';
    }
    if ($_GET['status'] == 'upload_error') {
        $toast_message = 'There was a problem uploading your file.';
        $toast_type = 'error';
    }
    if ($_GET['status'] == 'delete_error') {
        $toast_message = 'There was a problem deleting the image.';
        $toast_type = 'error';
    }
}
// --- END NEW ---

// Fetch all images from the database
$sql = "SELECT id, image_name, image_path, created_at FROM gallery ORDER BY created_at DESC";
$result = $con->query($sql);

if (!$result) {
    die("Could not fetch images: " . $con->error);
}

$images = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery</title>
    
    <style>
        /* Simple transition for modals and toast */
        .modal, .toast {
            transition: opacity 0.25s ease;
        }
        .toast {
            transition: opacity 0.5s, transform 0.5s ease;
        }
        /* State for toast to be hidden (faded out and moved down) */
        .toast.hidden {
            opacity: 0;
            transform: translateY(20px);
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
            <tbody id="image-table-body" class="divide-y divide-gray-700">
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
                                
                                <button class="delete-btn bg-red-500 hover:bg-red-600 text-white text-sm py-1 px-3 rounded"
                                        data-href="../lib/delete_image.php?id=<?php echo $image['id']; ?>">
                                    Delete
                                </button>
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
                <input type="text" name="image_name" id="image_name" class="w-full p-2 bg-gray-100 border border-gray-600 rounded" required>
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
            <button id="closePreviewModalBtn" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
        </div>
        <div class="mt-2">
            <img id="previewImage" src="" alt="Image preview" class="max-w-full max-h-[80vh] rounded object-contain">
            <p id="previewCaption" class="text-center text-gray-600 mt-2"></p>
        </div>
    </div>
</div>

<div id="deleteConfirmModal" class="modal fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
        <div class="flex items-start mb-4">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-4 text-left">
                <h3 class="text-lg font-medium text-gray-900">Delete Image</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-600">
                        Are you sure you want to delete this image? This action will permanently remove the file and cannot be undone.
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 -m-6 mt-6 sm:flex sm:flex-row-reverse rounded-b-lg">
            <a id="confirmDeleteLink" href="#" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                Confirm Delete
            </a>
            <button id="cancelDeleteBtn" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<div id="toast" class="toast fixed bottom-5 right-5 w-full max-w-xs p-4 rounded-lg shadow-lg text-white hidden">
    <p id="toastMessage"></p>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- Toast Logic ---
    const toast = document.getElementById('toast');
    const toastMessageEl = document.getElementById('toastMessage');

    function showToast(message, type = 'success') {
        if (!toast || !toastMessageEl) return; // Add check
        
        toastMessageEl.textContent = message;
        if (type === 'success') {
            toast.classList.add('bg-green-500');
            toast.classList.remove('bg-red-500');
        } else {
            toast.classList.add('bg-red-500');
            toast.classList.remove('bg-green-500');
        }
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }

    const toastMessageFromPHP = "<?php echo $toast_message; ?>";
    const toastTypeFromPHP = "<?php echo $toast_type; ?>";

    if (toastMessageFromPHP) {
        showToast(toastMessageFromPHP, toastTypeFromPHP);
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // --- Modal Elements ---
    const uploadModal = document.getElementById('uploadModal');
    const previewModal = document.getElementById('previewModal');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');

    // --- Upload Modal ---
    const openUploadBtn = document.getElementById('openUploadModalBtn');
    const closeUploadBtn = document.getElementById('closeUploadModalBtn');
    if (openUploadBtn) {
        openUploadBtn.addEventListener('click', () => uploadModal.classList.remove('hidden'));
    }
    if (closeUploadBtn) {
        closeUploadBtn.addEventListener('click', () => uploadModal.classList.add('hidden'));
    }

    // --- Preview Modal ---
    const closePreviewBtn = document.getElementById('closePreviewModalBtn');
    const previewImage = document.getElementById('previewImage');
    const previewCaption = document.getElementById('previewCaption');
    if (closePreviewBtn) {
        closePreviewBtn.addEventListener('click', () => previewModal.classList.add('hidden'));
    }

    // --- Delete Modal ---
    const confirmDeleteLink = document.getElementById('confirmDeleteLink');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            deleteConfirmModal.classList.add('hidden');
        });
    }

    // --- NEW: CONSOLIDATED TABLE EVENT LISTENER ---
    const imageTableBody = document.getElementById('image-table-body');
    if (imageTableBody) {
        imageTableBody.addEventListener('click', (e) => {
            
            // Check if a PREVIEW button was clicked
            const previewBtn = e.target.closest('.preview-btn');
            if (previewBtn) {
                if (previewImage && previewCaption && previewModal) {
                    previewImage.src = previewBtn.dataset.imageSrc;
                    previewCaption.textContent = previewBtn.dataset.imageName;
                    previewModal.classList.remove('hidden');
                }
                return; // Action handled
            }

            // Check if a DELETE button was clicked
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.preventDefault(); // Stop default button action
                if (confirmDeleteLink && deleteConfirmModal) {
                    confirmDeleteLink.href = deleteBtn.dataset.href;
                    deleteConfirmModal.classList.remove('hidden');
                }
                return; // Action handled
            }
        });
    }
    // --- END NEW LOGIC ---


    // --- General Modal Backdrop Close ---
    [uploadModal, previewModal, deleteConfirmModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', (e) => {
                // Check if the click is on the backdrop itself
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    });
});
</script>
<?php include_once('../include/footer.php'); ?>

</body>
</html>