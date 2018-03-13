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

if(isset($_GET['idrubriques']) && ctype_digit($_GET['idrubriques']))
{

   #on choisi une rubrique
	$tech_pitz = "INNER JOIN photo_has_rubriques h ON p.id = h.photo_id
				  INNER JOIN rubriques r ON r.id = h.rubriques_id 
				  WHERE r.id = ".$_GET['idrubriques'];
}
else
{
	$tech_pitz = "";
}

	
	$sql = "SELECT p.lenom,p.lextension,p.letitre,p.ladesc, u.lelogin
			FROM photo p
			INNER JOIN utilisateur u ON u.id = p.utilisateur_id
			$tech_pitz
			ORDER BY p.id DESC LIMIT 0,20; 
			";
			
	$recup_sql = $bdd->prepare($sql) or die (print_r(errorInfo()));	
	$recup_sql->execute();
	
	$sql="SELECT * FROM rubriques ORDER BY lintitule ASC;";
$recup_section = $bdd->prepare($sql) or die (print_r(errorInfo()));
$recup_section->execute();



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
        <?php echo "<span>Bonjour ";
			echo isset($_SESSION['lenom'])?$_SESSION['lenom']:" Visiteurs !";
			echo" </span>"
		?>
        <?php
           foreach($categories as $key => $value)
		   {
				
				if (isset($_GET['idrubriques']) && $_GET['idrubriques'] == $key)
				{	
					//echo"<li class='active'>$value</li>";
					echo " <h3 class='cat'>Vous êtes dans la section :<span> $value</span></h3>";
				}
				else
				{
					 //header('location: . CHEMIN_RACINE');
				}	
			}
          ?>
          
           <br />
            <?php
            while($ligne = $recup_sql->fetch())
			{
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".".$ligne['lextension']."' target='_blank'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".".$ligne['lextension']."' alt='' /></a>";
                 echo "<p>".$ligne['ladesc']."<br /> <span class='name'>by ".$ligne['lelogin']."</span></p>";
				
				  
                 echo "</div>";
               }                
               ?> 
        </div>
       
        <?php
			include 'includes/footer.php';
		 ?>
         </div>
    </body>
</html>