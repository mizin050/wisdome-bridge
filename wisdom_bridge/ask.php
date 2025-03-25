
<?php
require_once 'config.php';

// Require login to ask a question
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = $_POST['content'];
    $subject = sanitizeInput($_POST['subject']);
    $tags = sanitizeInput($_POST['tags']);
    
    if (empty($title) || empty($content) || empty($subject)) {
        $error = "Please fill in all required fields";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert question
            $q_sql = "INSERT INTO questions (title, content, user_id, subject) VALUES (?, ?, ?, ?)";
            $q_stmt = $conn->prepare($q_sql);
            $q_stmt->bind_param("ssis", $title, $content, $_SESSION['user_id'], $subject);
            $q_stmt->execute();
            
            $question_id = $conn->insert_id;
            
            // Process tags if provided
            if (!empty($tags)) {
                $tag_list = array_map('trim', explode(',', $tags));
                
                foreach ($tag_list as $tag_name) {
                    if (empty($tag_name)) continue;
                    
                    // Check if tag exists
                    $tag_check_sql = "SELECT id FROM tags WHERE name = ?";
                    $tag_check_stmt = $conn->prepare($tag_check_sql);
                    $tag_check_stmt->bind_param("s", $tag_name);
                    $tag_check_stmt->execute();
                    $tag_result = $tag_check_stmt->get_result();
                    
                    if ($tag_result->num_rows > 0) {
                        // Tag exists, get its ID
                        $tag_row = $tag_result->fetch_assoc();
                        $tag_id = $tag_row['id'];
                    } else {
                        // Create new tag
                        $tag_insert_sql = "INSERT INTO tags (name) VALUES (?)";
                        $tag_insert_stmt = $conn->prepare($tag_insert_sql);
                        $tag_insert_stmt->bind_param("s", $tag_name);
                        $tag_insert_stmt->execute();
                        $tag_id = $conn->insert_id;
                    }
                    
                    // Link tag to question
                    $qt_sql = "INSERT INTO question_tags (question_id, tag_id) VALUES (?, ?)";
                    $qt_stmt = $conn->prepare($qt_sql);
                    $qt_stmt->bind_param("ii", $question_id, $tag_id);
                    $qt_stmt->execute();
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            // Success
            $success = "Your question has been posted successfully!";
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $error = "Error posting question: " . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h1 class="text-2xl font-bold mb-6">Ask a Question</h1>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
            <div class="mt-4">
                <a href="index.php" class="text-green-700 font-medium hover:underline">Back to home</a>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" action="ask.php">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-medium mb-2">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., How do I solve this engineering problem?"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Be specific and imagine you're asking a question to a person</p>
            </div>
            
            <div class="mb-4">
                <label for="content" class="block text-gray-700 font-medium mb-2">Details</label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="8" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Include all the information someone would need to answer your question"
                    required
                ></textarea>
            </div>
            
            <div class="mb-4">
                <label for="subject" class="block text-gray-700 font-medium mb-2">Subject</label>
                <select 
                    id="subject" 
                    name="subject" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
                    <option value="" disabled selected>Select a subject</option>
                    <option value="Civil Engineering">Civil Engineering</option>
                    <option value="Mechanical Engineering">Mechanical Engineering</option>
                    <option value="Electrical Engineering">Electrical Engineering</option>
                    <option value="Electronics Engineering">Electronics Engineering</option>
                    <option value="Biomedical Engineering">Biomedical Engineering</option>
                    <option value="Chemical Engineering">Chemical Engineering</option>
                    <option value="Environmental Engineering">Environmental Engineering</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Industrial Engineering">Industrial Engineering</option>
                    <option value="Aerospace Engineering">Aerospace Engineering</option>
                    <option value="Other Engineering">Other Engineering</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="tags" class="block text-gray-700 font-medium mb-2">Tags</label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., motors, circuits, design"
                >
                <p class="text-xs text-gray-500 mt-1">Add up to 5 tags separated by commas (e.g., motors, circuits, design)</p>
            </div>
            
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors"
            >
                Post Your Question
            </button>
        </form>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
