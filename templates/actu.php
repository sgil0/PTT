<?php

// Récupération de l'ID utilisateur ou définition d'une valeur par défaut (0 si non connecté)
$idUser = $_SESSION['idUser'] ?? 0;

require_once __DIR__ . '/../libs/maLibSQL.pdo.php';
require_once __DIR__ . '/../libs/modele.php';

// Récupération de la liste des actualités depuis la base de données
$actualites = getActualites();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fil d'actualité - Actualités de l'entreprise</title>
  <style>
    /* Styles de base pour l'affichage des actualités */
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

<?php
if (isset($_SESSION['idUser'])) {
    $idUser = $_SESSION['idUser'];
    $isAdmin = isUserAdmin($idUser);
} else {
    $isAdmin = false;
}
?>

<body>
  <h1>Fil d'actualité</h1>
  
  <!-- Bouton redirigeant vers l'interface admin (via index.php) visible uniquement pour les administrateurs -->
  <?php if (isUserAdmin($idUser)): ?>
    <p><a href="index.php?view=adminActu">Passer en mode édition</a></p>
  <?php endif; ?>

  <div id="listeActualites">
    <?php foreach($actualites as $actu): ?>
      <div class="actualite draggable" data-id="<?= $actu['id_actualite'] ?>" draggable="true">
        <img src="<?= htmlspecialchars($actu['image_actu']) ?>" alt="<?= htmlspecialchars($actu['titre']) ?>">
        <div>
          <h2><?= htmlspecialchars($actu['titre']) ?></h2>
          <p><?= htmlspecialchars($actu['contenu']) ?></p>
          <p>Publié le : <?= htmlspecialchars($actu['date_publication']) ?></p>
          <p>Auteur : <?= htmlspecialchars($actu['id_auteur']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Script JavaScript pour le drag-and-drop (fonctionnel pour tous, si nécessaire) -->
  <script>
    const draggables = document.querySelectorAll('.draggable');
    const container = document.getElementById('listeActualites');

    draggables.forEach(draggable => {
      draggable.addEventListener('dragstart', () => {
        draggable.classList.add('dragging');
      });
      draggable.addEventListener('dragend', () => {
        draggable.classList.remove('dragging');
        // Récupération du nouvel ordre pour vérification (vous pouvez l'envoyer au serveur si besoin)
        const order = Array.from(document.querySelectorAll('.actualite')).map(item => item.dataset.id);
        console.log('Nouvel ordre:', order);
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
          return { offset: offset, element: child };
        } else {
          return closest;
        }
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
  </script>
</body>
</html>
