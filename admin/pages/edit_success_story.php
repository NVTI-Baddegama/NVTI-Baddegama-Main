<?php 
// Admin Header එක include කිරීම
include_once('../include/header.php');

// 1. URL එකෙන් 'id' එක ලබා ගැනීම
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // ID එකක් නැත්නම්, Manage පිටුවට යොමු කිරීම
    header("Location: manage_success_stories.php");
    exit();
}

$story_id = (int)$_GET['id'];

// 2. දත්ත ගබඩාවෙන් අදාළ Story එකේ දත්ත ලබා ගැනීම
include_once(__DIR__ . '/../../include/connection.php');
$stmt = $con->prepare("SELECT * FROM success_stories WHERE id = ?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // අදාළ ID එකට දත්ත නොමැති නම්
    $_SESSION['message'] = "Story not found.";
    $_SESSION['message_type'] = "error";
    header("Location: manage_success_stories.php");
    exit();
}
$story = $result->fetch_assoc();
$stmt->close();
?>

<div class="container mx-auto max-w-4xl p-6 bg-white shadow-md rounded-lg mt-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Success Story</h1>

    <form action="../lib/update_success_story_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
        <input type="hidden" name="existing_image_path" value="<?php echo htmlspecialchars($story['image_path']); ?>">

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" id="name" name="name" required
                   value="<?php echo htmlspecialchars($story['name']); ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Position / Job Title</label>
            <input type="text" id="position" name="position"
                   value="<?php echo htmlspecialchars($story['position']); ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
            <input type="text" id="company_name" name="company_name"
                   value="<?php echo htmlspecialchars($story['company_name']); ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo htmlspecialchars($story['description']); ?></textarea>
        </div>

        <div>
            <label for="story_image" class="block text-sm font-medium text-gray-700">Update Student Image (Optional)</label>
            
            <?php if (!empty($story['image_path'])): ?>
                <img src="../../<?php echo htmlspecialchars($story['image_path']); ?>" alt="Current Image" class="w-32 h-32 object-cover rounded-md my-2">
            <?php endif; ?>
            
            <input type="file" id="story_image" name="story_image" accept="image/png, image/jpeg, image/jpg"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current image. Uploading a new image will replace the old one.</p>
        </div>

        <div>
            <label for="youtube_url" class="block text-sm font-medium text-gray-700">YouTube URL (Optional)</label>
            <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=..."
                   value="<?php echo htmlspecialchars($story['youtube_url']); ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="contact_details" class="block text-sm font-medium text-gray-700">Contact Details (Optional)</label>
            <input type="text" id="contact_details" name="contact_details" placeholder="Phone number or Email"
                   value="<?php echo htmlspecialchars($story['contact_details']); ?>"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Story
            </button>
        </div>

    </form>
</div>

<?php 
// Admin Footer එක include කිරීම
include_once('../include/footer.php');
?>