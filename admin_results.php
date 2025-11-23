<?php include 'includes/db_connect.php'; include 'includes/functions.php'; 
if (!isLoggedIn() || !isAdmin()) redirect('index.php');
$eid = $conn->query("SELECT election_id FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc()['election_id'];
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Results</title><link rel="stylesheet" href="css/style.css">
<style>body{background:#f4f6f9;} .container{max-width:1100px;margin:20px auto;padding:30px;}</style>
</head><body>
<div class="container">
    <h1>Results & Reports</h1>
    <a href="admin_dashboard.php">← Back</a><br><br>
    <a href="download_pdf.php"><button style="padding:20px 50px;font-size:20px;background:#d81b60;color:white;">Download Official PDF</button></a>
    <br><br>
    <iframe src="results.php" width="100%" height="700" style="border:none;"></iframe>
</div>
</body></html>