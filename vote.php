<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
if (!isLoggedIn()) redirect('index.php');

if ($_POST['candidate_id']) {
    $user_id = $_SESSION['user_id'];
    $cand_id = (int)$_POST['candidate_id'];

    // Check election is open and user hasn't voted
    $open = $conn->query("SELECT election_id FROM Election WHERE status='open' LIMIT 1")->fetch_assoc();
    $has_voted = $conn->query("SELECT has_voted FROM Student WHERE voter_id=$user_id")->fetch_assoc()['has_voted'];

    if (!$open || $has_voted) {
        redirect('student_dashboard.php');
    }

    $eid = $open['election_id'];
    $stmt = $conn->prepare("INSERT INTO Vote (voter_id, candidate_id, election_id) VALUES (?,?,?)");
    $stmt->bind_param("iii", $user_id, $cand_id, $eid);
    $stmt->execute();
    $conn->query("UPDATE Student SET has_voted=1 WHERE voter_id=$user_id");
    
    // Success page instead of results
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vote Recorded</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container">
        <h1>Thank You!</h1>
        <div class="success" style="font-size:20px;padding:30px;">
            Your vote has been recorded successfully.
        </div>
        <p>Results will be announced by the Admin after voting ends.</p>
        <br>
        <a href="student_dashboard.php"><button>Back to Dashboard</button></a>
        <br><br>
        <a href="logout.php">Logout</a>
    </div>
    </body>
    </html>
    <?php
    exit();
}
?>