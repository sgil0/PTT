<?php
session_start();

require_once __DIR__ . '/../libs/maLibUtils.php';
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

// Vérifier que l'utilisateur est admin
$idUser = $_SESSION['idUser'] ?? 0;
if (!isUserAdmin($idUser)) {
    echo "Accès refusé.";
    exit;
}

// Récupérer l'ID de l'actualité à supprimer
$idActu = valider("id"); // ou $_GET['id']
if (!$idActu) {
    echo "ID d'actualité manquant.";
    exit;
}

// Suppression de l'actualité via une fonction du modèle
if (deleteActu($idActu)) {
    // Rediriger vers la vue d'administration avec un message de succès
    header("Location: ../index.php?view=adminActu&message=Suppression%20reussie");
    exit;

} else {
    echo "Erreur lors de la suppression de l'actualité.";
}
?>
