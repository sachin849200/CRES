<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
if (!isLoggedIn()) redirect('index.php');

$election = $conn->query("SELECT election_id, status FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc();
$eid = $election['election_id'];
$is_published = ($election['status'] === 'closed');  // Only show results when admin stops election
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Election Results</h1>
    <a href="<?= isAdmin() ? 'admin_dashboard.php' : 'student_dashboard.php' ?>">← Back</a> | 
    <a href="logout.php">Logout</a>

    <?php if (!$is_published): ?>
        <div class="error" style="font-size:18px;padding:30px;">
            Results will be published by Admin after voting ends.<br><br>
            Please check back later.
        </div>
    <?php else: 
        // Show ranked results only after admin stops election
        $res = $conn->query("SELECT c.name, c.symbol, c.manifesto, COUNT(v.vote_id) as votes 
                             FROM Candidate c 
                             LEFT JOIN Vote v ON c.candidate_id = v.candidate_id AND v.election_id = $eid 
                             WHERE c.election_id = $eid 
                             GROUP BY c.candidate_id 
                             ORDER BY votes DESC, c.name ASC");

        $total_votes = $conn->query("SELECT COUNT(*) as t FROM Vote WHERE election_id=$eid")->fetch_assoc()['t'];
        $rank = 1;
        ?>
        <div class="success" style="font-size:22px;padding:20px;">
            Winner: <strong><?= $res->fetch_assoc()['name'] ?? 'No votes yet' ?></strong>
            <?php $res->data_seek(0); // Reset pointer ?>
        </div>

        <h2>Final Ranking</h2>
        <?php while ($r = $res->fetch_assoc()): ?>
            <div class="card" style="display:flex;align-items:center;justify-content:space-between;background:#<?= $rank==1?'d4edda':($rank==2?'fff3cd':($rank==3?'d1ecf1':'f8f9ff')) ?>;">
                <div>
                    <strong style="font-size:20px;">#<?= $rank ?> → <?= htmlspecialchars($r['name']) ?></strong><br>
                    <em><?= $r['votes'] ?> votes 
                    <?= $total_votes > 0 ? '(' . round($r['votes']/$total_votes*100, 1) . '%)' : '' ?></em>
                </div>
                <?php if ($r['symbol']) echo "<img src='{$r['symbol']}' width='80' style='border-radius:12px;'>"; ?>
            </div>
            <?php $rank++; endwhile; ?>

        <p><strong>Total Votes Cast:</strong> <?= $total_votes ?></p>
    <?php endif; ?>
</div>
</body>
</html>