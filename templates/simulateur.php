
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
<?php if ($isAdmin): ?>
    <button id="toggle-edit-mode">Mode Édition</button>
<?php endif; ?>
<body>
    <h1>Simulateur</h1>

    <div id="view">
    <h2>Simulations disponibles</h2>
    
    <?php
    include_once "libs/maLibSQL.pdo.php";
    $simulations = SQLSelect("SELECT * FROM simulations ORDER BY date_creation DESC");

    if (!$simulations || empty($simulations)) {
        echo "<p>Aucune simulation trouvée.</p>"; // Afficher un message au lieu d'une erreur
    } else {
        foreach ($simulations as $sim) {
            echo "<div class='simulation-card'>";
            echo "<h3><a href='simulation.php?id=" . $sim['id'] . "'>" . htmlspecialchars($sim['titre']) . "</a></h3>";
            echo "<p>" . htmlspecialchars($sim['description']) . "</p>";
            echo "<p class='date'>Créée le : " . $sim['date_creation'] . "</p>";
            echo "</div>";
        }
    }
    ?>
    </div>

    <div id="edit">
    <h2>Créer une nouvelle simulation</h2>
    <form method="POST" action="controleur.php">
        <input type="hidden" name="action" value="ajouter_simulation">
        
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre" required>
        
        <label for="description">Description :</label>
        <textarea name="description" id="description" required></textarea>
        
        <button type="submit">Créer</button>
        <button id="cancel-btn">Annuler</button>
    </form>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let editMode = false;
        const button = document.getElementById("toggle-edit-mode");

        button.addEventListener("click", function () {
            editMode = !editMode;

            if (editMode) {
                document.getElementById("view").style.display = "none";
                document.getElementById("edit").style.display = "block";
                button.textContent = "Mode Vue"; // Change le texte du bouton
            } else {
                document.getElementById("edit").style.display = "none";
                document.getElementById("view").style.display = "block";
                button.textContent = "Mode Édition"; // Change le texte du bouton
            }
        });
    });
</script>
</body>
</html>

<style>
/* Centrer le contenu principal */
#view, #edit {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Centrer le conteneur des simulations */
.simulation-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    max-width: 1000px;
    margin: auto;
}

/* Cartes de simulation */
.simulation-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    width: 280px;
    transition: transform 0.2s ease-in-out;
    text-align: center;
}

/* Effet au survol */
.simulation-card:hover {
    transform: scale(1.05);
}

/* Style du bouton Mode Édition */
#toggle-edit-mode {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease-in-out;
}

#toggle-edit-mode:hover {
    background-color: #0056b3;
}

/* Style du titre */
.simulation-card h3 a {
    text-decoration: none;
    color: #007bff;
    font-size: 18px;
}

.simulation-card h3 a:hover {
    text-decoration: underline;
}

/* Description */
.simulation-card p {
    font-size: 14px;
    color: #555;
    margin: 10px 0;
}

/* Date de création */
.simulation-card .date {
    font-size: 12px;
    color: #888;
}
</style>
