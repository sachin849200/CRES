<?php include 'includes/db_connect.php'; include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRES - Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head><body>
<div class="container">
    <h1>Admin Login</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Admin Email" value="admin@cres.com" readonly>
        <input type="password" name="password" placeholder="Admin Password" required>
        <button type="submit" name="login">Login as Admin</button>
    </form>
    <p><a href="index.php">← Student Login</a></p>

    <?php
    if (isset($_POST['login'])) {
        if ($_POST['email'] === 'admin@cres.com' && password_verify($_POST['password'], $conn->query("SELECT password FROM Student WHERE email='admin@cres.com'")->fetch_assoc()['password'] ?? '')) {
            $id = $conn->query("SELECT voter_id FROM Student WHERE email='admin@cres.com'")->fetch_assoc()['voter_id'];
            $_SESSION['user_id'] = $id;
            $_SESSION['is_admin'] = true;
            redirect('admin_dashboard.php');
        } else {
            echo '<div class="error">Invalid admin credentials</div>';
        }
    }
    ?>
</div>
</body></html>