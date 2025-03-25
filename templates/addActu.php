<?php
session_start();

// Inclusion des dépendances nécessaires
require_once __DIR__ . '/../libs/maLibUtils.php';
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

// Vérifier que l'utilisateur est admin
$idUser = $_SESSION['idUser'] ?? 0;
if (!isUserAdmin($idUser)) {
    echo "Accès refusé.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter une actualité</title>
</head>
<body>
  <h2>Ajouter une actualité</h2>
  <!-- Le formulaire envoie les données vers le contrôleur (controleur.php) avec l'action addActuProcess -->
  <form action="../controleur.php?action=addActuProcess" method="post" enctype="multipart/form-data">
    <p>
      <label for="titre">Titre :</label><br>
      <input type="text" name="titre" id="titre" required>
    </p>
    <p>
      <label for="contenu">Contenu :</label><br>
      <textarea name="contenu" id="contenu" rows="5" cols="40" required></textarea>
    </p>
    <!-- La date de publication et l'id de l'auteur seront définis automatiquement dans le contrôleur -->
    <p>
      <label for="image_actu">Image (optionnel) :</label><br>
      <input type="file" name="image_actu" id="image_actu" accept="image/*">
    </p>
    <p>
      <button type="submit">Enregistrer</button>
    </p>
  </form>
</body>
</html>
