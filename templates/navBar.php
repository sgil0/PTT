<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
  <a class="navbar-brand" href="#">
      <img src="./ressources/logoPinf.png" alt="Logo" width="60" height="49" class="d-inline-block align-text-top">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav  nav-underline">
        <a id="accueil" class="nav-link" aria-current="page" href="./index.php?view=accueil">Accueil</a>
        <a id="simulateur" class="nav-link" href="./index.php?view=simulateur">Simulations</a>
        <a id = ""class="nav-link" href="./index.php?view=primes">Nos Primes</a>
        <a class="nav-link" href="./index.php?view=actu">Actus</a>
<?php 
  
  if (valider("connecte","SESSION")){
    $idUser=valider('idUser','SESSION');
    $nomUser=getNom($idUser);
    $prenomUser=getPrenom($idUser);

    if (whoIsHe($idUser) == "Admin") {
      echo ' 
        <a class="nav-link" href="./index.php?view=admin">Administration</a> 
      ';
    } else {
      echo ' 
      <a class="nav-link" href="./index.php?view=userFiles">Mes  dossiers</a> 
      ';
    }

    echo '</div> </div>
        <div class="d-flex flex-column align-items-center">
        <div><a href="?view=userSettings" class="link-dark link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover"><strong>' . $nomUser . ' ' . $prenomUser . '</strong></a></div>
    ';
  } else {
    echo '
    </div> </div>
    <div class="d-flex align-items-center">
    <form class="container-fluid ">
    <a href="?view=login" class="btn btn-secondary" tabindex="-1" role="button" aria-disabled="true" style="margin-top:15px;">Se connecter</a>
    </form>
    </div>

    ';
  }
?>  
  </div>
</nav>


