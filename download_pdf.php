<?php
require_once 'vendor/tcpdf/tcpdf.php';
include 'includes/db_connect.php';

$eid = $conn->query("SELECT election_id FROM Election ORDER BY election_id DESC LIMIT 1")->fetch_assoc()['election_id'];
$title = $conn->query("SELECT title FROM Election WHERE election_id=$eid")->fetch_assoc()['title'];
$total = $conn->query("SELECT COUNT(*) FROM Vote WHERE election_id=$eid")->fetch_row()[0];

$pdf = new TCPDF();
$pdf->SetCreator('CRES System');
$pdf->SetTitle('Election Results');
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'ELECTION RESULTS', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Total Votes: $total", 0, 1, 'C');
$pdf->Ln(10);

$res = $conn->query("SELECT c.name, COUNT(v.vote_id) as votes 
                     FROM Candidate c 
                     LEFT JOIN Vote v ON c.candidate_id=v.candidate_id AND v.election_id=$eid
                     WHERE c.election_id=$eid 
                     GROUP BY c.candidate_id ORDER BY votes DESC");

$rank = 1;
while($r = $res->fetch_assoc()) {
    $pdf->SetFont('helvetica', $rank==1 ? 'B' : '', $rank==1 ? 16 : 12);
    $winner = $rank==1 ? " ← WINNER" : "";
    $pdf->Cell(0, 10, "$rank. " . $r['name'] . " → " . $r['votes'] . " votes $winner", 0, 1);
    $rank++;
}

$pdf->Output("Election_Results_" . date('d-m-Y') . ".pdf", 'D');
?>