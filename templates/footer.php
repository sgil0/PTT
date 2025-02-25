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
   	 <p class="text-muted credit">
		<?php
		// Si l'utilisateur est connecte, on affiche un lien de deconnexion 
		if (valider("connecte","SESSION"))
		{
			echo '
			</div>
			<div class="d-flex align-items-center">
			<form class="container-fluid ">
			<a href=\'controleur.php?action=Logout\' class="btn btn-secondary" tabindex="-1" role="button" aria-disabled="true" style="margin-top:15px;">Se deconnecter</a>
			</form>
			</div>

			';
		}
		?>
	</p>
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
