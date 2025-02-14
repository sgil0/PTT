<?php

//C'est la propriété php_self qui nous l'indique : 
// Quand on vient de index : 
// [PHP_SELF] => /chatISIG/index.php 
// Quand on vient directement par le répertoire templates
// [PHP_SELF] => /chatISIG/templates/accueil.php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
// Pas de soucis de bufferisation, puisque c'est dans le cas où on appelle directement la page sans son contexte
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}

?>
	  <a class="nav-link active" href="#">Accueil</a>
		<a class="nav-link" href="index.php?view=simulateur">Simulations</a>
    <a class="nav-link" href="index.php?view=primes">Nos primes</a>
    <a class="nav-link" href="index.php?view=actu">Actus</a>
    <?php
if (valider("isAdmin", "SESSION")) 
{
	echo '<a class="nav-link" href="index.php?view=administration">Administration</a>';
}
?>
      </div>
    </div>
  </div>
</nav>
