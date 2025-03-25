<?php
include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

if (isset($_SESSION['idUser'])) {
    $idUser = $_SESSION['idUser'];
    $isAdmin = isUserAdmin($idUser);
} else {
    $isAdmin = false;
}

// Récupérer toutes les questions et propositions
$questions = SQLSelect("SELECT * FROM questions");
$propositions = SQLSelect("SELECT * FROM propositions");
$aides = SQLSelect("SELECT * FROM aides");
$conditions = SQLSelect("SELECT * FROM conditions_aides");
$questions = $questions ?: [];
$propositions = $propositions ?: [];
$aides = $aides ?: [];
$conditions = $conditions ?: [];


// Indexation pour accélérer l'accès
$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['proposition'];
}

$cond_map = [];
foreach ($conditions as $c) {
    $cond_map[$c['aide_id']][] = [
        'question_id' => $c['question_id'],
        'valeur' => $c['valeur_attendue']
    ];
}

// Traitement du formulaire
$resultats = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reponses = $_POST['reponses'] ?? [];

    foreach ($aides as $aide) {
        $valide = true;
        foreach ($cond_map[$aide['id']] ?? [] as $cond) {
            $qID = $cond['question_id'];
            $attendue = strtolower(trim($cond['valeur']));
            $reponse = strtolower(trim($reponses[$qID] ?? ''));
            if ($reponse === '') continue; // indifférent
            if ($reponse != $attendue) {
                $valide = false;
                break;
            }
        }
        if ($valide) $resultats[] = $aide;
    }
}
?>

<h1>Simulateur d'aides</h1>

<form method="POST">
<?php foreach ($questions as $q): ?>
    <div style="margin-bottom:20px">
        <label><strong><?= htmlspecialchars($q['question']) ?></strong></label><br>

        <?php if ($q['type'] === 'bool'): ?>
            <select name="reponses[<?= $q['id'] ?>]">
                <option value="">-- Choisir --</option>
                <option value="oui">Oui</option>
                <option value="non">Non</option>
            </select>

        <?php elseif ($q['type'] === 'select'): ?>
            <select name="reponses[<?= $q['id'] ?>]">
                <option value="">-- Choisir --</option>
                <?php foreach ($prop_map[$q['id']] ?? [] as $opt): ?>
                    <option value="<?= $opt ?>"><?= $opt ?></option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($q['type'] === 'number'): ?>
            <input type="number" name="reponses[<?= $q['id'] ?>]">
        <?php endif; ?>
    </div>
<?php endforeach; ?>

    <button type="submit">Vérifier mes aides</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <h2>Aides éligibles :</h2>
    <?php if (empty($resultats)): ?>
        <p>Aucune aide ne correspond à vos réponses.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($resultats as $aide): ?>
            <li><strong><?= htmlspecialchars($aide['nom']) ?></strong> : <?= htmlspecialchars($aide['description']) ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center">
    <?php if ($isAdmin): ?>
        <a href="./index.php?view=gestionSimulateur" class="btn btn-sm btn-outline-secondary">🛠 Admin</a>
    <?php endif; ?>
</div>
