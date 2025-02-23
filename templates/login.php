<?php

// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=login");
	die("");
}

// Chargement eventuel des données en cookies
$login = valider("login", "COOKIE");
$passe = valider("passe", "COOKIE"); 
if ($checked = valider("remember", "COOKIE")) $checked = "checked"; 

?>
<form role="form" action="controleur.php">
<section class="vh-100 gradient-custom">
  <div class="container py-6 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 pb-5">
            <img src="./ressources/logoPinf.png" alt="Elpis" class="profile-img" style="width:80px; height:80px;">
              <h2 class="fw-bold mb-2 text-uppercase">Se connecter</h2>
              <br>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="text" id="typeEmailX" class="form-control form-control-lg" name="login" placeholder="Email" value="<?php echo $login;?>"/>
                
              </div>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="password" id="typePasswordX" class="form-control form-control-lg" name="passe" placeholder="Mot de passe" value="<?php echo $passe;?>"/>
                
                
              </div>
              
              <button class="btn btn-outline-light btn-lg px-4" type="submit" name="action" value="Connexion" >Se connecter</button>
              
              <br>
              <br>
              <p>Vous n'avez pas de compte ? <a href="./index.php?view=register" class="link-warning link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover">Créer un compte</a></p>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</form>

<style>
  body {
    background-color: #f1f1f3;
  }
</style>