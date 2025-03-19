
<?php
// Vérifie si l'utilisateur est connecté et récupère son ID
if (isset($_SESSION['idUser'])) { // Utilisation correcte de la session
    $idUser = $_SESSION['idUser']; 
    $isAdmin = isUserAdmin($idUser);
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
        $("#edit").hide();
        $(document).ready(function () {
            $("#edit-btn").click(function () {
                $("#view").hide();
                $("#edit").show();
            });

            $("#cancel-btn, #save-btn").click(function () {
                $("#edit").hide();
                $("#view").show();
            });
        });
    </script>
</body>
</html>
