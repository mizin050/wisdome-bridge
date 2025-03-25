
<?php
// Database connection configuration
$servername = "localhost";
$username = "root"; // Change to your MySQL username
$password = ""; // Change to your MySQL password

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS wisdom_bridge";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("wisdom_bridge");

// Read SQL from db_setup.sql file
$sql = file_get_contents('db_setup.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database tables created successfully<br>";
    
    // Consume results to allow further queries
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    // Create demo content
    if ($conn->error) {
        echo "Error: " . $conn->error . "<br>";
    } else {
        // Create demo user
        $name = "Demo User";
        $email = "demo@example.com";
        $password = password_hash("password123", PASSWORD_DEFAULT);
        
        $user_sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("sss", $name, $email, $password);
        
        if ($user_stmt->execute()) {
            $user_id = $conn->insert_id;
            echo "Demo user created successfully<br>";
            
            // Create demo questions
            $subjects = ["Mathematics", "Physics", "Chemistry", "Biology", "Computer Science"];
            
            for ($i = 1; $i <= 5; $i++) {
                $title = "Sample Question $i";
                $content = "This is a sample question content for demonstration purposes. It contains details about the problem that needs to be solved.";
                $subject = $subjects[$i - 1];
                
                $q_sql = "INSERT INTO questions (title, content, user_id, subject) VALUES (?, ?, ?, ?)";
                $q_stmt = $conn->prepare($q_sql);
                $q_stmt->bind_param("ssis", $title, $content, $user_id, $subject);
                
                if ($q_stmt->execute()) {
                    $question_id = $conn->insert_id;
                    
                    // Create demo tags
                    $tags = ["homework", "example", "help"];
                    
                    foreach ($tags as $tag_name) {
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
                    
                    // Create demo answer
                    $answer_content = "This is a sample answer to the question. It provides information on how to solve the problem.";
                    
                    $a_sql = "INSERT INTO answers (content, user_id, question_id) VALUES (?, ?, ?)";
                    $a_stmt = $conn->prepare($a_sql);
                    $a_stmt->bind_param("sii", $answer_content, $user_id, $question_id);
                    $a_stmt->execute();
                }
            }
            
            echo "Demo questions and answers created successfully<br>";
        } else {
            echo "Error creating demo user: " . $user_stmt->error . "<br>";
        }
    }
} else {
    echo "Error creating tables: " . $conn->error . "<br>";
}

echo "<br>Installation completed. <a href='index.php'>Go to homepage</a>";

$conn->close();
?>
