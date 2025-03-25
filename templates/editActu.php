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

// Récupérer l'ID de l'actualité (en utilisant valider() ou $_GET)
$idActu = valider("id"); // ou $_GET['id'] si vous préférez
if (!$idActu) {
    echo "ID d'actualité manquant.";
    exit;
}

// Récupérer les infos de l'actualité
$actu = getActuById($idActu);
if ($actu instanceof PDOStatement) {
    // Effectuer le fetch pour obtenir un tableau associatif
    $actu = $actu->fetch(PDO::FETCH_ASSOC);
}
if (!$actu) {
    echo "Actualité introuvable.";
    exit;
}
?>
<h2>Modifier l'actualité</h2>
<form action="controleur.php?action=saveEditActu" method="post">
  <input type="hidden" name="id" value="<?= $actu['id_actualite'] ?>">
  
  <p>
    <label for="titre">Titre :</label>
    <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($actu['titre']) ?>">
  </p>
  
  <p>
    <label for="contenu">Contenu :</label>
    <textarea name="contenu" id="contenu"><?= htmlspecialchars($actu['contenu']) ?></textarea>
  </p>
  
  <!-- Ajoutez d'autres champs si nécessaire -->
  
  <p>
    <button type="submit">Enregistrer</button>
  </p>
</form>
