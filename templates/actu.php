<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page 10</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #edit-section { display: none; }
    </style>
</head>
<body>
    <h1>Actu</h1>

    <div id="view-section">
        <p>Contenu de la page...</p>
        <?php if ($isAdmin): ?>
            <button id="edit-btn">Modifier</button>
        <?php endif; ?>
    </div>

    <div id="edit-section">
        <h2>Page 11 - Mode édition</h2>
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
