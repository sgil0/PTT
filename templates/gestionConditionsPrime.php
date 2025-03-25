<?php
include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

$prime_id = $_GET['prime_id'] ?? null;
if (!$prime_id) die("Prime non spécifiée.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SQLExec("DELETE FROM conditions_primes WHERE prime_id = ?", [$prime_id]);

    if (isset($_POST['conditions']) && is_array($_POST['conditions'])) {
        foreach ($_POST['conditions'] as $question_id => $valeur) {
            if (trim($valeur) === '') continue; // on ignore si vide
            SQLInsert("INSERT INTO conditions_primes (prime_id, question_id, valeur_attendue) VALUES (?, ?, ?)", [$prime_id, $question_id, $valeur]);
        }
    }
}

$prime = SQLSelect("SELECT * FROM primes WHERE id = ?", [$prime_id])[0] ?? null;
$questions = SQLSelect("SELECT * FROM questions");
$propositions = SQLSelect("SELECT * FROM propositions");
$conditions_existantes = SQLSelect("SELECT * FROM conditions_primes WHERE prime_id = ?", [$prime_id]);

$conditions_map = [];
foreach ($conditions_existantes as $cond) {
    $conditions_map[$cond['question_id']] = $cond['valeur_attendue'];
}

$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['proposition'];
}
?>

<h1>Conditions pour la prime : <?= htmlspecialchars($prime['nom']) ?></h1>

<form method="POST">
<?php foreach ($questions as $q): ?>
    <div style="margin-bottom:20px">
        <label><strong><?= htmlspecialchars($q['question']) ?></strong> (<?= $q['type'] ?>)</label><br>
        <?php $selected = $conditions_map[$q['id']] ?? ''; ?>

        <?php if ($q['type'] == 'bool'): ?>
            <select name="conditions[<?= $q['id'] ?>]">
                <option value="">-- indifférent --</option>
                <option value="oui" <?= $selected === 'oui' ? 'selected' : '' ?>>Oui</option>
                <option value="non" <?= $selected === 'non' ? 'selected' : '' ?>>Non</option>
            </select>

        <?php elseif ($q['type'] == 'select'): ?>
            <select name="conditions[<?= $q['id'] ?>]">
                <option value="">-- indifférent --</option>
                <?php foreach ($prop_map[$q['id']] ?? [] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $selected === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($q['type'] == 'number'): ?>
            <input type="number" name="conditions[<?= $q['id'] ?>]" value="<?= htmlspecialchars($selected) ?>">
        <?php endif; ?>
    </div>
<?php endforeach; ?>

    <button type="submit">Enregistrer les conditions</button>
</form>

<a href="index.php?view=gestionPrimes">Retour aux primes</a>
