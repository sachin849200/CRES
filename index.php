<?php include 'includes/db_connect.php'; include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRES - Student Login</title>
    <link rel="stylesheet" href="css/style.css">
</head><body>
<div class="container">
    <h1>Student Login</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Student Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login as Student</button>
    </form>
    <p><a href="admin_login.php">Admin Login →</a></p>

    <?php
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $stmt = $conn->prepare("SELECT voter_id, password FROM Student WHERE email = ? AND email != 'admin@cres.com'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $hash);
        if ($stmt->fetch() && password_verify($_POST['password'], $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['is_admin'] = false;
            redirect('student_dashboard.php');
        } else {
            echo '<div class="error">Invalid student credentials or use student login only</div>';
        }
        $stmt->close();
    }
    ?>
</div>
</body></html>