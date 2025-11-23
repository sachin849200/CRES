<?php
// logout.php
session_start();
session_destroy();   // Clear all session data

// Optional: Show a nice message before redirect
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Logged Out Successfully!</h1>
    <p>Thank you for using CRES</p>
    <div class="success">
        You have been securely logged out.
    </div>
    <br>
    <a href="index.php"><button>Back to Student Login</button></a>
    <br><br>
    <a href="admin_login.php">Admin Login →</a>
</div>

<!-- Auto redirect after 3 seconds (optional but cool) -->
<script>
    setTimeout(function(){
        window.location.href = "index.php";
    }, 8000);
</script>
</body>
</html>