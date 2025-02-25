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
      <div class="d-flex align-items-center">
        <div class="d-flex flex-column align-items-center">
        <img src="./ressources/profil.png" alt="Profil" class="profile-img" style="width:35px; height:35px;">
        <span class="profile-text mt-1"><strong>' . $nomUser . ' ' . $prenomUser . '</strong></span>
        </div>
    </div>

    ';
  } else {
    echo '
    <form class="container-fluid ">
    <a href="#" class="btn btn-primary disabled" tabindex="-1" role="button" aria-disabled="true">Primary link</a>
    </form>


    ';
  }


?>
      
    
  </div>
</nav>


