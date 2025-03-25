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
