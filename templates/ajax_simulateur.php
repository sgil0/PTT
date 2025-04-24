<?php
session_start();
header('Content-Type: application/json');

include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

if (!isset($_SESSION['idUser'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
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

$reponses = $_POST['reponses'] ?? [];
$resultats = [];

foreach ($primes as $prime) {
    $valide = true;
    foreach ($cond_map[$prime['id']] ?? [] as $cond) {
        $qID = $cond['question_id'];
        $typeCond = $questions[array_search($qID, array_column($questions, 'id'))]['type'] ?? '';
        $reponse = trim($reponses[$qID] ?? '');

        if ($typeCond === 'bool' || $typeCond === 'select') {
            if ($reponse === '') {
                $valide = false;
                break;
            }
            $attendues = explode(',', $cond['valeur_attendue']);
            if (!in_array($reponse, $attendues)) {
                $valide = false;
                break;
            }
        }

        if ($typeCond === 'number') {
            if ($reponse === '') {
                $valide = false;
                break;
            }
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

ob_start();
if (empty($resultats)) {
    echo "<p>Aucune prime ne correspond à vos réponses.</p>";
} else {
    echo '<div style="display: flex; flex-wrap: wrap; gap: 20px;">';
    foreach ($resultats as $prime) {
        echo '<div style="border: 1px solid #ccc; padding: 10px; width: 250px;">';
        echo '<img src="uploads/primes/' . htmlspecialchars($prime['image']) . '" style="max-width:100%; height:auto;">';
        echo '<h4>' . htmlspecialchars($prime['nom']) . '</h4>';
        echo '<p>' . nl2br(htmlspecialchars($prime['description'])) . '</p>';
        echo '</div>';
    }
    echo '</div>';
}
$html = ob_get_clean();

echo json_encode(['status' => 'success', 'html' => $html]);
