<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
if (!isLoggedIn() || !isAdmin()) redirect('index.php');

$eid = $conn->query("SELECT election_id FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc()['election_id'];
$e_title = $conn->query("SELECT title FROM Election WHERE election_id=$eid")->fetch_assoc()['title'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voters List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Voters List - <?= htmlspecialchars($e_title) ?></h1>
    <a href="admin_dashboard.php">Back</a> | <a href="logout.php">Logout</a>

    <table style="margin-top:20px;width:100%;border-collapse:collapse;">
        <tr style="background:#f0f0f0;"><th>S.No</th><th>Name</th><th>Reg No</th><th>Voted At</th></tr>
        <?php
        $res = $conn->query("SELECT s.name, s.reg_no, v.timestamp FROM Vote v JOIN Student s ON v.voter_id=s.voter_id WHERE v.election_id=$eid ORDER BY v.timestamp DESC");
        $i = 1;
        while($r = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= $r['reg_no'] ?></td>
            <td><?= date('d M Y, h:i:s A', strtotime($r['timestamp'])) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($i == 1): ?>
        <tr><td colspan="4" style="text-align:center;padding:30px;">No votes cast yet.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>