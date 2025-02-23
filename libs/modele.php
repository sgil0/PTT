<?php


// inclure ici la librairie faciliant les requêtes SQL
include_once("maLibSQL.pdo.php");



function verifUserBdd($email,$passe)
{
	// Vérifie l'identité d'un utilisateur 
	// dont les identifiants sont passes en paramètre
	// renvoie faux si user inconnu
	// renvoie l'id de l'utilisateur si succès

	$SQL="SELECT id FROM user WHERE email='$email' AND passe='$passe'";

	return SQLGetChamp($SQL);
	// si on avait besoin de plus d'un champ
	// on aurait du utiliser SQLSelect
}

/********* PARTIE 2 *********/

function isAdmin($idUser)
{
	// vérifie si l'utilisateur est un administrateur
	$SQL ="SELECT type FROM user WHERE id='$idUser' AND type='Admin'";
	return SQLGetChamp($SQL); 
}



function mkUser($email, $passe, $nom, $prenom, $type)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	return SQLInsert("
	  INSERT INTO user(email, passe, nom, prenom, type, connecte)
	  VALUES ('$email', '$passe', '$nom', '$prenom', '$type', 0);
	");
}

function rmUser($idUser)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	return SQLUpdate("
	  DELETE FROM user WHERE id='$idUser';
	");
}

function connecterUtilisateur($idUser)
{
	// cette fonction affecte le booléen "connecte" à vrai pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE user
	  SET connecte = 1
	  WHERE id = '$idUser';
	");
}

function deconnecterUtilisateur($idUser)
{
	// cette fonction affecte le booléen "connecte" à faux pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE user
	  SET connecte = 0
	  WHERE id = '$idUser';
	");
}

function changerPasse($idUser,$passe)
{
	// cette fonction modifie le mot de passe d'un utilisateur
	return SQLUpdate("
	  UPDATE user
	  SET passe = '$passe'
	  WHERE id = '$idUser';
	");
}

function changerEmail($idUser,$email)
{
	// cette fonction modifie le email d'un utilisateur
	return SQLUpdate("
	  UPDATE user
	  SET pseudo = '$email'
	  WHERE id = '$idUser';
	");
}

function promouvoirAdmin($idUser)
{
	// cette fonction fait de l'utilisateur un administrateur
	return SQLUpdate("
	  UPDATE user
	  SET admin = 1
	  WHERE id = '$idUser';
	");
}

function retrograderUser($idUser)
{
	// cette fonction fait de l'utilisateur un simple mortel
	return SQLUpdate("
	  UPDATE user
	  SET admin = 0
	  WHERE id = '$idUser';
	");
}

function whoIsHe($idUser){
	// Cette fonction renvoie le type de l'utilisateur
	$SQL ="SELECT type FROM user WHERE id='$idUser'";
	return SQLGetChamp($SQL);
}

function getNom($idUser){
	// Cette fonction renvoie le nom de l'utilisateur
	$SQL ="SELECT nom FROM user WHERE id='$idUser'";
	return SQLGetChamp($SQL);	
}

function getPrenom($idUser){
	// Cette fonction renvoie le Prenom de l'utilisateur
	$SQL ="SELECT Prenom FROM user WHERE id='$idUser'";
	return SQLGetChamp($SQL);	
}

?>
