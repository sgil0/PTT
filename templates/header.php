<?php

include_once (__DIR__ . '/../libs/maLibUtils.php');
include_once (__DIR__ . '/../libs/maLibBootstrap.php');
include_once (__DIR__ . '/../libs/modele.php');


if ($view != "login" && $view != "register"){
	include "navBar.php";
}


// Si la page est appelée directement par son adresse, on redirige en passant pas la page index
if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php");
	die("");
}

// Pose qq soucis avec certains serveurs...
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";

// $view = valider("view"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!--  H E A D  -->
<head>	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Pinf</title>
	<!-- <link rel="stylesheet" type="text/css" href="css/style.css"> -->

	<!-- Liaisons aux fichiers css de Bootstrap -->
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="css/sticky-footer.css" rel="stylesheet" />
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	

</head>
<!--  F I N  H E A D  -->




<div id="maPage" style="margin-bottom:1000px;">