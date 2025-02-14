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
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bglight text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 pb-5">

              <h2 class="fw-bold mb-2 text-black text-uppercase">Login</h2>
              <p class="text-black-50 mb-5">Please enter your login and password!</p>

              <div data-mdb-input-init class="form-outline form-black mb-4">
                <input type="text" id="typeEmailX" class="form-control form-control-lg" name="login" value="<?php echo $login;?>"/>
                <label class="form-label" for="typeEmailX">Email</label>
              </div>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="password" id="typePasswordX" class="form-control form-control-lg" name="passe" value="<?php echo $passe;?>"/>
                <label class="form-label" for="typePasswordX">Password</label>
              </div>
              <button class="btn btn-outline-dark btn-lg px-5" type="submit" name="action" value="Connexion" >Connexion</button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</form>

