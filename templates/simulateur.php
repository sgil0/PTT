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

$questions = is_array($questions) ? $questions : [];
$propositions = is_array($propositions) ? $propositions : [];

$prop_map = [];
foreach ($propositions as $p) {
    $prop_map[$p['question_id']][] = $p['proposition'];
}
?>

<h1>Simulateur de primes</h1>

<form id="simulateurForm">
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

<!-- Résultat AJAX -->
<div id="resultatsPrimes" style="margin-top: 30px;"></div>

<!-- Notification -->
<div id="toastPrime" style="
    display: none;
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #f07e1f;
    color: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    font-weight: bold;
    z-index: 1000;
">
    🎁 Une ou plusieurs primes correspondent à vos réponses !
</div>

<!-- Bouton admin -->
<div class="d-flex justify-content-between align-items-center">
    <?php if ($isAdmin): ?>
        <a href="./index.php?view=gestionSimulateur" class="btn btn-sm btn-outline-secondary">🛠 Admin</a>
    <?php endif; ?>
</div>

<!-- === jQuery AJAX === -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#simulateurForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();

    $.ajax({
        url: 'templates/ajax_simulateur.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.status === 'success') {
                $('#resultatsPrimes').html(response.html);

                // Animation toast si une prime détectée
                if (response.html.includes('<div style="border: 1px solid')) {
                    $('#toastPrime').fadeIn(300).delay(3000).fadeOut(500);
                }

            } else {
                $('#resultatsPrimes').html("<p>Une erreur est survenue.</p>");
            }
        },
        error: function() {
            $('#resultatsPrimes').html("<p>Impossible de contacter le serveur.</p>");
        }
    });
});
</script>

<!-- === Styles === -->
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC;
  min-height: 100vh;
}
h1, h2 {
  color: #d96c2c;
  font-weight: bold;
  margin-top: 40px;
  text-align: center;
}
form > div[style*="margin-bottom:20px"] {
  background: linear-gradient(to bottom right, #fff5e6, #ffe9cc);
  border: 1px solid #f4a63c;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(244, 166, 60, 0.15);
  padding: 20px 25px;
  margin-bottom: 24px;
}
input[type="number"], select {
  width: 100%;
  max-width: 400px;
  padding: 6px 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 0.95rem;
}
button[type="submit"] {
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7;
  font-weight: bold;
  border: none;
  border-radius: 50px 0 50px 50px;
  padding: 10px 20px;
  cursor: pointer;
}
button[type="submit"]:hover {
  background-color: #d96c2c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.btn-outline-secondary {
  color: #333 !important;
  border: 2px solid #f4a63c !important;
  border-radius: 20px !important;
  font-weight: bold !important;
}
.btn-outline-secondary:hover {
  background: #f4a63c !important;
  color: #fff !important;
}
</style>
