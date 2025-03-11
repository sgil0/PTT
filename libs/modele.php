<?php


// inclure ici la librairie faciliant les requêtes SQL
include_once("maLibSQL.pdo.php");



function verifUserBdd($email,$passe)
{
	// Vérifie l'identité d'un utilisateur 
	// dont les identifiants sont passes en paramètre
	// renvoie faux si utilisateurs inconnu
	// renvoie l'id de l'utilisateur si succès

	$SQL="SELECT id_utilisateur FROM utilisateurs WHERE email='$email' AND mot_de_passe='$passe'";

	return SQLGetChamp($SQL);
	// si on avait besoin de plus d'un champ
	// on aurait du utiliser SQLSelect
}

/********* PARTIE 2 *********/

function isAdmin($idUtilisateur)
{
	// vérifie si l'utilisateur est un administrateur
	$SQL ="SELECT type_utilisateur FROM utilisateurs WHERE id_utilisateur='$idUtilisateur' AND role='administrateur'";
	return SQLGetChamp($SQL); 
}



function mkUser($prenom, $nom, $email, $passe, $type)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	return SQLInsert("
	  INSERT INTO utilisateurs(prenom, nom, email, mot_de_passe, type_utilisateur, role)
	  VALUES ('$prenom', '$nom', '$email', '$passe', '$type', 'utilisateur');
	");
}

function rmUser($idUtilisateur)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	return SQLUpdate("
	  DELETE FROM utilisateurs WHERE id_utilisateur='$idUtilisateur';
	");
}

function changerPasse($idUtilisateur,$passe)
{
	// cette fonction modifie le mot de passe d'un utilisateur
	return SQLUpdate("
	  UPDATE utilisateurs
	  SET mot_de_passe = '$passe'
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function changerEmail($idUtilisateur,$email)
{
	// cette fonction modifie le email d'un utilisateur
	return SQLUpdate("
	  UPDATE utilisateurs
	  SET email = '$email'
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function promouvoirAdmin($idUtilisateur)
{
	// cette fonction fait de l'utilisateur un administrateur
	return SQLUpdate("
	  UPDATE utilisateurs
	  SET role = 'administrateur'
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function retrograderutilisateurs($idUtilisateur)
{
	// cette fonction fait de l'utilisateur un simple mortel
	return SQLUpdate("
	  UPDATE utilisateurs
	  SET role = 'utilisateur'
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function whoIsHe($idUtilisateur){
	// Cette fonction renvoie le type de l'utilisateur
	$SQL ="SELECT type_utilisateur FROM utilisateurs WHERE id_utilisateur='$idUtilisateur'";
	return SQLGetChamp($SQL);
}

function getNom($idUtilisateur){
	// Cette fonction renvoie le nom de l'utilisateur
	$SQL ="SELECT nom FROM utilisateurs WHERE id_utilisateur='$idUtilisateur'";
	return SQLGetChamp($SQL);	
}

function getPrenom($idUtilisateur){
	// Cette fonction renvoie le Prenom de l'utilisateur
	$SQL ="SELECT prenom FROM utilisateurs WHERE id_utilisateur='$idUtilisateur'";
	return SQLGetChamp($SQL);	
}

function getUtilisateur($idUtilisateur){
	return parcoursRs(SQLSelect("
	SELECT * FROM utilisateurs 
	WHERE id_utilisateur='$idUtilisateur'
    "));
}

function connecterUtilisateur($idUtilisateur)
{
	// cette fonction affecte le booléen "connecte" à vrai pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE utilisateur
	  SET connecte = 1
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function deconnecterUtilisateur($idUtilisateur)
{
	// cette fonction affecte le booléen "connecte" à faux pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE utilisateur
	  SET connecte = 0
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function getMDP($idUtilisateur){
	
}
?>
