
<?php
require_once 'config.php';

// Get recent questions
$sql = "SELECT q.*, u.name as author_name, 
        (SELECT COUNT(*) FROM answers WHERE question_id = q.id) as answers_count 
        FROM questions q 
        INNER JOIN users u ON q.user_id = u.id 
        ORDER BY q.created_at DESC 
        LIMIT 10";
$result = $conn->query($sql);
$questions = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Get tags for this question
        $tag_sql = "SELECT t.name FROM tags t 
                    INNER JOIN question_tags qt ON t.id = qt.tag_id 
                    WHERE qt.question_id = " . $row['id'];
        $tag_result = $conn->query($tag_sql);
        $tags = [];
        
        if ($tag_result->num_rows > 0) {
            while($tag_row = $tag_result->fetch_assoc()) {
                $tags[] = $tag_row['name'];
            }
        }
        
        $row['tags'] = $tags;
        $questions[] = $row;
    }
}

include 'header.php';
?>

<div class="flex flex-col space-y-6 mt-4">
    <h1 class="text-3xl font-bold text-gray-800">Recent Questions</h1>
    
    <?php if (empty($questions)): ?>
        <div class="bg-white p-8 rounded-lg shadow text-center">
            <p class="text-gray-500">No questions found. Be the first to ask!</p>
            <?php if (isLoggedIn()): ?>
                <a href="ask.php" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Ask a Question
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($questions as $question): ?>
                <div class="w-full rounded-xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="bg-blue-50 text-blue-500 text-xs font-medium px-2 py-1 rounded-full border border-blue-100">
                            <?php echo htmlspecialchars($question['subject']); ?>
                        </span>
                        <span class="text-xs text-gray-400">
                            <?php echo date('M j, Y', strtotime($question['created_at'])); ?>
                        </span>
                    </div>
                    
                    <a href="question.php?id=<?php echo $question['id']; ?>">
                        <h3 class="font-semibold text-lg mb-2 text-gray-800 hover:text-blue-500 transition-colors">
                            <?php echo htmlspecialchars($question['title']); ?>
                        </h3>
                    </a>
                    
                    <p class="text-sm text-gray-600 mb-4">
                        <?php 
                            $content = strip_tags($question['content']);
                            echo (strlen($content) > 150) ? substr($content, 0, 150) . '...' : $content; 
                        ?>
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="bg-gray-200 text-gray-700 h-7 w-7 rounded-full flex items-center justify-center text-xs">
                                <?php 
                                    $initials = '';
                                    $name_parts = explode(' ', $question['author_name']);
                                    foreach ($name_parts as $part) {
                                        $initials .= strtoupper(substr($part, 0, 1));
                                    }
                                    echo substr($initials, 0, 2);
                                ?>
                            </div>
                            <span class="text-xs text-gray-500">
                                <?php echo htmlspecialchars($question['author_name']); ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-1 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="text-xs"><?php echo $question['answers_count']; ?></span>
                            </div>
                            <div class="flex items-center space-x-1 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span class="text-xs"><?php echo $question['views_count']; ?></span>
                            </div>
                            <div class="flex items-center space-x-1 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span class="text-xs"><?php echo $question['upvotes_count']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($question['tags'])): ?>
                        <div class="flex flex-wrap gap-1 mt-3">
                            <?php foreach ($question['tags'] as $tag): ?>
                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                    <?php echo htmlspecialchars($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
