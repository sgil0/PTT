<?php
date_default_timezone_set("Europe/Paris");
session_start();


	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/modele.php"; 

	$addArgs = "";

	global $dbh; // Je déclare $dbh comme variable globale pour pouvoir l'utiliser dans les fonctions


	if ($action = valider("action"))
	{
		ob_start ();
		echo "Action = '$action' <br />";
		// ATTENTION : le codage des caractères peut poser PB si on utilise des actions comportant des accents... 
		// A EVITER si on ne maitrise pas ce type de problématiques

		/* TODO: A REVOIR !!
		// Dans tous les cas, il faut etre logue... 
		// Sauf si on veut se connecter (action == Connexion)

		if ($action != "Connexion") 
			securiser("login");
		*/

		// Un paramètre action a été soumis, on fait le boulot...
		switch($action)
		{
			
			
			// Connexion //////////////////////////////////////////////////
			case 'Connexion' :
				// On verifie la presence des champs login et passe
				if ($login = valider("login"))
				if ($passe = valider("passe"))
				{
					// On verifie l'utilisateur, 
					// et on crée des variables de session si tout est OK
					// Cf. maLibSecurisation
					if (verifUser($login,$passe)) {
						// tout s'est bien passé, doit-on se souvenir de la personne ? 
						if (valider("remember")) {
							setcookie("login",$login , time()+60*60*24*30);
							setcookie("passe",$password, time()+60*60*24*30);
							setcookie("remember",true, time()+60*60*24*30);
						} else {
							setcookie("login","", time()-3600);
							setcookie("passe","", time()-3600);
							setcookie("remember",false, time()-3600);
						}
						$idUser=valider('idUser','SESSION');
						connecterUtilisateur($idUser);
						$addArgs="?view=accueil";
					}	
				}

				
			break;

			case 'Logout' :
				$idUser=valider('idUser','SESSION');
				deconnecterUtilisateur($idUser);
				session_destroy();
			break;

			case 'creer compte' : 
				if ($email = valider("email"))
				if ($passe = valider("passe"))
				if ($nom = valider("nom"))
				if ($prenom = valider("prenom"))
				if ($type = valider("type"))
				{
					if (!(verifUserBdd($email,$passe))){
						mkUser($prenom,$nom,$email,$passe,$type);
						$addArgs="?view=login";
					} else {
						$addArgs="?view=register";
					}
					
				}
				break;

				case 'confirmer_rdv':
					$id_utilisateur = valider('idUser','SESSION');
					$selectedDate   = valider('day');
					$selectedTime   = valider('selectedTime');
					$description = valider('description');
				
					if ($id_utilisateur && $selectedDate && $selectedTime) {
						$date_heure = date("Y-m-d H:i:s", strtotime("$selectedDate $selectedTime"));
						if (ajouterRendezVous($id_utilisateur, $date_heure, $description)) {
							$addArgs = "?view=planning&confirmation=ok";
						} else {
							$addArgs = "?view=planning&error=insert";
						}
					} else {
						$addArgs = "?view=planning&error=missing_data";
					}
				break;
				
			
				case 'maj_email':
					// Traitement du formulaire de changement d'email
					if (isset($_POST['maj_email_submit'])) {
						if (!isset($_SESSION['idUser'])) {
							$_SESSION['popupEmail'] = "Vous devez être connecté pour changer votre email.";
							die();
						}
						$userId = valider('idUser','SESSION');
						$currentPassword = valider('current_password2');
						$newEmail = filter_var($_POST['new_email'], FILTER_VALIDATE_EMAIL);
						
						if (!$newEmail) {
							$_SESSION['popupEmail'] = "L'email saisi n'est pas valide.";
							die();
						}
						
						// Vérification du mot de passe de l'utilisateur
						$storedMDP = getMDP($userId);
						if ($storedMDP != $currentPassword) {
							$_SESSION['popupEmail'] = "Mot de passe incorrect.";
							die();
						}
						
						// Génération d'un token sécurisé et définition de son expiration (24h)
						$token = bin2hex(random_bytes(32));
						$expiration = date("Y-m-d H:i:s", strtotime('+24 hours'));
						
						// Insertion de la demande dans la table dédiée
						mkEmailChangeRequest($userId, $newEmail, $token, $expiration);
						
						// Envoi de l'email de confirmation
						$confirmationLink = "https://localhost/controleur.php?action=maj_email&token=" . $token;
						$subject = "Confirmez votre changement d'email";
						$message = "Bonjour,\n\nPour confirmer le changement de votre adresse email, cliquez sur le lien ci-dessous :\n" 
									. $confirmationLink 
									. "\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez cet email.";
						mail($newEmail, $subject, $message);
						
						$_SESSION['popupEmail'] = "Un email de confirmation vous a été envoyé à " . htmlspecialchars($newEmail) . ".";
					}
					// Traitement de la confirmation via le token
					elseif (isset($_GET['token'])) {
						$token = $_GET['token'];
						$request = getEmailChangeRequest($token);

						
						if (!$request) {
							die("Lien de confirmation invalide.");
						}
						
						if (strtotime($request['expires_at']) < time()) {
							die("Le lien de confirmation a expiré.");
						}
						
						// Mise à jour de l'email dans la table users
						updateMDP($request['id_utilisateur'], $request['new_email']);
						
						// Invalider le token
						deleteToken($token);

						$_SESSION['popupEmail'] = "Votre adresse email a été mise à jour avec succès.";
					}
					else {
						$_SESSION['popupEmail'] = "Aucune demande valide.";
					}
					$addArgs = "?view=userSettings";
					break;
			

				case 'maj_mdp' :
					$idUser = valider('idUser', 'SESSION');
				
					// Récupération des valeurs depuis le formulaire (POST)
					$currentMDP = valider('current_password');
					$storedMDP = getMDP($idUser); // Mot de passe actuel en base de données
				
					$newMdp = valider('new_password');
					$confirmNewMdp = valider('confirm_password');
				
					if ($currentMDP === $storedMDP) {
						if ($newMdp === $confirmNewMdp) {
							updateMDP($idUser, $newMdp);
							$_SESSION['popupMdp'] = "Mot de passe changé avec succès";
						} else {
							$_SESSION['popupMdp'] = "Merci de saisir des nouveaux mot de passe identiques";
						}
					} else {
						$_SESSION['popupMdp'] = "Mot de passe actuel incorrect";
					}
					// Rediriger vers la page userSettings sans passer le message en GET
					$addArgs = "?view=userSettings#new_email";
					break;

				case "saveEditActu":
					// Récupérer les données du formulaire
					$id = $_POST['id'] ?? 0;
					$titre = $_POST['titre'] ?? '';
					$contenu = $_POST['contenu'] ?? '';						
					// ... validation et sécurisation des données
					// Mise à jour de l'actualité dans la BDD via une fonction du modèle
					updateActu($id, $titre, $contenu);


				
				case "addActuProcess":
					$idUser = $_SESSION['idUser'] ?? 0;
        			if (!isUserAdmin($idUser)) {
            			echo "Accès refusé.";
            			exit;
        			}
					// Récupération des données du formulaire
					$titre = $_POST['titre'] ?? '';
					$contenu = $_POST['contenu'] ?? '';						
					// Utiliser la date actuelle pour la publication
					$date_publication = date("Y-m-d");
						
					// Récupérer l'id de l'auteur depuis la session
					$id_auteur = $_SESSION['idUser'] ?? 0;
						
					// Gestion de l'upload de l'image (optionnel)
					$image_actu = "";
					if (!empty($_FILES['image_actu']['tmp_name'])) {
    					$targetDir = "ressources/"; // Dossier de destination
    					// Créer le dossier s'il n'existe pas
    					if (!is_dir($targetDir)) {
        					mkdir($targetDir, 0755, true);
    					}
    					// Récupérer le nom du fichier et générer un nom unique
    					$originalName = basename($_FILES['image_actu']['name']);
    					$uniqueName = uniqid() . "_" . $originalName;
    					$targetFile = $targetDir . $uniqueName;
    
   						// Déplacer le fichier temporaire vers le dossier de destination
    					if (move_uploaded_file($_FILES['image_actu']['tmp_name'], $targetFile)) {
        					$image_actu = $targetFile; // Ce chemin sera stocké en base
    					} else {
        					// En cas d'erreur, vous pouvez gérer le message d'erreur
       					 	echo "Erreur lors du téléchargement de l'image.";
        					exit;
    					}
					}

						
					// Appel à la fonction du modèle pour créer l'actualité
					createActu($titre, $contenu, $date_publication, $image_actu, $id_auteur);

				case 'ajouter_simulation' :
					include_once "libs/maLibSQL.pdo.php";

					$titre = $_POST['titre'];
					$description = $_POST['description'];
				
					// Insérer la simulation en BDD
					$sql = "INSERT INTO simulations (titre, description) VALUES (:titre, :description)";
					$stmt = $dbh->prepare($sql);
					$stmt->execute(['titre' => $titre, 'description' => $description]);
				
					// Redirection après insertion
					header("Location: index.php?view=simulateur");
					exit();
				break;
		}


	}

	// On redirige toujours vers la page index, mais on ne connait pas le répertoire de base
	// On l'extrait donc du chemin du script courant : $_SERVER["PHP_SELF"]
	// Par exemple, si $_SERVER["PHP_SELF"] vaut /chat/data.php, dirname($_SERVER["PHP_SELF"]) contient /chat

	$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";
	// On redirige vers la page index avec les bons arguments

	header("Location:" . $urlBase . $addArgs);

	// On écrit seulement après cette entête
	ob_end_flush();
	
?>