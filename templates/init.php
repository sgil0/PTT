<?php
include_once(__DIR__ . '/../libs/maLibUtils.php');
include_once(__DIR__ . '/../libs/maLibBootstrap.php');
include_once(__DIR__ . '/../libs/modele.php');

// Redirection si la page est appelée directement
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
    header("Location:../index.php");
    exit;
}
