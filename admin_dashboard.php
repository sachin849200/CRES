<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
if (!isLoggedIn() || !isAdmin()) redirect('index.php');

$status_msg = "";

// FULL SYSTEM RESET (NUCLEAR OPTION) — FIXED & HIDDEN
if (isset($_POST['reset_everything'])) {
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("DELETE FROM Vote");
    $conn->query("DELETE FROM Candidate");
    $conn->query("DELETE FROM Election");
    $conn->query("UPDATE Student SET has_voted = 0");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    $conn->query("INSERT INTO Election (title, status) VALUES ('Fresh Election - Ready to Start', 'closed')");
    
    $status_msg = '<div class="success" style="font-size:20px;padding:25px;background:#c8e6c9;border-radius:12px;text-align:center;">
        <strong>ENTIRE SYSTEM RESET SUCCESSFULLY!</strong><br>
        All elections, votes, and candidates permanently deleted.<br>
        A fresh election has been created.
    </div>';
}

// CREATE NEW ELECTION
if (isset($_POST['new_election'])) {
    $title = $conn->real_escape_string($_POST['election_title']);
    $conn->query("INSERT INTO Election (title, status) VALUES ('$title', 'closed')");
    $status_msg = '<div class="success">New Election Created Successfully!</div>';
}

// GET CURRENT ELECTION
$current = $conn->query("SELECT * FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc();
if (!$current) {
    $conn->query("INSERT INTO Election (title, status) VALUES ('Class Representative Election', 'closed')");
    $current = $conn->query("SELECT * FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc();
}
$eid = $current['election_id'];
$e_status = $current['status'];

// NORMAL ELECTION CONTROLS
if (isset($_POST['start'])) {
    $conn->query("UPDATE Election SET status='open', start_time=NOW() WHERE election_id=$eid");
    $status_msg = '<div class="success">Election Started!</div>';
}
if (isset($_POST['stop'])) {
    $conn->query("UPDATE Election SET status='closed', end_time=NOW() WHERE election_id=$eid");
    $status_msg = '<div class="error">Election Stopped – Results Published!</div>';
}
if (isset($_POST['reset'])) {
    $conn->query("DELETE FROM Vote WHERE election_id=$eid");
    $conn->query("UPDATE Student SET has_voted=0");
    $conn->query("DELETE FROM Candidate WHERE election_id=$eid");
    $status_msg = '<div class="success">Current Election Reset!</div>';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRES - Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); margin: 20px 0; }
        button { padding: 14px 28px; font-size: 16px; border: none; border-radius: 10px; cursor: pointer; margin: 8px; }
        input { padding: 12px; width: 70%; border-radius: 8px; border: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <h1 style="text-align:center; color:#1976d2; margin-bottom:10px;">CRES Admin Panel</h1>
    <p style="text-align:center;"><a href="logout.php">Logout</a></p>

    <?php echo $status_msg; ?>

    <!-- Create New Election -->
    <div class="card">
        <h2>Create New Election</h2>
        <form method="POST">
            <input type="text" name="election_title" placeholder="e.g. CR Election 2025-26" required>
            <button name="new_election" style="background:#1976d2; color:white;">Create Election</button>
        </form>
    </div>

    <!-- Current Election Info -->
    <div class="card">
        <h2>Current Election: <strong><?php echo htmlspecialchars($current['title']); ?></strong></h2>
        <p>Status: <strong style="color:<?php echo $e_status=='open'?'green':'#d32f2f'; ?>;">
            <?php echo ucfirst($e_status); ?>
        </strong></p>

        <form method="POST" style="margin:20px 0;">
            <button name="start" style="background:#28a745; color:white;">Start Election</button>
            <button name="stop" style="background:#d32f2f; color:white;">Stop & Publish Results</button>
            <button name="reset" style="background:#ff9800; color:white;" 
                    onclick="return confirm('Delete all votes and candidates in this election?')">Reset Current Election</button>
        </form>
    </div>

    <!-- Navigation -->
    <div class="card" style="text-align:center;">
        <a href="admin_candidates.php"><button style="background:#4a00e0; color:white; padding:18px 40px; font-size:18px;">Manage Candidates</button></a>
        <a href="admin_results.php"><button style="background:#17a2b8; color:white; padding:18px 40px; font-size:18px;">View Results & Download PDF</button></a>
        <a href="admin_history.php"><button style="background:#6c757d; color:white; padding:18px 40px; font-size:18px;">Election History</button></a>
    </div>

    <!-- DANGER ZONE -->
    <div class="card" style="background:#ffebee; border:3px solid #f44336; text-align:center;">
        <h2 style="color:#c62828;">DANGER ZONE</h2>
        <p style="color:#b71c1c; font-weight:bold;">
            Permanently delete <u>ALL elections, votes, and candidates</u> from the system
        </p>
        <form method="POST" onsubmit="return confirm('THIS CANNOT BE UNDONE! Are you 100% sure?')">
            <button name="reset_everything" 
                    style="background:#c62828; color:white; padding:20px 50px; font-size:20px; font-weight:bold; border-radius:12px;">
                RESET ENTIRE ELECTION HISTORY
            </button>
        </form>
    </div>
</div>
</body>
</html>