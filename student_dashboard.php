<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
if (!isLoggedIn()) redirect('index.php');

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT name, has_voted FROM Student WHERE voter_id=$user_id")->fetch_assoc();

// Get active election
$election = $conn->query("SELECT election_id, status FROM Election WHERE status='open' LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head><body>
<div class="container">
    <h1>Hello, <?= htmlspecialchars($user['name']) ?></h1>
    <a href="logout.php" style="float:right;">Logout</a>

    <?php if (!$election) { ?>
        <div class="error">Election is not open yet.</div>
        <a href="results.php">View Previous Results →</a>
    <?php } elseif ($user['has_voted']) { ?>
        <div class="success">You have already voted. Thank you!</div>
        <?php
$election_status = $conn->query("SELECT status FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc()['status'];
if ($election_status === 'closed') {
    echo '<br><a href="results.php"><button style="background:#17a2b8;">View Results</button></a>';
}
?>
    <?php } else {
        $candidates = $conn->query("SELECT * FROM Candidate WHERE election_id={$election['election_id']}");
        if ($candidates->num_rows == 0) {
            echo "<p>No candidates yet.</p>";
        } else { ?>
            <h2>Cast Your Vote</h2>
            <p>Choose one candidate:</p>
            <?php while ($c = $candidates->fetch_assoc()): ?>
            <div class="card">
                <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                <?= nl2br(htmlspecialchars($c['manifesto'])) ?>
                <?php if ($c['symbol']) echo "<br><img src='{$c['symbol']}' width='80' style='margin-top:10px;border-radius:10px;'>"; ?>
                <form method="POST" action="vote.php" style="margin-top:15px;">
                    <input type="hidden" name="candidate_id" value="<?= $c['candidate_id'] ?>">
                    <button type="submit">Vote for <?= htmlspecialchars($c['name']) ?></button>
                </form>
            </div>
            <?php endwhile;
        }
    } ?>
</div>
</body></html>