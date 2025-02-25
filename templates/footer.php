<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}

?>

<!-- Fin du maPage -->
</div>

<div id="footer" class="bg-light">
  <div class="container">
    <div class="d-flex align-items-center" style="margin-top:15px; margin-bottom:15px;justify-content: center;">
      <?php
      // Si l'utilisateur est connecté, on affiche le bouton de déconnexion
      if (valider("connecte", "SESSION")) {
        echo '
          <a href="controleur.php?action=Logout" class="btn btn-secondary">
            Se déconnecter
          </a>
        ';
      }
      ?>
      <!-- Éléments communs affichés dans tous les cas -->
      <span class="ms-3">|‎ ‎ ‎ </span>
	  <span  style="font-weight:bold;">Nous contacter :‎ ‎ </span>
	  <div class="d-flex flex-column ms-4"> 
		  <span>Particulier :</span>
		  <span>- contact@elpis60.fr</span>
	  	  <span>- 03 10 45 45 10</span>
	</div>
	<div class="d-flex flex-column ms-4"> 
		  <span>Pro/association :</span>
		  <span>- partenaire@elpis60.fr</span>
	  	  <span>- 07 65 71 95 05</span>
	</div>
	<div class="d-flex flex-column ms-5"> 
		  <span>Mentions légales</span>
	</div>
    </div>
  </div>
</div>

</body>
</html>

<style>
#footer {
    position: fixed;
    bottom: 0;
    right:0;
    left:0;
    background-color: transparent;
    text-align: left;
}
</style>
