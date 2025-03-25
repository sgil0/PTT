<?php
session_start();
require_once __DIR__ . '/../libs/maLibUtils.php';
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

// Vérifier l'accès admin
$idUser = $_SESSION['idUser'] ?? 0;
if (!isUserAdmin($idUser)) {
    echo "Accès refusé.";
    exit;
}

// Récupérer l'id de l'actualité à modifier
$idActu = valider("id"); // ou $_GET['id']
if (!$idActu) {
    echo "ID manquant.";
    exit;
}

// Récupérer l’actualité
$actu = getActuById($idActu);

// Vérifier si la fonction a bien renvoyé un tableau
if (!$actu) {
    echo "Actualité introuvable.";
    exit;
}

// Ici, $actu est un tableau associatif avec les clés :
// 'id_actualite', 'titre', 'contenu', 'date_publication', 'image_actu', 'id_auteur'
?>

<h2>Modifier l'actualité</h2>
<form action="controleur.php?action=saveEditActu" method="post">
  <input type="hidden" name="id" value="<?= $actu['id_actualite'] ?>">
  
  <p>
    <label for="titre">Titre :</label><br>
    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($actu['titre']) ?>">
  </p>
  
  <p>
    <label for="contenu">Contenu :</label><br>
    <textarea name="contenu" id="contenu"><?= htmlspecialchars($actu['contenu']) ?></textarea>
  </p>
  
  <button type="submit">Enregistrer</button>
</form>
