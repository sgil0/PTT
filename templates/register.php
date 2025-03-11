<form role="form" action="controleur.php">
<section class="vh-100 gradient-custom">
  <div class="container py-6 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 pb-5">
              <img src="./ressources/logoPinf.png" alt="Elpis" class="profile-img" style="width:80px; height:80px;">
              <h2 class="fw-bold mb-2 text-uppercase">Créer un compte</h2>
              <br>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="text" id="typeEmailX" class="form-control form-control-lg" name="email" placeholder="Adresse email"/>
              </div>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="password" id="typePasswordX" class="form-control form-control-lg" name="passe" placeholder="Mot de passe"/>
              </div>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="text" id="typeEmailX" class="form-control form-control-lg" name="nom" placeholder="Nom"/>
              </div>

              <div data-mdb-input-init class="form-outline form-white mb-4">
                <input type="text" id="typeEmailX" class="form-control form-control-lg" name="prenom" placeholder="Prénom"/>
              </div>

            <select class="form-select" aria-label="Default select example" name="type">
                <option selected>Vous êtes...</option>
                <option value="particulier">Particulier</option>
                <option value="entreprise">Entreprise</option>
                <option value="copropriété">Copropriété / SCI</option>
                <option value="artisans">Artisans</option>
            </select>

            <br>
            
            <button class="btn btn-outline-light btn-lg px-4" type="submit" name="action" value="creer compte" >Créer mon compte</button>
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