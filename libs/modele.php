<?php


// inclure ici la librairie faciliant les requêtes SQL
include_once("maLibSQL.pdo.php");


function listerUtilisateurs($classe = "both")
{
	// NB : la présence du symbole '=' indique la valeur par défaut du paramètre s'il n'est pas fourni
	// Cette fonction liste les utilisateurs de la base de données 
	// et renvoie un tableau d'enregistrements. 
	// Chaque enregistrement est un tableau associatif contenant les champs 
	// id,pseudo,blacklist,connecte,couleur

	// Lorsque la variable $classe vaut "both", elle renvoie tous les utilisateurs
	// Lorsqu'elle vaut "bl", elle ne renvoie que les utilisateurs blacklistés
	// Lorsqu'elle vaut "nbl", elle ne renvoie que les utilisateurs non blacklistés

	$SQL = "select * from users";
	if ($classe == "bl")
		$SQL .= " where blacklist=1";
	if ($classe == "nbl")
		$SQL .= " where blacklist=0";
	
	// echo $SQL;
	return parcoursRs(SQLSelect($SQL));
}


function interdireUtilisateur($idUser)
{
	// cette fonction affecte le booléen "blacklist" à vrai
	$SQL = "UPDATE users SET blacklist=1 WHERE id='$idUser'";
	// les apostrophes font partie de la sécurité !! 
	// Il faut utiliser addslashes lors de la récupération 
	// des données depuis les formulaires

	SQLUpdate($SQL);
}

function autoriserUtilisateur($idUser)
{
	// cette fonction affecte le booléen "blacklist" à faux 
	$SQL = "UPDATE users SET blacklist=0 WHERE id='$idUser'";
	SQLUpdate($SQL);
}

function verifUserBdd($login,$passe)
{
	// Vérifie l'identité d'un utilisateur 
	// dont les identifiants sont passes en paramètre
	// renvoie faux si user inconnu
	// renvoie l'id de l'utilisateur si succès

	$SQL="SELECT id FROM users WHERE pseudo='$login' AND passe='$passe'";

	return SQLGetChamp($SQL);
	// si on avait besoin de plus d'un champ
	// on aurait du utiliser SQLSelect
}

/********* PARTIE 2 *********/

function isAdmin($idUser)
{
	// vérifie si l'utilisateur est un administrateur
	$SQL ="SELECT admin FROM users WHERE id='$idUser'";
	return SQLGetChamp($SQL); 
}



function mkUser($pseudo, $passe,$admin=false,$couleur="black")
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	$intAdmin = $admin ? 1 : 0;
	return SQLInsert("
	  INSERT INTO users(pseudo, passe, admin, couleur)
	  VALUES ('$pseudo', '$passe', $intAdmin, '$couleur');
	");
}

function rmUser($idUser)
{
	// Cette fonction crée un nouvel utilisateur et renvoie l'identifiant de l'utilisateur créé
	return SQLUpdate("
	  DELETE FROM users WHERE id='$idUser';
	");
}

function connecterUtilisateur($idUser)
{
	// cette fonction affecte le booléen "connecte" à vrai pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE users
	  SET connecte = 1
	  WHERE id = '$idUser';
	");
}

function deconnecterUtilisateur($idUser)
{
	// cette fonction affecte le booléen "connecte" à faux pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE users
	  SET connecte = 0
	  WHERE id = '$idUser';
	");
}

function changerCouleur($idUser,$couleur="black")
{
	// cette fonction modifie la valeur du champ 'couleur' de l'utilisateur concerné
	$sql = "
	  UPDATE users
	  SET couleur = '$couleur'
	  WHERE id = '$idUser';
	";
	return SQLUpdate($sql);
}

function changerPasse($idUser,$passe)
{
	// cette fonction modifie le mot de passe d'un utilisateur
	return SQLUpdate("
	  UPDATE users
	  SET passe = '$passe'
	  WHERE id = '$idUser';
	");
}

function changerPseudo($idUser,$pseudo)
{
	// cette fonction modifie le pseudo d'un utilisateur
	return SQLUpdate("
	  UPDATE users
	  SET pseudo = '$pseudo'
	  WHERE id = '$idUser';
	");
}

function promouvoirAdmin($idUser)
{
	// cette fonction fait de l'utilisateur un administrateur
	return SQLUpdate("
	  UPDATE users
	  SET admin = 1
	  WHERE id = '$idUser';
	");
}

function retrograderUser($idUser)
{
	// cette fonction fait de l'utilisateur un simple mortel
	return SQLUpdate("
	  UPDATE users
	  SET admin = 0
	  WHERE id = '$idUser';
	");
}


/********* PARTIE 3 *********/

function listerUtilisateursConnectes()
{
	// Liste les utilisteurs connectes
	return parcoursRs(SQLSelect("
	  SELECT * FROM users WHERE connecte;
	"));
}

function listerConversations($mode="tout")
{
	// Liste toutes les conversations ($mode="tout")
	// OU uniquement celles actives  ($mode="actives"), ou inactives  ($mode="inactives")
	$sqlMode = "";
	if ($mode == "actives") {
	  $sqlMode = "WHERE active";
  }
	if ($mode == "inactives") {
	  $sqlMode = "WHERE NOT active";
  }
	return parcoursRs(SQLSelect("
	  SELECT * FROM conversations $sqlMode;
	"));
}

function archiverConversation($idConversation)
{
	// rend une conversation inactive
	return SQLUpdate("
	  UPDATE conversations
	  SET active = 0
	  WHERE id = '$idConversation';
	");
}

function creerConversation($theme)
{
	// crée une nouvelle conversation et renvoie son identifiant
	SQLInsert("
	  INSERT INTO conversations(theme)
	  VALUES ('$theme');
	");
	return SQLGetChamp("
	  SELECT MAX(id)
	  FROM conversations;
	");
}

function reactiverConversation($idConversation)
{	
	// rend une conversation active
	return SQLUpdate("
	  UPDATE conversations
	  SET active = 1
	  WHERE id = '$idConversation';
	");
}

function supprimerConversation($idConv)
{
	// supprime une conversation et ses messages

	// NB : on aurait pu aussi demander à mysql de supprimer automatiquement
	// les messages lorsqu'une conversation est supprimée, 
	// en déclarant idConversation comme clé étrangère vers le champ id de la table 
	// des conversations et en définissant un trigger
	SQLDelete("
	  DELETE FROM message
	  WHERE idConversation = '$idConv';
	");
	return SQLDelete("
	  DELETE FROM conversations
	  WHERE id = '$idConv';
	");
}


function enregistrerMessage($idConversation, $idAuteur, $contenu)
{
	// Enregistre un message dans la base en encodant les caractères spéciaux HTML : <, > et & pour interdire les messages HTML
	$contenu = htmlspecialchars($contenu);
	return SQLInsert("
	  INSERT INTO message(idConversation, idAuteur, contenu)
	  VALUES('$idConversation', '$idAuteur', '$contenu');
	");
}

function listerMessages($idConv,$format="asso")
{
	// Liste les messages de cette conversation, au format JSON ou tableau associatif
	// Champs à extraire : contenu, auteur, couleur 
	// en ne renvoyant pas les utilisateurs blacklistés
	$sql = "
	  SELECT
	    message.contenu AS contenu,
	    users.pseudo AS auteur,
	    users.couleur AS couleur
	  FROM message
	  JOIN users ON message.idAuteur = users.id
	  WHERE message.idConversation = '$idConv'
	    AND NOT users.blacklist;
  ";
	$res = parcoursRs(SQLSelect($sql));
	if (strtoupper($format) == "JSON") {
	  return JSON_encode($res);
	} else {
	  return $res;
	}
}

function listerMessagesFromIndex($idConv,$index)
{
	// Liste les messages de cette conversation, 
	// dont l'id est superieur à l'identifiant passé
	// Champs à extraire : contenu, auteur, couleur 
	// en ne renvoyant pas les utilisateurs blacklistés
	$sql = "
	  SELECT
	    message.contenu AS contenu,
	    users.pseudo AS auteur,
	    users.couleur AS couleur
	  FROM message
	  JOIN users ON message.idAuteur = users.id
	  WHERE message.idConversation = '$idConv'
	    AND message.id > '$index'
	    AND NOT users.blacklist;
  ";
	return parcoursRs(SQLSelect($sql));
}

function getConversation($idConv)
{	
	// Récupère les données de la conversation (theme, active)
	return parcoursRs(SQLSelect("
	  SELECT theme, active
	  FROM conversations
	  WHERE id = '$idConv';
	"));
}

?>
