<?php
include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

if (isset($_SESSION['idUser'])) {
    $idUser = $_SESSION['idUser'];
    $isAdmin = isUserAdmin($idUser);
} else {
    $isAdmin = false;
}



$questions = SQLSelect("SELECT * FROM questions");
$propositions = SQLSelect("SELECT * FROM propositions");
$primes = SQLSelect("SELECT * FROM primes");
$conditions = SQLSelect("SELECT * FROM conditions_primes");

$questions = is_array($questions) ? $questions : [];
$propositions = is_array($propositions) ? $propositions : [];
$primes = is_array($primes) ? $primes : [];
$conditions = is_array($conditions) ? $conditions : [];

$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['proposition'];
}

$cond_map = [];
foreach ($conditions as $c) {
    $cond_map[$c['prime_id']][] = $c;
}

$resultats = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reponses = $_POST['reponses'] ?? [];

    foreach ($primes as $prime) {
        $valide = true;
        foreach ($cond_map[$prime['id']] ?? [] as $cond) {
            $qID = $cond['question_id'];
            $typeCond = $questions[array_search($qID, array_column($questions, 'id'))]['type'];
            $reponse = trim($reponses[$qID] ?? '');

            if ($typeCond === 'bool' || $typeCond === 'select') {
                if ($cond['valeur_attendue'] === '' || $reponse === '') continue;
                $attendues = explode(',', $cond['valeur_attendue']);
                if (!in_array($reponse, $attendues)) {
                    $valide = false;
                    break;
                }
            }

            if ($typeCond === 'number') {
                if ($reponse === '') continue;
                $val = (int)$reponse;
                if (($cond['borne_min'] !== null && $val < $cond['borne_min']) ||
                    ($cond['borne_max'] !== null && $val > $cond['borne_max'])) {
                    $valide = false;
                    break;
                }
            }
        }
        if ($valide) $resultats[] = $prime;
    }
}
?>

<h1>Simulateur de primes</h1>

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

    <button type="submit">Voir mes primes</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <h2>Primes éligibles :</h2>
    <?php if (empty($resultats)): ?>
        <p>Aucune prime ne correspond à vos réponses.</p>
    <?php else: ?>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <?php foreach ($resultats as $prime): ?>
            <div style="border: 1px solid #ccc; padding: 10px; width: 250px;">
                <img src="uploads/primes/<?= htmlspecialchars($prime['image']) ?>" style="max-width:100%; height:auto;">
                <h4><?= htmlspecialchars($prime['nom']) ?></h4>
                <p><?= nl2br(htmlspecialchars($prime['description'])) ?></p>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center">
    <?php if ($isAdmin): ?>
        <a href="./index.php?view=gestionSimulateur" class="btn btn-sm btn-outline-secondary">🛠 Admin</a>
    <?php endif; ?>
</div>
