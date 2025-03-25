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
    /* Styles de base */
    #listeActualites {
      max-width: 800px;
      margin: auto;
    }
    .actualite {
      border: 1px solid #ccc;
      padding: 10px;
      margin-bottom: 10px;
      display: flex;
      align-items: flex-start;
      background: #f9f9f9;
    }
    .actualite img {
      max-width: 150px;
      margin-right: 15px;
    }
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      align-items: center;
      justify-content: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      max-width: 600px;
      width: 90%;
      border-radius: 5px;
    }
    .close-modal {
      float: right;
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
