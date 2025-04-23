<?php
include_once __DIR__ . "/../libs/maLibSQL.pdo.php";

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    SQLExec("DELETE FROM questions WHERE id = ?", [$id]);
    header("Location: index.php?view=gestionSimulateur");
    exit;
}

// Chargement pour édition
$editQuestion = null;
$editProps = [];
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editQuestion = SQLSelect("SELECT * FROM questions WHERE id = ?", [$id])[0] ?? null;
    $editProps = SQLSelect("SELECT * FROM propositions WHERE question_id = ?", [$id]);
}


// Gestion des ajouts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_question') {
        $intitule = $_POST['intitule'];
        $type = $_POST['type'];
        $question_id = SQLInsert("INSERT INTO questions (question, type) VALUES (?, ?)", [$intitule, $type]);

        if ($type == 'select' && isset($_POST['propositions'])) {
            foreach ($_POST['propositions'] as $prop) {
                SQLInsert("INSERT INTO propositions (question_id, proposition) VALUES (?, ?)", [$question_id, $prop]);
            }
        }
    }
    if ($_POST['action'] == 'update_question') {
        $id = (int)$_POST['question_id'];
        $intitule = $_POST['intitule'];
        $type = $editQuestion['type'];
        SQLExec("UPDATE questions SET question = ? WHERE id = ?", [$intitule, $id]);
        SQLExec("DELETE FROM propositions WHERE question_id = ?", [$id]);
        if ($type == 'select' && isset($_POST['propositions'])) {
            foreach ($_POST['propositions'] as $prop) {
                $prop = trim($prop);
                if ($prop !== '') {
                    SQLInsert("INSERT INTO propositions (question_id, proposition) VALUES (?, ?)", [$id, $prop]);
                }
            }
        }            
        header("Location: index.php?view=gestionSimulateur");
        exit;
    }
}

// Récupération des questions
$questions = SQLSelect("SELECT * FROM questions");
$aides = SQLSelect("SELECT * FROM aides");
$questions = $questions ?: [];
$aides = $aides ?: [];
?>

<h1>Gestion du Simulateur</h1>

<h2><?= $editQuestion ? "Modifier la question" : "Ajouter une question" ?></h2>
<form method="POST">
    <input type="hidden" name="action" value="<?= $editQuestion ? 'update_question' : 'add_question' ?>">
    <?php if ($editQuestion): ?>
        <input type="hidden" name="question_id" value="<?= $editQuestion['id'] ?>">
    <?php endif; ?>

    <label>Intitulé :</label><br>
    <input type="text" name="intitule" value="<?= $editQuestion['question'] ?? '' ?>" required><br><br>

    <label>Type :</label><br>
    <select name="type" id="typeSelect" onchange="toggleProps()" <?= $editQuestion ? 'disabled' : '' ?>>
        <option value="bool" <?= ($editQuestion['type'] ?? '') === 'bool' ? 'selected' : '' ?>>Oui / Non</option>
        <option value="select" <?= ($editQuestion['type'] ?? '') === 'select' ? 'selected' : '' ?>>Choix multiple</option>
        <option value="number" <?= ($editQuestion['type'] ?? '') === 'number' ? 'selected' : '' ?>>Nombre</option>
    </select><br><br>

    <div id="propositionsBlock" style="display: <?= ($editQuestion['type'] ?? '') === 'select' ? 'block' : 'none' ?>">
        <label>Propositions :</label><br>
        <?php foreach ($editProps as $prop): ?>
            <input type="text" name="propositions[]" value="<?= $prop['proposition'] ?>" class="form-control mb-1">
        <?php endforeach; ?>
        <input type="text" name="propositions[]" class="form-control mb-1">
    </div><br>

    <button type="submit"><?= $editQuestion ? 'Mettre à jour' : 'Ajouter' ?></button>
</form>

<hr>

<h2>Questions existantes</h2>
<ul>
<?php foreach ($questions as $q): ?>
    <li>
        <strong><?= htmlspecialchars($q['question']) ?></strong> (<?= $q['type'] ?>)
        <a href="index.php?view=gestionSimulateur&edit=<?= $q['id'] ?>" class="btn btn-sm btn-warning">📝 Modifier</a>
        <a href="index.php?view=gestionSimulateur&delete=<?= $q['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette question ?')">🗑 Supprimer</a>
    </li>
<?php endforeach; ?>
</ul>

<hr>

<h2>Gestion des aides</h2>
<ul>
<?php foreach ($aides as $aide): ?>
    <li>
        <strong><?= htmlspecialchars($aide['nom']) ?></strong> - <a href="gestionConditions.php?aide_id=<?= $aide['id'] ?>">Définir les conditions</a>
    </li>
<?php endforeach; ?>
</ul>

<script>
function toggleProps() {
    const type = document.getElementById("typeSelect").value;
    document.getElementById("propositionsBlock").style.display = (type === 'select') ? 'block' : 'none';
}
</script>
<a href="index.php?view=gestionPrimes" class="btn btn-outline-success">🎁 Gérer les primes</a>

<style>
    /* =========================
   1. Style global
   ========================= */
body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC; /* Fond clair */
  min-height: 100vh;
}

/* =========================
   2. Titres et textes
   ========================= */
h1, h2 {
  color: #d96c2c;   /* Orange soutenu */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
  text-align: center;
}

label, p {
  color: #333;
  font-weight: 500;
}

strong {
  color: #d96c2c;  /* Pour souligner les éléments importants */
}

/* =========================
   3. Mise en page du formulaire
   ========================= */
/* Pour encadrer le formulaire d'ajout / édition de question */
form[method="POST"] {
  max-width: 600px;
  margin: 0 auto 30px auto;
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Champs de formulaire */
input[type="text"],
input[type="number"],
select {
  width: 100%;
  max-width: 500px;
  margin-top: 6px;
  margin-bottom: 16px;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
  display: block;
}

/* PropositionsBlock (inputs pour les choix multiples) */
#propositionsBlock .form-control.mb-1 {
  margin-bottom: 8px !important;
}

/* =========================
   4. Boutons
   ========================= */
/* Bouton principal (Ajouter / Mettre à jour) */
button[type="submit"] {
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7;
  font-weight: bold;
  border: none;
  border-radius: 50px 0 50px 50px; /* Forme "feuille" */
  padding: 10px 20px;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}

button[type="submit"]:hover {
  background-color: #d96c2c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Boutons .btn-sm (📝 Modifier / 🗑 Supprimer / Gérer primes) */
.btn-sm {
  font-size: 0.85rem;
  padding: 6px 12px;
  border-radius: 50px 0 50px 50px !important; /* même forme */
  transition: transform 0.3s, box-shadow 0.3s;
}

/* Harmoniser l'aspect tout en gardant la différence de couleur */
.btn-warning {
  background-color: #f0ad4e !important; /* Couleur "warning" bootstrap */
  border: none !important;
  color: #fff !important;
}
.btn-warning:hover {
  background-color: #ec971f !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.btn-danger {
  background-color: #d9534f !important; /* Couleur "danger" bootstrap */
  border: none !important;
  color: #fff !important;
}
.btn-danger:hover {
  background-color: #c9302c !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.btn-outline-success {
  border: 2px solid #5cb85c !important; /* Couleur "success" bootstrap */
  color: #5cb85c !important;
  border-radius: 50px 0 50px 50px !important;
  background-color: transparent !important;
}
.btn-outline-success:hover {
  background-color: #5cb85c !important;
  color: #fff !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* =========================
   5. Listes
   ========================= */
ul {
  margin: 20px auto;
  max-width: 700px;
  list-style-type: none;  /* On enlève les puces classiques */
  padding: 0;
}

ul li {
  background-color: #fff;
  margin-bottom: 10px;
  padding: 12px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* =========================
   6. Liens de la page
   ========================= */
a {
  text-decoration: none;
  color: #d96c2c;
}

a:hover {
  color: #f07e1f;
}
</style>