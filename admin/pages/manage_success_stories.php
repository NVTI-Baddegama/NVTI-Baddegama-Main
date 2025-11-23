<?php
// Admin Header එක include කිරීම
include_once('../include/header.php');

// Session එකෙන් එන පණිවිඩ (messages) පෙන්වීම
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// දත්ත ගබඩාවෙන් සියලුම Stories ලබා ගැනීම
$stories = [];
if (isset($con)) {
    // Approved, Pending සියල්ලම මෙහිදී ලබා ගනී
    $sql = "SELECT id, name, position, company_name, is_active FROM success_stories ORDER BY created_at DESC";
    $result = $con->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stories[] = $row;
        }
    }
}
?>

<?php if ($message): ?>
    <div class="container mx-auto max-w-6xl mt-4">
        <div class="<?php echo ($message_type == 'success') ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative"
            role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
        </div>
    </div>
<?php endif; ?>

<div class="mb-4 flex justify-end">
    <a href="../../pages/submit_story.php"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow transition duration-150">
        + Add New Success Story
    </a>
</div>

<div class="container mx-auto max-w-6xl p-6 bg-white shadow-md rounded-lg mt-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Success Stories</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th
                        class="px-6 py-3 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Name</th>
                    <th
                        class="px-6 py-3 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Position & Company</th>
                    <th
                        class="px-6 py-3 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status</th>
                    <th
                        class="px-6 py-3 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (!empty($stories)): ?>
                    <?php foreach ($stories as $story): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($story['name']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-800"><?php echo htmlspecialchars($story['position']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($story['company_name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if ($story['is_active'] == 1): ?>
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <?php
                                // 'Approve' හෝ 'Hide' ලෙස බොත්තම වෙනස් කිරීම
                                $toggle_text = ($story['is_active'] == 1) ? 'Hide' : 'Approve';
                                $toggle_class = ($story['is_active'] == 1) ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600';
                                ?>
                                <a href="../lib/story_action_handler.php?action=toggle&id=<?php echo $story['id']; ?>"
                                    class="text-white px-3 py-1 rounded-md text-xs <?php echo $toggle_class; ?> transition duration-150">
                                    <?php echo $toggle_text; ?>
                                </a>

                                <a href="edit_success_story.php?id=<?php echo $story['id']; ?>"
                                    class="text-white px-3 py-1 rounded-md text-xs bg-blue-500 hover:bg-blue-600 transition duration-150">
                                    Edit
                                </a>
                                <a href="../lib/story_action_handler.php?action=delete&id=<?php echo $story['id']; ?>"
                                    class="text-white px-3 py-1 rounded-md text-xs bg-red-500 hover:bg-red-600 transition duration-150"
                                    onclick="return confirm('Are you sure you want to delete this story? This action cannot be undone.');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No success stories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Admin Footer එක include කිරීම
include_once('../include/footer.php');
?>