<?php

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit();
}

include_once 'header.php';
include_once 'navBar.php';

$id_utilisateur = $_SESSION['idUser'];
$dossiers = getDossiersByUser($id_utilisateur);
?>

<div class="container mt-5">
    <h1 class="mb-4">Mes dossiers</h1>
    <?php if (count($dossiers) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>État</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dossiers as $dossier): ?>
                    <tr>
                        <td><?= htmlspecialchars($dossier['id_dossier']) ?></td>
                        <td><?= htmlspecialchars($dossier['etat']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($dossier['date_creation'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aucun dossier en cours.</div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
