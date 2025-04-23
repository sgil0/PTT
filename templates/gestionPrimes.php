<?php
include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

// Dossier de destination pour les images
$uploadDir = "uploads/primes/";
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

// Ajout d'une prime
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'], $_FILES['image'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'] ?? '';
    $imageName = '';

    if ($_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('prime_') . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    SQLInsert("INSERT INTO primes (nom, description, image) VALUES (?, ?, ?)", [$nom, $description, $imageName]);
}

// Récupération des primes
$primes = SQLSelect("SELECT * FROM primes ORDER BY id DESC");
?>

<h1>Gestion des Primes</h1>

<h2>Ajouter une prime</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" rows="3"></textarea><br><br>

    <label>Image :</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">Ajouter la prime</button>
</form>

<hr>

<h2>Primes existantes</h2>
<div style="display: flex; flex-wrap: wrap; gap: 20px;">
<?php foreach ($primes as $prime): ?>
    <div style="border: 1px solid #ccc; padding: 10px; width: 200px;">
        <img src="<?= $uploadDir . htmlspecialchars($prime['image']) ?>" alt="image" style="max-width: 100%; height: auto;">
        <h4><?= htmlspecialchars($prime['nom']) ?></h4>
        <p style="font-size: 0.9em;"><?= nl2br(htmlspecialchars($prime['description'])) ?></p>
        <a href="index.php?view=gestionConditionsPrime&prime_id=<?= $prime['id'] ?>" class="btn btn-sm btn-outline-primary">Définir critères</a>
    </div>
<?php endforeach; ?>
</div>

<style>
/* =========================
   1. Style global
   ========================= */
   body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC; /* Fond clair */
  color: #333;
}

h1, h2 {
  text-align: center;
  color: #d96c2c;  /* Orange soutenu */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}

/* =========================
   2. Formulaire d'ajout de prime
   ========================= */
form[method="POST"] {
  max-width: 600px;
  margin: 0 auto 30px auto;
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

form[method="POST"] label {
  font-weight: bold;
  color: #333;
}

form[method="POST"] input[type="text"],
form[method="POST"] textarea,
form[method="POST"] input[type="file"] {
  width: 100%;
  padding: 8px 10px;
  margin-top: 6px;
  margin-bottom: 16px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
  display: block;
}

/* Bouton "Ajouter la prime" */
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
  margin: 0 auto;
}
button[type="submit"]:hover {
  background-color: #d96c2c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* =========================
   3. Liste des primes
   ========================= */
/* Conteneur principal des cartes (display:flex, flex-wrap...) */
div[style*="display: flex; flex-wrap: wrap; gap: 20px;"] {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 10px;
}

/* Carte de chaque prime */
div[style*="border: 1px solid #ccc; padding: 10px; width: 200px;"] {
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}
div[style*="border: 1px solid #ccc; padding: 10px; width: 200px;"]:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Image de la prime */
div[style*="border: 1px solid #ccc; padding: 10px; width: 200px;"] img {
  max-width: 100%;
  height: auto;
  border-radius: 5px;
  margin-bottom: 10px;
}

/* Titre de la prime */
div[style*="border: 1px solid #ccc; padding: 10px; width: 200px;"] h4 {
  color: #d96c2c;
  margin: 5px 0;
}

/* Description de la prime */
div[style*="border: 1px solid #ccc; padding: 10px; width: 200px;"] p {
  font-size: 0.9em;
  margin: 5px 0;
}

/* =========================
   4. Bouton "Définir critères"
   ========================= */
/* .btn.btn-sm.btn-outline-primary */
.btn-outline-primary {
  border: 2px solid #007bff !important; /* Couleur "primary" bootstrap par défaut */
  color: #007bff !important;
  background-color: transparent !important;
  border-radius: 50px 0 50px 50px !important; /* Forme "feuille" */
  font-weight: bold !important;
  padding: 6px 12px !important;
  text-decoration: none !important;
  display: inline-block;
  transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
}

/* Hover sur le bouton "Définir critères" */
.btn-outline-primary:hover {
  background-color: #007bff !important;
  color: #fff !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>