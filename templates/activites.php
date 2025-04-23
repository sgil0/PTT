<?php
require_once __DIR__ . '/../libs/modele.php';


// Lecture du type d'utilisateur transmis dans l'URL
$type = $_GET['type'] ?? 'particulier';

// Liste des types reconnus pour affichage
$libelles = [
    'particulier' => 'Particulier',
    'professionnel' => 'Professionnel',
    'collectivite' => 'Collectivité',
    'association' => 'Association'
];

$libelle = $libelles[$type] ?? 'Utilisateur';

// Gestion de la soumission du formulaire
if (!empty($_POST['titre']) && !empty($_POST['contenu'])) {
    $titre = addslashes($_POST['titre']);
    $contenu = addslashes($_POST['contenu']);
    createActivite($titre, $contenu, $type);
}
?>

<h2><?= htmlspecialchars($libelle) ?> - Proposer une activité</h2>

<form method="post" style="margin: 20px 0;">
    <input type="text" name="titre" placeholder="Titre de l'activité" required style="width: 100%; padding: 10px; margin-bottom: 10px;"><br>
    <textarea name="contenu" placeholder="Décrivez votre activité..." required style="width: 100%; padding: 10px;"></textarea><br>
    <button type="submit" style="margin-top: 10px; padding: 10px 20px;">Publier</button>
</form>

<hr>

<h3>Activités publiées :</h3>

<?php
$activites = getActivitesByType($type);

if (count($activites) > 0) {
    foreach ($activites as $actu) {
        echo "<div style='margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>" . htmlspecialchars($actu['titre']) . "</strong><br>";
        echo "<p>" . nl2br(htmlspecialchars($actu['contenu'])) . "</p>";
        echo "<small>Posté le " . $actu['date_creation'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>Aucune activité pour le moment.</p>";
}
?>
