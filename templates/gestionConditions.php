<?php
include_once __DIR__ . "/../libs/maLibSQL.pdo.php";

$aide_id = $_GET['aide_id'] ?? null;
if (!$aide_id) die("Aide non spécifiée.");

// Traitement de l'enregistrement des conditions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supprimer les conditions existantes pour cette aide
    SQLExec("DELETE FROM conditions_aides WHERE aide_id = ?", [$aide_id]);

    if (isset($_POST['conditions']) && is_array($_POST['conditions'])) {
        foreach ($_POST['conditions'] as $question_id => $valeur) {
            SQLInsert("INSERT INTO conditions_aides (aide_id, question_id, valeur_attendue) VALUES (?, ?, ?)", [$aide_id, $question_id, $valeur]);
        }
    }
}

// Données nécessaires
$aide = SQLSelect("SELECT * FROM aides WHERE id = ?", [$aide_id])[0];
$questions = SQLSelect("SELECT * FROM questions");
$propositions = SQLSelect("SELECT * FROM propositions");
$conditions_existantes = SQLSelect("SELECT * FROM conditions_aides WHERE aide_id = ?", [$aide_id]);

// Indexer les conditions existantes par question
$conditions_map = [];
foreach ($conditions_existantes as $cond) {
    $conditions_map[$cond['question_id']] = $cond['valeur_attendue'];
}

// Grouper les propositions par question
$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['texte'];
}
?>

<h1>Conditions pour l'aide : <?= htmlspecialchars($aide['nom']) ?></h1>

<form method="POST">
<?php foreach ($questions as $q): ?>
    <div style="margin-bottom:20px">
        <label><strong><?= htmlspecialchars($q['intitule']) ?></strong> (<?= $q['type'] ?>)</label><br>
        <?php if ($q['type'] == 'bool'): ?>
            <select name="conditions[<?= $q['id'] ?>]">
                <option value="">-- indifférent --</option>
                <option value="oui" <?= ($conditions_map[$q['id']] ?? '') === 'oui' ? 'selected' : '' ?>>Oui</option>
                <option value="non" <?= ($conditions_map[$q['id']] ?? '') === 'non' ? 'selected' : '' ?>>Non</option>
            </select>
        <?php elseif ($q['type'] == 'select'): ?>
            <select name="conditions[<?= $q['id'] ?>]">
                <option value="">-- indifférent --</option>
                <?php foreach ($prop_map[$q['id']] ?? [] as $option): ?>
                    <option value="<?= $option ?>" <?= ($conditions_map[$q['id']] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option>
                <?php endforeach; ?>
            </select>
        <?php elseif ($q['type'] == 'number'): ?>
            <input type="number" name="conditions[<?= $q['id'] ?>]" value="<?= htmlspecialchars($conditions_map[$q['id']] ?? '') ?>">
        <?php endif; ?>
    </div>
<?php endforeach; ?>

    <button type="submit">Enregistrer les conditions</button>
</form>

<a href="gestionSimulateur.php">Retour</a>
