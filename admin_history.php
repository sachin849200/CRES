<?php include 'includes/db_connect.php'; include 'includes/functions.php'; 
if (!isLoggedIn() || !isAdmin()) redirect('index.php'); ?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Election History</title><link rel="stylesheet" href="css/style.css">
<style>body{background:#f4f6f9;} .container{max-width:1100px;margin:20px auto;padding:30px;}</style>
</head><body>
<div class="container">
    <h1>Election History</h1>
    <a href="admin_dashboard.php">← Back</a>
    <table style="width:100%;background:white;margin-top:20px;">
        <tr style="background:#333;color:white;"><th>Title</th><th>Status</th><th>Started</th><th>Ended</th></tr>
        <?php $all = $conn->query("SELECT * FROM Election ORDER BY election_id DESC");
        while($e = $all->fetch_assoc()): ?>
        <tr><td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
            <td><?= ucfirst($e['status']) ?></td>
            <td><?= $e['start_time'] ? date('d M Y h:i A', strtotime($e['start_time'])) : '-' ?></td>
            <td><?= $e['end_time'] ? date('d M Y h:i A', strtotime($e['end_time'])) : '-' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body></html>