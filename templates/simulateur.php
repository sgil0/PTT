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
  position: relative; /* Pour la vague décorative en haut */
}



/* =========================
   3. Titres et zones de texte
   ========================= */
h1, h2 {
  color: #d96c2c;
  font-weight: bold;
  margin-top: 40px;
  text-align: center;
}

p, label {
  color: #333;
}



/* =========================
   5. Style des blocs "questions"
   ========================= */
   form > div[style*="margin-bottom:20px"] {
  background: linear-gradient(to bottom right, #fff5e6, #ffe9cc);
  border: 1px solid #f4a63c;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(244, 166, 60, 0.15);
  padding: 20px 25px;
  margin-bottom: 24px;
  transition: box-shadow 0.3s, transform 0.2s;
}

form > div[style*="margin-bottom:20px"]:hover {
  box-shadow: 0 6px 16px rgba(244, 166, 60, 0.25);
  transform: translateY(-2px);
}

form label {
  font-weight: bold;
  color: #d96c2c;
  font-size: 1.05rem;
  margin-bottom: 10px;
  display: block;
}


/* =========================
   6. Champs de formulaire
   ========================= */
input[type="number"],
select {
  width: 100%;
  max-width: 400px;
  padding: 6px 8px;
  margin-top: 6px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
}

/* =========================
   7. Bouton "Vérifier mes aides"
   ========================= */
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

/* Effet au survol */
button[type="submit"]:hover {
  background-color: #d96c2c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* =========================
   8. Liste des aides éligibles
   ========================= */
ul {
  list-style-type: disc;
  margin-left: 1.5rem;
}

li strong {
  color: #d96c2c;
}

/* =========================
   9. Bouton admin
   ========================= */
.btn-outline-secondary {
  color: #333 !important;
  border: 2px solid #f4a63c !important;
  border-radius: 20px !important;
  font-weight: bold !important;
  transition: background 0.3s, color 0.3s;
}

.btn-outline-secondary:hover {
  background: #f4a63c !important;
  color: #fff !important;
}

/* =========================
   10. Ajustements
   ========================= */
h2 {
  margin-top: 30px;
}

h2 + p,  /* Le paragraphe "Aucune aide ne correspond..." */
h2 + ul { 
  margin-top: 10px;
  margin-bottom: 30px;
  text-align: center;
}

.d-flex.justify-content-between.align-items-center {
  margin-top: 30px;
  display: flex;
  justify-content: flex-end; /* Bouton admin à droite */
}
</style>