
<?php
require_once 'config.php';

// Require login to view notes
requireLogin();

// Get user's notes
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC"; // Changed from updated_at to created_at
$stmt = $conn->prepare($sql);
$result = null; // Initialize $result variable

// Check if prepare was successful
if ($stmt === false) {
    $error_message = "Error preparing statement: " . $conn->error;
} else {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Handle note deletion
    $success_message = '';
    $error_message = '';

    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $note_id = $_GET['delete'];
        
        // Verify note belongs to user
        $check_sql = "SELECT id FROM notes WHERE id = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            $error_message = "Error preparing statement: " . $conn->error;
        } else {
            $check_stmt->bind_param("ii", $note_id, $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Delete the note
                $delete_sql = "DELETE FROM notes WHERE id = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                
                if ($delete_stmt === false) {
                    $error_message = "Error preparing delete statement: " . $conn->error;
                } else {
                    $delete_stmt->bind_param("i", $note_id);
                    
                    if ($delete_stmt->execute()) {
                        $success_message = "Note deleted successfully!";
                        // Refresh the notes list
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $error_message = "Error deleting note: " . $conn->error;
                    }
                }
            } else {
                $error_message = "You don't have permission to delete this note.";
            }
        }
    }
}

include 'header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">My Notes</h1>
        <a href="new_note.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            + Add New Note
        </a>
    </div>
    
    <?php if (!empty($success_message)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($result) && $result->num_rows == 0): ?>
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">No Notes Yet</h2>
            <p class="text-gray-500 mb-4">You haven't created any notes yet. Get started by adding your first note!</p>
            <a href="new_note.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Create Your First Note
            </a>
        </div>
    <?php elseif (isset($result)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php while ($note = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($note['title']); ?></h3>
                            <div class="flex space-x-2">
                                <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <a href="notes.php?delete=<?php echo $note['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this note?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="text-gray-600 mb-3 whitespace-pre-line max-h-24 overflow-hidden">
                            <?php echo htmlspecialchars(substr($note['content'], 0, 150)) . (strlen($note['content']) > 150 ? '...' : ''); ?>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Created: <?php echo date('M j, Y', strtotime($note['created_at'])); ?></span>
                            <?php if (isset($note['updated_at']) && $note['updated_at'] != $note['created_at']): ?>
                                <span>Updated: <?php echo date('M j, Y', strtotime($note['updated_at'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
