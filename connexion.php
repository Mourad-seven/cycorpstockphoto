<?php
require_once("includes/connect.php");

if(isset($_POST['lelogin']) && isset($_POST['lepass']))
{
	//définition des variables local
	$monlogin = htmlspecialchars(strip_tags(trim($_POST['lelogin'])),ENT_QUOTES);
	$monpswd = htmlspecialchars(strip_tags(trim($_POST['lepass'])),ENT_QUOTES);
	
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
		
		#creation des variables de session
		$_SESSION['id_session'] = session_id();
		
		#Variables venant de l'administrator
		$_SESSION['lid'] = $recup_tab['id'];
        $_SESSION['lelogin'] = $recup_tab['lelogin'];
        $_SESSION['lenom'] = $recup_tab['lenom'];
        $_SESSION['lemail'] = $recup_tab['lemail'];
		$_SESSION['levalide'] = $recup_tab['valide'];
		$_SESSION['ledroit'] = $recup_tab['droit_id'];       
		#Redirection vers l'admin                                                                                                      
        echo'<script>document.location.replace("index.php")</script>';
	}
	else
	{
		echo '<body onLoad="alert(\'Mauvais login ou le mot de passe incorrect...\')">';
		// puis on le redirige vers la page d'accueil
		echo '<meta http-equiv="refresh" content="0;URL=index.php">';
		$error_connect = "Mauvais login ou le mot de passe incorrect";
	}
	
	
}

//vérification de la session et de sa validation
if(isset($_SESSION['clef']) && $_SESSION['clef'] == session_id())
{
	$connexion_valide = 1;
}
?>

<!doctype html>
<html>
<?php
   include "includes/head.php";
?>

    <body>
        <div class="wrap">
            <h4>S'IDENTIFIER</h4>

            <div class="flick-form_grid">
                <form method="post" action="" name="newletter">
                    <input type="text" class="textbox" placeholder="Votre Login" name="lelogin" required>
                    <input type="text" class="textbox" placeholder="Mot de passe" name="lepass" required >
                    <div class="smt">
                        <input type="submit" value="S'identifier">
                    </div>
                    <?php
                    //Si il y a une erreur lors de loggage
                    if (isset($error_connect)) {
                        echo $error_connect;
                    }
                    ?>
                </form>
            </div>


        </div>
    </body>
</html>