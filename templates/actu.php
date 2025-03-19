<?php
//session_start();
require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

// Récupération de la liste des actualités depuis la base de données
// Vous devez avoir défini cette fonction dans votre modèle
$actualites = getActualites();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fil d'actualité - Actualités de l'entreprise</title>
  <style>
    /* Style de base pour l'affichage des actualités */
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
    .draggable {
      cursor: move;
    }
  </style>
</head>
<body>
  <h1>Fil d'actualité</h1>
  
  <!-- Bouton d'ajout visible uniquement pour l'admin -->
   <?php $_SESSION['idUser'] = $idUser; ?>
  <?php if(isUserAdmin($idUser)): ?>
    <p><a href="addActu.php">Ajouter une actualité</a></p>
  <?php endif; ?>

  <div id="listeActualites">
  <?php foreach($actualites as $actu): ?>
  <div class="actualite draggable" data-id="<?= $actu['id_actualite'] ?>" draggable="true">
    <img src="<?= $actu['image_actu'] ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
    <div>
      <h2><?= htmlspecialchars($actu['titre']) ?></h2>
      <p><?= htmlspecialchars($actu['contenu']) ?></p>
      <!-- Si vous souhaitez afficher la date ou l'auteur -->
      <p>Publié le : <?= htmlspecialchars($actu['date_publication']) ?></p>
      <p>Auteur : <?= htmlspecialchars($actu['id_auteur']) ?></p>

      <!-- Liens d'administration (modification et suppression) -->
      <?php if(isUserAdmin($idUser)): ?>
        <p>
          <a href="editActu.php?id=<?= $actu['id_actualite'] ?>">Modifier</a> | 
          <a href="deleteActu.php?id=<?= $actu['id_actualite'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette actualité ?');">Supprimer</a>
        </p>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

  </div>

  <!-- Script JavaScript pour le drag-and-drop -->
  <script>
    const draggables = document.querySelectorAll('.draggable');
    const container = document.getElementById('listeActualites');

    draggables.forEach(draggable => {
      draggable.addEventListener('dragstart', () => {
        draggable.classList.add('dragging');
      });
      draggable.addEventListener('dragend', () => {
        draggable.classList.remove('dragging');
        // Récupération du nouvel ordre (à envoyer ensuite en AJAX pour sauvegarde dans la BDD)
        const order = Array.from(document.querySelectorAll('.actualite')).map(item => item.dataset.id);
        console.log('Nouvel ordre:', order);
        // Vous pouvez ici réaliser un appel AJAX pour sauvegarder l'ordre dans la base
      });
    });

    container.addEventListener('dragover', (e) => {
      e.preventDefault();
      const dragging = document.querySelector('.dragging');
      const afterElement = getDragAfterElement(container, e.clientY);
      if (afterElement == null) {
        container.appendChild(dragging);
      } else {
        container.insertBefore(dragging, afterElement);
      }
    });

    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll('.actualite:not(.dragging)')];
      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child }
        } else {
          return closest;
        }
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
  </script>
</body>
</html>
