
<?php
require_once 'config.php';

// Require login to edit notes
requireLogin();

$error = '';
$success = '';
$note = null;

// Check if note ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: notes.php');
    exit();
}

$note_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify note belongs to user and get its data
$check_sql = "SELECT * FROM notes WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $note_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: notes.php');
    exit();
}

$note = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = $_POST['content'];
    
    if (empty($title) || empty($content)) {
        $error = "Please fill in all required fields";
    } else {
        // Update note
        $update_sql = "UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Your note has been updated successfully!";
            // Refresh note data
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $note = $result->fetch_assoc();
        } else {
            $error = "Error updating note: " . $conn->error;
        }
    }
}

include 'header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6">Edit Note</h1>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
            <div class="mt-4">
                <a href="notes.php" class="text-green-700 font-medium hover:underline">Back to all notes</a>
            </div>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="edit_note.php?id=<?php echo $note_id; ?>">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                value="<?php echo htmlspecialchars($note['title']); ?>"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            >
        </div>
        
        <div class="mb-6">
            <label for="content" class="block text-gray-700 font-medium mb-2">Content</label>
            <textarea 
                id="content" 
                name="content" 
                rows="12" 
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            ><?php echo htmlspecialchars($note['content']); ?></textarea>
        </div>
        
        <div class="flex space-x-4">
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors"
            >
                Update Note
            </button>
            <a href="notes.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
