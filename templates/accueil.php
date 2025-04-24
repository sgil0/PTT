

<style>
@import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
    height: 100%;
    font-family: Arial, sans-serif;
    overflow-x: hidden;
}

.hero {
    position: relative;
    min-height: 100vh;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}

/* Image de fond floutée et atténuée */
.hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: url('ressources/raw.png') center/cover no-repeat;
    filter: brightness(0.7);
    opacity: 0.3;
    z-index: 1;
}

/* Contenu principal */
.overlay-content {
    position: relative;
    z-index: 2;
    padding: 30px;
}

html,body  
{  
overflow:hidden;  
} 

.overlay-content img {
    height: 80px;
    margin-bottom: 30px;
}

.cta {
    display: inline-block;
    background-color: #ffc107;
    color: #d94100;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    margin-top: 10px;
    transition: background-color 0.3s;
}

.cta:hover {
    background-color: #e0a800;
}

.tagline {
    font-family: 'Pacifico', cursive;
    font-size: 28px;
    color: #d94100;
    margin-top: 30px;
}

.footer-text {
    margin-top: 40px;
    color: #d94100;
    font-size: 16px;
}
</style>

<div class="hero">
    <div class="overlay-content">
        <img src="ressources/logoPinf.png" alt="Logo Elpis">
        <a href="simulateur.php" class="cta">Je calcule mes aides</a>
        <div class="tagline">Vos démarches administratives en toute sérénité</div>
        <div class="footer-text">
            Depuis le 26/09/2023,<br>
            <strong>Elpis</strong> s’occupe de toutes vos démarches administratives.
        </div>
    </div>
</div>
