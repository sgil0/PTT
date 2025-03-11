<?php

// // Vérification de l'authentification
// if (!isset($_SESSION['user'])) {
//     header("Location: login.php");
//     exit();
// }

// Récupération des informations de l'utilisateur (exemple : depuis la session)
$idUser=valider('idUser','SESSION');
// var_dump($idUser);
$user = getUtilisateur($idUser); // Ex : ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@example.com']
// var_dump($user);
?>

<body>
    <div class="container my-5">
        <h1 class="mb-4">Paramètres Utilisateur</h1>

        <!-- Informations personnelles -->
        <div class="card mb-4">
            <div class="card-header"> 
                Informations personnelles
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> <?php echo $user[0]['nom'] ?></p>
                <p><strong>Prénom :</strong> <?php echo $user[0]['prenom'] ?></p>
                <p><strong>Email :</strong> <?php echo $user[0]['email'] ?></p>
            </div>
        </div>

        <!-- Formulaire pour modifier l'Email -->
        <div class="card mb-4">
            <div class="card-header">
                Modifier l'Email
            </div>
            <div class="card-body">
                <form method="post" action="controleur.php">
                    <div class="mb-3">
                        <label for="new_email" class="form-label">Nouvel Email :</label>
                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                    </div>
                    <button type="submit" class="btn btn-primary"  value="maj_email">Mettre à jour l'Email</button>
                </form>
            </div>
        </div>

        <!-- Formulaire pour modifier le mot de passe -->
        <div class="card mb-4">
            <div class="card-header">
                Modifier le Mot de Passe
            </div>
            <div class="card-body">
                <form method="post" action="controleur.php">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel :</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe :</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer nouveau mot de passe :</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour le Mot de Passe</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
