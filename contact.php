<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/connect.php';
require_once 'includes/fonctions.php';

if(isset($_POST['lelogin']) && isset($_POST['lepass']))
{
	//définition des variables local
	$monlogin = traite_chaine($_POST['lelogin']);
	$monpswd = traite_chaine($_POST['lepass']);
	
	//Vérification dans la table administrator si le login et mot de pass correspondent à un administrator...
	$req_admin = "SELECT  u.id, u.lemail, u.lenom, 
		         d.lenom AS nom_perm, d.laperm 
	             FROM utilisateur u
		         INNER JOIN droit d ON u.droit_id = d.id
                 WHERE u.lelogin='$monlogin' AND u.lepass = '$monpswd';";
	$recup_admin = $bdd->query($req_admin) or die (print_r($bdd->erroInfo()));
	
	if($recup_admin->rowCount())
	{
		$recup_tab = $recup_admin->fetch();
		$_SESSION = $recup_tab;
		
		#creation des variables de session
		$_SESSION['sid'] = session_id();
		
		#Variables venant de l'administrator
        $_SESSION['lelogin'] = $monlogin;
        header('location: ' . CHEMIN_RACINE);
	}
	else
	{
		echo '<body onLoad="alert(\'Mauvais login ou le mot de passe incorrect...\')">';
		// puis on le redirige vers la page d'accueil
		echo '<meta http-equiv="refresh" content="0;URL=index.php">';
		$error_connect = "Mauvais login ou le mot de passe incorrect";
	}
}

//verifier si le formulaire à bien été envoyé
if(isset($_POST['nom']) && isset($_POST['titre']) && isset($_POST['sujet']) && isset($_POST['couriel']) && isset($_POST['letexte']))
{
	$nom = strip_tags(trim($_POST['nom']));
	$titre = strip_tags(trim($_POST['titre']));
	$subject = strip_tags(trim($_POST['sujet']));
	$couriel = strip_tags(trim($_POST['couriel']));
	$message = strip_tags(trim($_POST['letexte']));
	
	//si tout va bien, on envoie le mail
	
	 $to      = 'sallartistee@yahoo.fr';
     $headers = 'From: '. $couriel. "\r\n" .
     'Reply-To: ' .$couriel. "\r\n" .
     'X-Mailer: PHP/' . phpversion();

     mail($to, $subject, $titre. $nom ." : ". $message, $headers);
    
    $affiche = "Cool, Votre message a bien été envoyé, nous vous contacterons soon!!!";
}

?>

<!doctype html>
<html>
    <?php
      include "includes/head.php";
    ?>

    <body>
       <div class="wrap">
         <?php include 'includes/header.php'; ?>
        <div class="content">
            <h2>Contactez-nous</h2>
               
               <div id="formulaire">
            <h5>UN PETIT MOT ?</h5>
            <p>
                Vous pouvez utiliser le formulaire de contact ci-dessous pour toute demande. 
            </p><br />
            <?php
                if(isset($affiche))
                {
                   echo $affiche;
                }else{
			?> 
            
            <form action="" method="post">
                <input type="text" name="nom" placeholder="Votre Nom" required />
                <input type="text" name="prenom" placeholder="Votre Prénom" />
                <input type="radio" name="titre" value="Mme" id="mme" required /><label for="mme">Mme</label>
                <input type="radio" name="titre" value="Mme" id="mme" /><label for="mme">Mme</label>
                <input type="radio" name="titre" value="M" id="mon" /><label for="mon">M</label>
                <input type="email" name="couriel" placeholder="Entrez votre e-mail" required /><br/><br />
                <input type="text" name="sujet" placeholder="Sujet de votre mail" required /><br/><br />
                <textarea maxlength="500" placeholder="Entrez votre message" name="letexte" required rows="6" cols="90" ></textarea><br>
                <input type="submit" value="ENVOYEZ" >
            </form>
            <?php
            }
			?>

        </div><!--fin #formulaire-->
              
        </div>
       
        <?php
			include 'includes/footer.php';
		 ?>
         </div>
    </body>
</html>