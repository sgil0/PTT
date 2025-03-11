<?php
date_default_timezone_set("Europe/Paris");
session_start();

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/modele.php"; 


	$addArgs = "";

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
						$addArgs="?view=accueil";
					}	
				}

				
			break;

			case 'Logout' :
				$idUser=valider('idUser','SESSION');
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
			
				case 'maj_email' :


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
							$_SESSION['popup'] = "Mot de passe changé avec succès";
						} else {
							$_SESSION['popup'] = "Merci de saisir des nouveaux mot de passe identiques";
						}
					} else {
						$_SESSION['popup'] = "Mot de passe actuel incorrect";
					}
					// Rediriger vers la page userSettings sans passer le message en GET
					$addArgs = "?view=userSettings#new_email";
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