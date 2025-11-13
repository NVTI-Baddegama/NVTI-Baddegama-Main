<?php 
// 1. පොදු Header එක (Public Header)
include_once('../include/header.php'); 

// 2. Session එකෙන් එන පණිවිඩ (messages) පෙන්වීම
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="container mx-auto max-w-2xl p-6 bg-white shadow-md rounded-lg mt-10 mb-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Submit Your Success Story</h1>
    <p class="text-center text-gray-600 mb-6">
        Share your journey with us. Your story will be reviewed by an administrator before it is published.
    </p>

    <?php if ($message): ?>
        <div class="mb-4">
            <div class="<?php echo ($message_type == 'success') ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
            </div>
        </div>
    <?php endif; ?>


    <form action="../lib/submit_story_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" id="name" name="name" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Position / Job Title</label>
            <input type="text" id="position" name="position"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
            <input type="text" id="company_name" name="company_name"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Your Story / Description</label>
            <textarea id="description" name="description" rows="4" required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div>
            <label for="story_image" class="block text-sm font-medium text-gray-700">Your Image (Optional)</label>
            <input type="file" id="story_image" name="story_image" accept="image/png, image/jpeg, image/jpg"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="text-xs text-gray-500 mt-1">If you add a YouTube video, this image might not be shown.</p>
        </div>

        <div>
            <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube URL (Optional)</label>
            <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..."
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="contact_details" class="block text-sm font-medium text-gray-700">Contact Details (Optional)</label>
            <input type="text" id="contact_details" name="contact_details" placeholder="Phone number or Email (Will not be published if sensitive)"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="flex justify-center">
            <button type="submit"
                    class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Submit for Review
            </button>
        </div>

    </form>
</div>

<?php 
// 5. පොදු Footer එක (Public Footer)
include_once('../include/footer.php'); 
?>