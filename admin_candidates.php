<?php include 'includes/db_connect.php'; include 'includes/functions.php'; 
if (!isLoggedIn() || !isAdmin()) redirect('index.php');
$eid = $conn->query("SELECT election_id FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc()['election_id'];
if (isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $manifesto = $conn->real_escape_string($_POST['manifesto']);
    $symbol = $_FILES['symbol']['name'] ? "images/".time()."_".basename($_FILES['symbol']['name']) : "";
    if ($symbol) move_uploaded_file($_FILES['symbol']['tmp_name'], $symbol);
    $stmt = $conn->prepare("INSERT INTO Candidate (election_id,name,manifesto,symbol) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $eid, $name, $manifesto, $symbol); $stmt->execute();
}
if (isset($_POST['delete'])) {
    $did = (int)$_POST['delete_id'];
    $conn->query("DELETE FROM Candidate WHERE candidate_id=$did");
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Candidates</title><link rel="stylesheet" href="css/style.css">
<style>body{background:#f4f6f9;} .container{max-width:1100px;margin:20px auto;padding:30px;}</style>
</head><body>
<div class="container">
    <h1>Manage Candidates</h1>
    <a href="admin_dashboard.php">← Back</a>
    <form method="POST" enctype="multipart/form-data" style="background:white;padding:25px;border-radius:12px;margin:20px 0;">
        <input type="text" name="name" placeholder="Name" required><br><br>
        <textarea name="manifesto" placeholder="Manifesto" rows="4" style="width:100%;"></textarea><br><br>
        <input type="file" name="symbol" accept="image/*"><br><br>
        <button name="add" style="padding:12px 30px;background:#4a00e0;color:white;">Add Candidate</button>
    </form>
    <?php $cands = $conn->query("SELECT * FROM Candidate WHERE election_id=$eid");
    while($c = $cands->fetch_assoc()): ?>
    <div style="background:white;padding:20px;margin:15px 0;border-radius:12px;display:flex;justify-content:space-between;align-items:center;">
        <div><?php if($c['symbol']) echo "<img src='{$c['symbol']}' width='80' style='border-radius:10px;'>"; ?>
            <strong><?= htmlspecialchars($c['name']) ?></strong><br><?= nl2br(htmlspecialchars($c['manifesto'])) ?>
        </div>
        <form method="POST"><input type="hidden" name="delete_id" value="<?= $c['candidate_id'] ?>">
            <button name="delete" style="background:#dc3545;color:white;padding:8px 15px;" onclick="return confirm('Delete?')">Delete</button>
        </form>
    </div>
    <?php endwhile; ?>
</div>
</body></html>