
<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$question_id = $_GET['id'];

// Increment view count
$view_sql = "UPDATE questions SET views_count = views_count + 1 WHERE id = ?";
$view_stmt = $conn->prepare($view_sql);
$view_stmt->bind_param("i", $question_id);
$view_stmt->execute();

// Get question details
$q_sql = "SELECT q.*, u.name as author_name 
          FROM questions q 
          INNER JOIN users u ON q.user_id = u.id 
          WHERE q.id = ?";
$q_stmt = $conn->prepare($q_sql);
$q_stmt->bind_param("i", $question_id);
$q_stmt->execute();
$q_result = $q_stmt->get_result();

if ($q_result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$question = $q_result->fetch_assoc();

// Get tags for this question
$tag_sql = "SELECT t.name FROM tags t 
            INNER JOIN question_tags qt ON t.id = qt.tag_id 
            WHERE qt.question_id = ?";
$tag_stmt = $conn->prepare($tag_sql);
$tag_stmt->bind_param("i", $question_id);
$tag_stmt->execute();
$tag_result = $tag_stmt->get_result();
$tags = [];

if ($tag_result->num_rows > 0) {
    while($tag_row = $tag_result->fetch_assoc()) {
        $tags[] = $tag_row['name'];
    }
}

// Get answers
$a_sql = "SELECT a.*, u.name as author_name 
          FROM answers a 
          INNER JOIN users u ON a.user_id = u.id 
          WHERE a.question_id = ? 
          ORDER BY a.is_correct DESC, a.upvotes_count DESC, a.created_at ASC";
$a_stmt = $conn->prepare($a_sql);
$a_stmt->bind_param("i", $question_id);
$a_stmt->execute();
$a_result = $a_stmt->get_result();
$answers = [];

if ($a_result->num_rows > 0) {
    while($a_row = $a_result->fetch_assoc()) {
        $answers[] = $a_row;
    }
}

// Process answer submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer_content'])) {
    if (!isLoggedIn()) {
        $error = "You must be signed in to post an answer";
    } else {
        $content = $_POST['answer_content'];
        
        if (empty($content)) {
            $error = "Answer cannot be empty";
        } else {
            $insert_sql = "INSERT INTO answers (content, user_id, question_id) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sii", $content, $_SESSION['user_id'], $question_id);
            
            if ($insert_stmt->execute()) {
                $success = "Your answer has been posted successfully!";
                
                // Refresh page to show the new answer
                header("Location: question.php?id=$question_id&success=1");
                exit();
            } else {
                $error = "Error posting answer: " . $conn->error;
            }
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Your answer has been posted successfully!";
}

include 'header.php';
?>

<div class="max-w-4xl mx-auto mt-8">
    <!-- Question -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex items-center space-x-2 mb-2">
            <span class="bg-blue-50 text-blue-500 text-xs font-medium px-2 py-1 rounded-full border border-blue-100">
                <?php echo htmlspecialchars($question['subject']); ?>
            </span>
            <span class="text-xs text-gray-400">
                <?php echo date('M j, Y', strtotime($question['created_at'])); ?>
            </span>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            <?php echo htmlspecialchars($question['title']); ?>
        </h1>
        
        <div class="prose max-w-none mb-6">
            <?php echo nl2br(htmlspecialchars($question['content'])); ?>
        </div>
        
        <?php if (!empty($tags)): ?>
            <div class="flex flex-wrap gap-1 mb-4">
                <?php foreach ($tags as $tag): ?>
                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                        <?php echo htmlspecialchars($tag); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="flex items-center justify-between border-t pt-4 mt-4">
            <div class="flex items-center space-x-2">
                <div class="bg-gray-200 text-gray-700 h-8 w-8 rounded-full flex items-center justify-center text-xs">
                    <?php 
                        $initials = '';
                        $name_parts = explode(' ', $question['author_name']);
                        foreach ($name_parts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo substr($initials, 0, 2);
                    ?>
                </div>
                <span class="text-sm text-gray-600">
                    <?php echo htmlspecialchars($question['author_name']); ?>
                </span>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-1 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="text-sm"><?php echo $question['views_count']; ?> views</span>
                </div>
                <button class="flex items-center space-x-1 text-gray-500 hover:text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <span class="text-sm"><?php echo $question['upvotes_count']; ?></span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Answers -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">
            <?php echo count($answers); ?> Answer<?php echo count($answers) != 1 ? 's' : ''; ?>
        </h2>
        
        <?php if (empty($answers)): ?>
            <div class="bg-gray-50 p-6 rounded-lg text-center">
                <p class="text-gray-500">No answers yet. Be the first to answer this question!</p>
            </div>
        <?php else: ?>
            <?php foreach ($answers as $answer): ?>
                <div class="bg-white p-6 rounded-lg shadow-md mb-4 <?php echo $answer['is_correct'] ? 'border-2 border-green-400' : ''; ?>">
                    <?php if ($answer['is_correct']): ?>
                        <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium inline-block mb-3">
                            Best Answer
                        </div>
                    <?php endif; ?>
                    
                    <div class="prose max-w-none mb-4">
                        <?php echo nl2br(htmlspecialchars($answer['content'])); ?>
                    </div>
                    
                    <div class="flex items-center justify-between border-t pt-4 mt-4">
                        <div class="flex items-center space-x-2">
                            <div class="bg-gray-200 text-gray-700 h-8 w-8 rounded-full flex items-center justify-center text-xs">
                                <?php 
                                    $initials = '';
                                    $name_parts = explode(' ', $answer['author_name']);
                                    foreach ($name_parts as $part) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                    }
                                    echo substr($initials, 0, 2);
                                ?>
                            </div>
                            <span class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($answer['author_name']); ?>
                            </span>
                            <span class="text-xs text-gray-400">
                                answered <?php echo date('M j, Y', strtotime($answer['created_at'])); ?>
                            </span>
                        </div>
                        
                        <button class="flex items-center space-x-1 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                            <span class="text-sm"><?php echo $answer['upvotes_count']; ?></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Post an answer -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Your Answer</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isLoggedIn()): ?>
            <form method="POST" action="question.php?id=<?php echo $question_id; ?>">
                <div class="mb-4">
                    <textarea 
                        id="answer_content" 
                        name="answer_content" 
                        rows="6" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Write your answer here..."
                        required
                    ></textarea>
                </div>
                
                <button 
                    type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors"
                >
                    Post Your Answer
                </button>
            </form>
        <?php else: ?>
            <div class="bg-gray-50 p-6 rounded-lg text-center">
                <p class="text-gray-600 mb-4">You need to sign in to post an answer.</p>
                <a 
                    href="signin.php" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors inline-block"
                >
                    Sign In
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
