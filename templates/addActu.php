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

<style> 
/* ------------------------ */
/* 1. Style global          */
/* ------------------------ */
body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC; /* Fond clair */
  color: #333;
  min-height: 100vh;
}

/* ------------------------ */
/* 2. Titres                */
/* ------------------------ */
h2 {
  text-align: center;
  color: #d96c2c;      /* Orange soutenu */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}

/* ------------------------ */
/* 3. Conteneur du formulaire */
/* ------------------------ */
form {
  max-width: 600px;
  margin: 0 auto;
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* ------------------------ */
/* 4. Paragraphes et labels  */
/* ------------------------ */
form p {
  margin-bottom: 15px;
}

label {
  font-weight: bold;
  color: #333;
}

/* ------------------------ */
/* 5. Champs de formulaire   */
/* ------------------------ */
input[type="text"],
textarea,
input[type="file"] {
  width: 100%;
  padding: 8px 10px;
  margin-top: 6px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
  box-sizing: border-box;
  display: block;
}

/* ------------------------ */
/* 6. Bouton d'enregistrement */
/* ------------------------ */
button[type="submit"] {
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7;
  font-weight: bold;
  border: none;
  border-radius: 50px 0 50px 50px; /* Forme "feuille" */
  padding: 10px 20px;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
  display: block;
  margin: 20px auto 0 auto;
}

button[type="submit"]:hover {
  background-color: #d96c2c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>