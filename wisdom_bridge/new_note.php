
<?php
require_once 'config.php';

// Require login to create notes
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = $_POST['content'];
    
    if (empty($title) || empty($content)) {
        $error = "Please fill in all required fields";
    } else {
        // Check if notes table exists and has correct structure
        $check_table_sql = "SHOW TABLES LIKE 'notes'";
        $table_result = $conn->query($check_table_sql);
        
        if ($table_result->num_rows == 0) {
            // Notes table doesn't exist, create it
            $create_table_sql = "CREATE TABLE notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )";
            
            if ($conn->query($create_table_sql) === FALSE) {
                $error = "Error creating notes table: " . $conn->error;
            }
        }
        
        // If no error, proceed with insert
        if (empty($error)) {
            // Insert note
            $sql = "INSERT INTO notes (title, content, user_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            // Check if prepare was successful
            if ($stmt === false) {
                $error = "Error preparing statement: " . $conn->error;
            } else {
                $stmt->bind_param("ssi", $title, $content, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = "Your note has been saved successfully!";
                } else {
                    $error = "Error saving note: " . $conn->error;
                }
            }
        }
    }
}

include 'header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6">Create New Note</h1>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
            <div class="mt-4">
                <a href="notes.php" class="text-green-700 font-medium hover:underline">View all notes</a>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" action="new_note.php">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Note title"
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
                    placeholder="Write your note here..."
                    required
                ></textarea>
            </div>
            
            <div class="flex space-x-4">
                <button 
                    type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors"
                >
                    Save Note
                </button>
                <a href="notes.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
