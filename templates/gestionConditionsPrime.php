<?php
include_once __DIR__ . '/../libs/maLibSQL.pdo.php';

$prime_id = $_GET['prime_id'] ?? null;
if (!$prime_id) die("Prime non spécifiée.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SQLExec("DELETE FROM conditions_primes WHERE prime_id = ?", [$prime_id]);

    foreach ($_POST['conditions'] as $question_id => $data) {
        $question_id = (int)$question_id;
        $type = $data['type'];

        if ($type === 'bool') {
            $valeur = $data['valeur'] ?? '';
            if ($valeur !== '') {
                SQLInsert("INSERT INTO conditions_primes (prime_id, question_id, valeur_attendue) VALUES (?, ?, ?)", [$prime_id, $question_id, $valeur]);
            }
        } elseif ($type === 'select') {
            if (!isset($data['indifferent']) && !empty($data['valeurs'])) {
                $valeur_str = implode(",", $data['valeurs']);
                SQLInsert("INSERT INTO conditions_primes (prime_id, question_id, valeur_attendue) VALUES (?, ?, ?)", [$prime_id, $question_id, $valeur_str]);
            }
        } elseif ($type === 'number') {
            $min = is_numeric($data['min']) ? (int)$data['min'] : null;
            $max = is_numeric($data['max']) ? (int)$data['max'] : null;
            if ($min !== null || $max !== null) {
                SQLInsert("INSERT INTO conditions_primes (prime_id, question_id, borne_min, borne_max) VALUES (?, ?, ?, ?)", [$prime_id, $question_id, $min, $max]);
            }
        }
    }
}

$prime = SQLSelect("SELECT * FROM primes WHERE id = ?", [$prime_id])[0] ?? null;
$questions = SQLSelect("SELECT * FROM questions");
$propositions = SQLSelect("SELECT * FROM propositions");
$conditions_existantes = SQLSelect("SELECT * FROM conditions_primes WHERE prime_id = ?", [$prime_id]);

$conditions_map = [];
foreach ($conditions_existantes as $cond) {
    $conditions_map[$cond['question_id']] = $cond;
}

$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['proposition'];
}
?>

<h1>Conditions pour la prime : <?= htmlspecialchars($prime['nom']) ?></h1>

<form method="POST">
<?php foreach ($questions as $q): ?>
    <?php $cond = $conditions_map[$q['id']] ?? [] ?>
    <div style="margin-bottom:20px">
        <label><strong><?= htmlspecialchars($q['question']) ?></strong> (<?= $q['type'] ?>)</label><br>

        <?php if ($q['type'] === 'bool'): ?>
            <select name="conditions[<?= $q['id'] ?>][valeur]">
                <option value="">-- indifférent --</option>
                <option value="oui" <?= ($cond['valeur_attendue'] ?? '') === 'oui' ? 'selected' : '' ?>>Oui</option>
                <option value="non" <?= ($cond['valeur_attendue'] ?? '') === 'non' ? 'selected' : '' ?>>Non</option>
            </select>

        <?php elseif ($q['type'] === 'select'): ?>
            <?php $selectedVals = isset($cond['valeur_attendue']) ? explode(",", $cond['valeur_attendue']) : []; ?>
            <?php foreach ($prop_map[$q['id']] ?? [] as $opt): ?>
                <label>
                    <input type="checkbox" name="conditions[<?= $q['id'] ?>][valeurs][]" value="<?= $opt ?>"
                        <?= in_array($opt, $selectedVals) ? 'checked' : '' ?>>
                    <?= $opt ?>
                </label><br>
            <?php endforeach; ?>
            <label>
                <input type="checkbox" name="conditions[<?= $q['id'] ?>][indifferent]" value="1"
                    <?= empty($cond['valeur_attendue']) ? 'checked' : '' ?>>
                Ignorer cette question
            </label>

        <?php elseif ($q['type'] === 'number'): ?>
            Min : <input type="number" name="conditions[<?= $q['id'] ?>][min]" value="<?= htmlspecialchars($cond['borne_min'] ?? '') ?>">
            Max : <input type="number" name="conditions[<?= $q['id'] ?>][max]" value="<?= htmlspecialchars($cond['borne_max'] ?? '') ?>">
        <?php endif; ?>

        <input type="hidden" name="conditions[<?= $q['id'] ?>][type]" value="<?= $q['type'] ?>">
    </div>
<?php endforeach; ?>

    <button type="submit">Enregistrer les conditions</button>
</form>

<a href="index.php?view=gestionPrimes">Retour aux primes</a>

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

h1 {
  text-align: center;
  color: #d96c2c;  /* Orange soutenu */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}

/* =========================
   2. Formulaire principal
   ========================= */
form[method="POST"] {
  max-width: 700px;
  margin: 0 auto 30px auto;
  background-color: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* =========================
   3. Blocs de questions
   ========================= */
/* Chaque question est dans un <div style="margin-bottom:20px"> */
form[method="POST"] > div[style*="margin-bottom:20px"] {
  background-color: #FAF6E7;
  border: 1px solid #f4a63c;
  border-radius: 6px;
  padding: 16px;
  margin-bottom: 20px !important; /* Réaffirme la marge */
}

form[method="POST"] label strong {
  color: #d96c2c;
}

/* =========================
   4. Champs de formulaire
   ========================= */
input[type="number"],
select,
input[type="checkbox"] {
  margin-top: 5px;
  margin-bottom: 10px;
}

input[type="number"] {
  width: 100px;
  padding: 4px 6px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

select {
  padding: 4px 6px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

label input[type="checkbox"] {
  margin-right: 5px;
}

/* =========================
   5. Bouton "Enregistrer les conditions"
   ========================= */
button[type="submit"] {
  display: block;
  margin: 20px auto 0 auto;
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
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* =========================
   6. Lien "Retour aux primes"
   ========================= */
/* Pour styliser également le lien "Retour aux primes" 
   en bouton feuille (si souhaité). */
a[href*="view=gestionPrimes"] {
  display: inline-block;
  margin: 0 auto;
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7 !important;
  font-weight: bold;
  border: none;
  border-radius: 50px 0 50px 50px; /* Forme "feuille" */
  padding: 8px 16px;
  text-decoration: none;
  transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
  margin-left: 20px; /* petite marge si besoin */
}

a[href*="view=gestionPrimes"]:hover {
  background-color: #d96c2c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>