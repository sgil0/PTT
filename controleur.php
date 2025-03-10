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
				if ($email = valider("login"))
				if ($passe = valider("passe"))
				if ($nom = valider("nom"))
				if ($prenom = valider("prenom"))
				if ($type = valider("type"))
				{	
					if (!(verifUserBdd($email,$passe))){
						mkUser($email,$passe,$nom,$prenom,$type);
						$addArgs="?view=login";
					} else {
						$addArgs="?view=register";
					}
					
				}
				
				case 'confirmer_rdv':
					// Récupère l'identifiant de l'utilisateur depuis la session
					$id_utilisateur = valider("idUser", "SESSION");
					// Récupère la date et l'heure sélectionnées envoyées en POST
					$selectedDate = valider("day");
					$selectedTime = valider("selectedTime");
				
					if ($id_utilisateur && $selectedDate && $selectedTime) {
						 // Combine la date et l'heure pour créer un datetime au format SQL
						 $date_heure = date("Y-m-d H:i:s", strtotime($selectedDate . " " . $selectedTime));
						 
						 // Appel à une fonction modèle pour insérer le rendez-vous dans la BDD
						 if (ajouterRendezVous($id_utilisateur, $date_heure, "")) {
							  // Si insertion réussie, redirige vers une vue de confirmation (à adapter)
							  $addArgs = "?view=confirmation_rdv";
						 } else {
							  // En cas d'erreur d'insertion
							  $addArgs = "?view=planning&error=insert";
						 }
					} else {
						 // Si des données sont manquantes
						 $addArgs = "?view=planning&error=missing_data";
					}
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