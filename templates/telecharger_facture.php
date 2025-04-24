<?php
require_once '../controleur.php';

if (!isset($_POST['id_dossier'])) {
    exit("ID dossier manquant.");
}

$id_dossier = intval($_POST['id_dossier']);

global $bdd;
$sql = "SELECT pdf_recap FROM dossiers WHERE id_dossier = ?";
$stmt = $bdd->prepare($sql);
$stmt->execute([$id_dossier]);
$dossier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dossier || empty($dossier['pdf_recap'])) {
    exit("Aucun PDF trouvé pour ce dossier.");
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="recap_' . $id_dossier . '.pdf"');
echo $dossier['pdf_recap'];
exit;
