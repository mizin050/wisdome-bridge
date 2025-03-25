
<?php
require_once 'config.php';

// Clear all session variables
$_SESSION = array();

// If a session cookie is used, delete it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Display success message and redirect to home page
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to the new homepage
header("Location: index.php");
exit();
?>
