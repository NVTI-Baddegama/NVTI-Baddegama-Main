<?php
// Ensure header is included first and session started
include_once('../include/header.php'); 
include_once('../../include/connection.php');

// Fetch the current settings from the database
$settings_query = "SELECT * FROM mail_settings WHERE id = 1 LIMIT 1";
$settings_result = $con->query($settings_query);
$settings = $settings_result->fetch_assoc();

$con->close();
?>

<h2 class="text-3xl font-bold text-gray-800 mb-6">Mail Settings (Student Notifications)</h2>

<?php
if (isset($_SESSION['mail_success_msg'])) {
    echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert"><strong class="font-bold">Success!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['mail_success_msg']) . '</span></div>';
    unset($_SESSION['mail_success_msg']);
}
if (isset($_SESSION['mail_error_msg'])) {
    echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' . htmlspecialchars($_SESSION['mail_error_msg']) . '</span></div>';
    unset($_SESSION['mail_error_msg']);
}
?>

<div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-500 w-full lg:w-2/3 mx-auto">
    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Update Notification Recipients</h3>
    <p class="text-sm text-gray-600 mb-6">
        When a new student registers, an email (with PDF) will be sent to the addresses listed below.
        You can list multiple emails in each box, separated by a comma (e.g., `email1@gmail.com, email2@gmail.com`).
    </p>

    <form id="mailSettingsForm" action="../lib/mail_handler.php" method="POST" class="mt-6">
        <input type="hidden" name="action" value="update_settings">
        
        <div class="space-y-6">

            <div>
                <label for="send_mail" class="block text-sm font-medium text-gray-700 mb-1">Main Recipient(s) (To:)</label>
                <input type="text" id="send_mail" name="send_mail"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       value="<?php echo htmlspecialchars($settings['send_mail'] ?? ''); ?>"
                       placeholder="admin@nvti.lk, info@nvti.lk">
                <p class="text-xs text-gray-500 mt-1">The primary email(s) to receive the notification.</p>
            </div>

            <div>
                <label for="cc_mail" class="block text-sm font-medium text-gray-700 mb-1">CC Recipient(s) (Optional)</label>
                <input type="text" id="cc_mail" name="cc_mail"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       value="<?php echo htmlspecialchars($settings['cc_mail'] ?? ''); ?>"
                       placeholder="manager@nvti.lk">
                <p class="text-xs text-gray-500 mt-1">Carbon Copy. These recipients will be visible to all.</p>
            </div>
            
        

        </div>

        <div class="mt-8 text-right">
            <button type="submit" name="save_settings"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition duration-150 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Settings
            </button>
        </div>
    </form>
</div>

<?php
include_once('../include/footer.php');
?>