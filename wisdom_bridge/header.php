
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisdom Bridge - Knowledge Sharing Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .glass {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .user-dropdown {
            position: relative;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            width: 12rem;
            background-color: white;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }
        .dropdown-menu.open {
            display: block;
        }
    </style>
</head>
<body>
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="index.php" class="flex items-center space-x-2">
                    <span class="text-xl font-bold text-blue-500">Wisdom Bridge</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-600 hover:text-blue-500 text-sm font-medium">Home</a>
                    <a href="questions.php" class="text-gray-600 hover:text-blue-500 text-sm font-medium">Q&A</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="notes.php" class="text-gray-600 hover:text-blue-500 text-sm font-medium">My Notes</a>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="ask.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Ask Question
                        </a>
                        <div class="user-dropdown">
                            <button class="dropdown-toggle flex items-center space-x-1 text-gray-700 hover:text-blue-500 cursor-pointer">
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu py-1">
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="notes.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Notes</a>
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="signin.php" class="text-gray-600 hover:text-blue-500 text-sm font-medium">Sign In</a>
                        <a href="signup.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all dropdown toggles
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            
            // Add click handler to each toggle
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent click from immediately closing dropdown
                    
                    // Get the dropdown menu associated with this toggle
                    const dropdown = this.nextElementSibling;
                    
                    // Toggle the "open" class on the dropdown
                    dropdown.classList.toggle('open');
                });
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                // If click wasn't inside a dropdown menu
                if (!e.target.closest('.dropdown-menu')) {
                    // Close all dropdown menus
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('open');
                    });
                }
            });
        });
    </script>
