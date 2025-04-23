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
	  UPDATE utilisateurs
	  SET connecte = 1
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function deconnecterUtilisateur($idUtilisateur)
{
	// cette fonction affecte le booléen "connecte" à faux pour l'utilisateur concerné 
	return SQLUpdate("
	  UPDATE utilisateurs
	  SET connecte = 0
	  WHERE id_utilisateur = '$idUtilisateur';
	");
}

function getMDP($idUtilisateur){
	return SQLGetChamp("
		SELECT mot_de_passe FROM utilisateurs
		WHERE id_utilisateur = '$idUtilisateur';
	");
}

function updateMDP($idUtilisateur,$newMDP){
	return SQLUpdate("
		UPDATE utilisateurs
	    SET mot_de_passe = '$newMDP'
	    WHERE id_utilisateur = '$idUtilisateur';
	");
}

function mkEmailChangeRequest($idUtilisateur, $newEmail, $token, $expiration){
	return SQLInsert("
	INSERT INTO email_change_requests (id_utilisateur, new_email, token, expires_at) 
	VALUES ('$idUtilisateur', '$newEmail', '$token', '$expiration')
	");
}

function getEmailChangeRequest($token){
	return parcoursRs(SQLSelect("
	SELECT * FROM email_change_requests 
	WHERE token = '$token'
    "));
}

function updateMail($idUtilisateur, $newEmail){
	return SQLUpdate("
	UPDATE utilisateurs
	SET email = '$newEmail'
	WHERE id_utilisateur = '$idUtilisateur';
	");
}

function deleteToken($token){
	return SQLUpdate("
	DELETE FROM email_change_requests 
	WHERE token = '$token'
	");
}

function ajouterRendezVous($id_utilisateur, $date_heure, $description = "")
{
    // Table "rendez_vous" et colonnes : id_utilisateur, date_heure, description, statut
    // statut a une valeur par défaut ('confirmé'), donc pas nécessaire de l'insérer si on veut la valeur par défaut

    // SQLInsert(...) : votre fonction d'exécution (maLibSQL.pdo.php) qui renvoie l'ID inséré ou false en cas d'erreur
    return SQLInsert("
	  INSERT INTO rendez_vous(id_utilisateur, date_heure, description)
	  VALUES ('$id_utilisateur', '$date_heure', '$description');
	");
}

function getRendezVousByDate($date) {
    // On récupère uniquement l'heure au format HH:MM pour les rendez-vous de la date donnée
    $sql = "SELECT DATE_FORMAT(date_heure, '%H:%i') as time 
            FROM rendez_vous 
            WHERE DATE(date_heure) = '$date'";
    // SQLSelect() retourne un tableau de lignes (tableau associatif)
    return SQLSelect($sql);
}

function getActualites() {
    // Récupère toutes les actualités et les infos sur l'auteur via une jointure
    $sql = "SELECT a.*, u.nom AS nom_auteur, u.prenom AS prenom_auteur 
            FROM actualites a
            LEFT JOIN utilisateurs u ON a.id_auteur = u.id_utilisateur
            ORDER BY a.date_publication DESC";
    return SQLSelect($sql);
}


function isUserAdmin($idUser) {
    // Prépare la requête avec un paramètre pour éviter l'injection SQL
    $sql = "SELECT role FROM utilisateurs WHERE id_utilisateur = $idUser";

    // Récupère la valeur du champ "role" pour cet utilisateur
    $role = SQLGetChamp($sql);

    // Compare avec la chaîne "administrateur" (attention à l'orthographe)
    return ($role === "administrateur");
}

function getActuById($idActu) {
    // Prépare la requête avec un paramètre pour éviter l'injection SQL
    $actu = SQLSelect("
	SELECT * FROM actualites 
	WHERE id_actualite = $idActu
	");
	if ($actu && count($actu) > 0) {
        return $actu[0]; // On renvoie la première (et unique) ligne sous forme de tableau associatif
    }
	return false;
}

function updateActu($id, $titre, $contenu) {
    return SQLUpdate("
	UPDATE actualites
	SET titre = '$titre', contenu = '$contenu'
	WHERE id_actualite = '$id';
	");
}

function deleteActu($idActu) {
	return SQLUpdate("
	DELETE FROM actualites
	WHERE id_actualite = '$idActu'
	");
}

function createActu($titre, $contenu, $date_publication, $image_actu, $id_auteur) {
    $sql = "INSERT INTO actualites (titre, contenu, date_publication, image_actu, id_auteur)
            VALUES (?, ?, ?, ?, ?)";

    // On passe les valeurs dans un tableau
    return SQLExec($sql, array($titre, $contenu, $date_publication, $image_actu, $id_auteur));
}

function getUserById($idUser) {
    // Prépare la requête avec un paramètre pour éviter l'injection SQL
    $user = SQLSelect("
	SELECT prenom, nom FROM utilisateurs
	WHERE id_utilisateur = $idUser
	");
	return $user;
}

function createActivite($titre, $contenu, $type_utilisateur)
{
    return SQLInsert("
        INSERT INTO activites (titre, contenu, type_utilisateur)
        VALUES ('$titre', '$contenu', '$type_utilisateur')
    ");
}

function getActivitesByType($type_utilisateur)
{
    return SQLSelect("
        SELECT * FROM activites
        WHERE type_utilisateur = '$type_utilisateur'
        ORDER BY date_creation DESC
    ");
}

function getDossiersByUser($idUser)
{
	return SQLSelect("
		SELECT id_dossier, etat, date_creation 
        FROM dossiers 
        WHERE id_utilisateur = $idUser
	");
}
?>
