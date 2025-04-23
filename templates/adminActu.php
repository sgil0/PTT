<?php

// On vérifie que l'utilisateur est admin, sinon on redirige
$idUser = $_SESSION['idUser'] ?? 0;
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

if (!isUserAdmin($idUser)) {
    header('Location: actu.php');
    exit;
}

$actualites = getActualites();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Administration des Actualités</title>
  <style>
/* ------------------------ */
/* 1. Style global & conteneur */
/* ------------------------ */
body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC;
  color: #333;
}
h1, h2 {
  text-align: center;
  color: #d96c2c;
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}
#listeActualites {
  max-width: 800px;
  margin: 20px auto;
  padding: 0 10px;
}

/* ------------------------ */
/* 2. Cartes "actualite"    */
/* ------------------------ */
.actualite {
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 20px;
  display: flex;
  align-items: flex-start;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}
.actualite:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
.actualite img {
  max-width: 150px;
  border-radius: 4px;
  margin-right: 15px;
}
.actualite h2 {
  margin-top: 0;
  color: #d96c2c;
}
.actualite p {
  margin: 5px 0;
}

/* ------------------------ */
/* 3. Liens et boutons      */
/* ------------------------ */
/* Pour tous les liens "Modifier", "Supprimer" et "Ajouter une actualité",
   on applique le même style de bouton "feuille". */

/* - "Modifier" a la classe .edit-link */
/* - "Supprimer" pointe vers deleteActu.php */
/* - "Ajouter une actualité" pointe vers addActu.php */

.edit-link,
a[href*="deleteActu.php"],
a[href*="addActu.php"] {
  display: inline-block;
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7 !important;
  padding: 6px 12px;
  border-radius: 50px 0 50px 50px; /* Forme "feuille" */
  font-weight: bold;
  text-decoration: none;
  margin-right: 5px;
  transition: transform 0.3s, box-shadow 0.3s, background 0.3s;
}

.edit-link:hover,
a[href*="deleteActu.php"]:hover,
a[href*="addActu.php"]:hover {
  background-color: #d96c2c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* ------------------------ */
/* 4. Modal (si besoin)     */
/* ------------------------ */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-content {
  background: #fff;
  padding: 20px;
  max-width: 600px;
  width: 90%;
  border-radius: 5px;
  position: relative;
}
.close-modal {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
}

  </style>
  <!-- Inclusion de jQuery pour faciliter l'AJAX -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h1>Administration des Actualités</h1>
  
  <p><a href="templates/addActu.php">Ajouter une actualité</a></p>
  
  <div id="listeActualites">
    <?php foreach($actualites as $actu): ?>
      <div class="actualite" data-id="<?= $actu['id_actualite'] ?>">
        <img src="<?= htmlspecialchars($actu['image_actu']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
        <div>
          <h2><?= htmlspecialchars($actu['titre']) ?></h2>
          <p><?= htmlspecialchars($actu['contenu']) ?></p>
          <p>Publié le : <?= htmlspecialchars($actu['date_publication']) ?></p>
          <p>Auteur : <?= htmlspecialchars($actu['id_auteur']) ?></p>
          <p>
            <!-- Lien pour modifier, on ajoute une classe pour déclencher l'AJAX -->
            <a href="templates/editActu.php?id=<?= $actu['id_actualite'] ?>" class="edit-link">Modifier</a> | 
            <a href="templates/deleteActu.php?id=<?= $actu['id_actualite'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette actualité ?');">Supprimer</a>
          </p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  
  <!-- Modal pour charger le contenu AJAX -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div id="modalBody">
        <!-- Le contenu de la page editActu.php sera chargé ici -->
      </div>
    </div>
  </div>
  
  <script>
    // Lorsque l'on clique sur le lien "Modifier"
    $(document).on('click', '.edit-link', function(e) {
      e.preventDefault();
      const url = $(this).attr('href');
      
      // Charger le contenu de editActu.php dans le modal
      $('#modalBody').load(url, function(response, status, xhr) {
        if(status === "error") {
          alert("Erreur lors du chargement: " + xhr.statusText);
        } else {
          $('#modal').fadeIn();
        }
      });
    });
    
    // Fermer le modal lorsque l'on clique sur le "X"
    $('.close-modal').click(function() {
      $('#modal').fadeOut();
    });
    
    // Optionnel : fermer le modal en cliquant en dehors du contenu
    $(window).click(function(e) {
      if ($(e.target).is('#modal')) {
        $('#modal').fadeOut();
      }
    });
  </script>
</body>
</html>
