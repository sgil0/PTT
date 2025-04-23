<?php
// inclusion dynamique des pages si besoin
if (isset($_GET['view']) && file_exists($_GET['view'])) {
    include($_GET['view']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Elpis</title>
    <style>
        body {
            text-align: center;
        }

        .button-group {
            margin-top: 50px;
        }
        .user-button {
            display: inline-block;
            margin: 20px;
            padding: 20px 40px;
            background-color: #f9c242;
            border-radius: 20px;
            color: #c0392b;
            font-size: 1.2em;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="button-group">
        <a class="user-button" href="index.php?view=activites&type=particulier">Je suis un Particulier</a>
        <a class="user-button" href="index.php?view=activites&type=professionnel">Je suis un Professionnel</a><br>
        <a class="user-button" href="index.php?view=activites&type=collectivite">Je suis une collectivité</a>
        <a class="user-button" href="index.php?view=activites&type=association">Je suis une association</a>
    </div>
</body>
</html>
