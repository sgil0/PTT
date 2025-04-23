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
          <p>Publié le : <?= htmlspecialchars(date("Y-m-d", strtotime($actu['date_publication']))) ?></p>
          <p>Auteur : <?= htmlspecialchars($actu['prenom_auteur'] . ' ' . $actu['nom_auteur']) ?></p>
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

<style> 
/* ------------------------ */
/* 1. Style global          */
/* ------------------------ */
body {
  margin: 0;
  padding: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #FCFCFC; /* Fond clair */
  color: #333;
}

/* ------------------------ */
/* 2. Titres                */
/* ------------------------ */
h1, h2 {
  text-align: center;
  color: #d96c2c;      /* Couleur orange soutenue */
  font-weight: bold;
  margin-top: 30px;
  margin-bottom: 20px;
}

/* ------------------------ */
/* 3. Conteneur des actualités */
/* ------------------------ */
#listeActualites {
  max-width: 800px;
  margin: 20px auto;
  padding: 0 10px;
}

/* ------------------------ */
/* 4. Bloc d'actualité       */
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

/* ------------------------ */
/* 5. Images des actualités  */
/* ------------------------ */
.actualite img {
  max-width: 150px;
  border-radius: 4px;
  margin-right: 15px;
}

/* ------------------------ */
/* 6. Titres et paragraphes dans chaque actualité */
/* ------------------------ */
.actualite h2 {
  margin-top: 0;
  color: #d96c2c;
}
.actualite p {
  margin: 5px 0;
}

/* ------------------------ */
/* 7. Curseur pour éléments draggables */
/* ------------------------ */
.draggable {
  cursor: move;
}

/* ------------------------ */
/* 8. Liens généraux         */
/* ------------------------ */
a {
  color: #d96c2c;
  text-decoration: none;
  transition: color 0.3s;
}

a:hover {
  color: #f07e1f;
}

/* ------------------------ */
/* 9. Bouton "Passer en mode édition" */
/* ------------------------ */
p > a {
  display: inline-block;
  background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
  color: #FAF6E7;
  padding: 8px 16px;
  border-radius: 50px 0 50px 50px; /* Forme "feuille" */
  font-weight: bold;
  transition: transform 0.3s, box-shadow 0.3s;
}

p > a:hover {
  background-color: #d96c2c;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>