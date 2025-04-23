<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="./ressources/logoPinf.png" alt="Logo" width="60" height="49" class="d-inline-block align-text-top">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" 
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav nav-underline">
        <a id="accueil" class="nav-link" aria-current="page" href="./index.php?view=accueil">Accueil</a>
        <a id="simulateur" class="nav-link" href="./index.php?view=simulateur">Simulations</a>
        <a class="nav-link" href="./index.php?view=planning">Prendre Rdv</a>
        <a class="nav-link" href="./index.php?view=i_activites">Nos activités</a>
        <a class="nav-link" href="./index.php?view=actu">Actus</a>
        <?php 
          if (valider("connecte","SESSION")){
            $idUser = valider('idUser','SESSION');
            $nomUser = getNom($idUser);
            $prenomUser = getPrenom($idUser);

            if (isUserAdmin($idUser)) {
              echo '<a class="nav-link" href="./index.php?view=admin">Administration</a>';
              echo '<a class="nav-link" href="./index.php?view=admin_permissions">Gérer les permissions</a>';
            } else {
              echo '<a class="nav-link" href="./index.php?view=userFiles">Mes  dossiers</a>';
            }
            echo '</div> </div>
                  <div class="d-flex flex-column align-items-center">
                    <div>
                      <a href="?view=userSettings" class="link-dark link-offset-2 
                         link-underline-opacity-0 link-underline-opacity-100-hover">
                        <strong>' . $nomUser . ' ' . $prenomUser . '</strong>
                      </a>
                    </div>';
          } else {
            echo '</div> </div>
                  <div class="d-flex align-items-center">
                    <form class="container-fluid">
                      <a href="?view=login" class="btn btn-secondary" tabindex="-1" role="button" aria-disabled="true" style="margin-top:15px;">Se connecter</a>
                    </form>
                  </div>';
          }
        ?>  
    </div>

    <!-- Styles -->
    <style>





      body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
      }

      /* Navbar */
      .navbar {
        background-color: #fff !important;
        padding: 1rem 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      /* Logo & marque */
      .navbar-brand {
        font-size: 28px;
        font-weight: bold;
        color: #d96c2c;
        display: flex;
        align-items: center;
      }

      .navbar-brand img {
        margin-right: 10px;
      }

      /* Style de base pour tous les boutons (liens et bouton "Se connecter") */
      .navbar-nav .nav-link,
      .d-flex .btn {
        background: linear-gradient(to bottom right, #f4a63c, #f07e1f);
        color: #FAF6E7 !important;
        padding: 8px 20px;
        border-radius: 50px 0 50px 50px; /* Forme de feuille */
        margin-left: 10px;
        font-weight: bold;
        transition: transform 0.3s, box-shadow 0.3s, color 0.3s;
        box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
        border: none;
        text-decoration: none;
      }

      .navbar-nav .nav-link:hover,
      .navbar-nav .nav-link:focus,
      .d-flex .btn:hover,
      .d-flex .btn:focus {
        border-color: rgba(0, 0, 0, 0.15);
        box-shadow: rgba(0, 0, 0, 0.1) 0 4px 12px;
        color: rgba(0, 0, 0, 0.65);
        transform: translateY(-1px);
      }

      .navbar-nav .nav-link:active,
      .d-flex .btn:active {
        border-color: rgba(0, 0, 0, 0.15);
        box-shadow: rgba(0, 0, 0, 0.06) 0 2px 4px;
        color: rgba(0, 0, 0, 0.65);
        transform: translateY(0);
      }
    </style>
</nav>