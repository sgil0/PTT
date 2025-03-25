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
