<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php");
    die("");
}
?>

<!-- Fin du maPage -->
</div>

<div id="footer" class="bg-light">

  <div class="container visible-footer">
    <div class="d-flex align-items-center" style="margin-top:15px; margin-bottom:15px; justify-content: center;">

      <?php
      if (valider("connecte", "SESSION")) {
        echo '
          <a href="controleur.php?action=Logout" class="btn btn-secondary btn-sm shadow-sm me-3">
            Se déconnecter
          </a>
        ';
      }
      ?>

      <!-- Bloc à dupliquer en texte décoratif -->
      <div id="footer-text-source">
        <span><strong>Nous contacter :</strong></span>
        <span>Particulier : contact@elpis60.fr - 03 10 45 45 10</span>
        <span>Pro/association : partenaire@elpis60.fr - 07 65 71 95 05</span>
        <span>Mentions légales</span>
      </div>

    </div>
  </div>

  <!-- Texte manuscrit décoratif -->
  <div id="footer-decor-text"></div>
</div>

<script>
  // Concatène les spans du bloc texte
  const lines = Array.from(document.querySelectorAll('#footer-text-source span'))
    .map(el => el.innerText.trim())
    .join(' | ');
  document.getElementById("footer-decor-text").innerText = lines;
</script>

</body>
</html>


<style>

@import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');

#footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 3;
    background: url('ressources/footer-bg.png') bottom center / cover no-repeat;
    padding-top: 0px;
    padding-bottom: 50px; /* 🔼 Hauteur augmentée */
    color: #fff;
    font-family: 'Segoe UI', sans-serif;
    border-top: none;
}

#footer .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: flex-start;
    text-align: left;
    gap: 40px;
    padding: 0 20px;
    z-index: 2;
    position: relative;
}

#footer-text-source {
    font-size: 0; /* Masque les sauts de ligne, mais reste lisible par JS */
    visibility: hidden;
    height: 0;
    overflow: hidden;
}

#footer span {
    display: block;
    margin: 2px 0;
}

#footer-decor-text {
    position: absolute;
    bottom: 10px;
    left: 0;
    width: 100%;
    font-family: 'Pacifico', cursive;
    font-size: 22px;
    text-align: center;
    background: linear-gradient(90deg, #f44336, #f9a825);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    pointer-events: none;
    opacity: 1;
    padding: 0 20pxpx;
}

/* Responsive */
@media (max-width: 768px) {
    #footer .container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    #footer-decor-text {
        font-size: 16px;
        bottom: 80px;
    }
}


</style>