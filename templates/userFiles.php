<?php
include_once 'header.php';
include_once 'navBar.php';

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit();
}

$id_utilisateur = $_SESSION['idUser'];
$dossiers = getDossiersByUser($id_utilisateur);
$prenom = getPrenom($id_utilisateur);
$nom = getNom( $id_utilisateur);
//svar_dump($dossiers);
?>

<div class="container mt-4">
    <h2 class="mb-4">Mes dossiers</h2>

    <?php if (count($dossiers) > 0): ?>
        <div class="accordion" id="accordionDossiers">
            <?php foreach ($dossiers as $index => $dossier): ?>
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header" id="heading<?= $index ?>">
                        <button class="accordion-button collapsed d-flex gap-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                            <span><strong><?= $nom . ' ' . $prenom?></strong></span>
                            <span><?= date('d/m/Y', strtotime($dossier['date_creation'])) ?></span>
                            <span><strong>État :</strong> <?= htmlspecialchars($dossier['etat']) ?></span>
                        </button>
                    </h2>
                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionDossiers">
                        <div class="accordion-body d-flex justify-content-between align-items-start">
                            <div>
                                <p><strong>Détails de la demande :</strong></p>
                                <p><?= nl2br(htmlspecialchars($dossier['details'] ?? 'Aucun détail')) ?></p>
                                <p><strong>Montant de l'aide :</strong> 
                                <?php 
                                    $json = json_decode($dossier['valeur_simulation'], true);
                                    echo isset($json['montant']) ? htmlspecialchars($json['montant']) . ' €' : 'Non spécifié';
                                ?>
                                </p>
                            </div>
                            <div>
                                <?php if (!empty($dossier['pdf_recap'])): ?>
                                    <form method="post" action="/telecharger_facture.php" target="_blank">
                                        <input type="hidden" name="id_dossier" value="<?= $dossier['id_dossier'] ?>">
                                        <button class="btn btn-outline-primary" type="submit">Voir le PDF récapitulatif</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-muted">Aucun PDF disponible</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Aucun dossier trouvé.</div>
    <?php endif; ?>
</div>

<?php include_once 'footer.php'; ?>
