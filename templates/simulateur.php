
<?php
// On vérifie si l'utilisateur est connecté et si c'est un admin
    if (isset($_SESSION['id_utilisateur'])) {
    $idUser = $_SESSION['id_utilisateur'];
    $isAdmin = isUserAdmin($idUser); // Appelle la fonction
} else {
    $isAdmin = false; // L'utilisateur n'est pas connecté
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>simulations</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #edit-section { display: none; }
    </style>
</head>
<body>
    <h1>Simulateur</h1>

    <div id="view">
        <p>Contenu de la page...</p>
        <?php if ($isAdmin): ?>
            <button id="edit-btn">Modifier</button>
        <?php endif; ?>
    </div>

    <div id="edit">
        <h2> Mode édition</h2>
        <textarea>Modifier le contenu ici...</textarea>
        <button id="save-btn">Sauvegarder</button>
        <button id="cancel-btn">Annuler</button>
    </div>

    <script>
        $(document).ready(function () {
            $("#edit-btn").click(function () {
                $("#view-section").hide();
                $("#edit-section").show();
            });

            $("#cancel-btn, #save-btn").click(function () {
                $("#edit-section").hide();
                $("#view-section").show();
            });
        });
    </script>
</body>
</html>
