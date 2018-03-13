<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/connect.php';
require_once 'includes/fonctions.php';

#preparation de la pagination
   $recup_nb_photo = "SELECT COUNT(*) nb FROM photo";
   $tot = $bdd->query($recup_nb_photo);
   $maligne = $tot->fetch();
	
   $nb_total = $maligne['nb'];
	
   //verification de la pagination
	
   if(isset($_GET[$get_pagination]) && ctype_digit($_GET[$get_pagination]))
    {
	$page_actu = $_GET[$get_pagination];
	}
	else
	{
	$page_actu = 1;
	}
   //creation de la variable de debut a mettre dans la limite
  $debut = ($page_actu-1)*$elements_par_page;
  
  #Initialisation de la pagination
  $get_pagination = "pg"; 
  $pagina = pagination($nb_total, $page_actu, $elements_par_page, $get_pagination);
  
//*******fin pagination*********//

# si on est pas (ou plus) connecté
if(!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) 
{
    header("location: deconnect.php");
}


// si on confirme la suppression
if(isset($_GET['delete'])&& ctype_digit($_GET['delete']))
{
    $idphoto = $_GET['delete'];
    $idutil = $_SESSION['id'];
    
    // récupération du nom de la photo
    $sql_10="SELECT lenom, lextension FROM photo WHERE id=$idphoto;";
	$sql1 = $bdd->prepare($sql_10) or die (print_r($bdd->erroInfo()));
	$sql1->execute();
	$nom_photo = $sql1->fetch();
	
    #suppression de la clef etrangere
    $sql_20="DELETE FROM photo_has_rubriques WHERE photo_id = $idphoto;";
	$sql2 = $bdd->prepare($sql_20) or die (print_r(errorInfo()));
	$sql2->execute();
	
	// puis suppression dans la table photo
    $sql_30="DELETE FROM photo WHERE id = $idphoto ;";
	$sql3 = $bdd->prepare($sql_30) or die (print_r(errorInfo()));
	$sql3->execute();
	
	#echo $dossier_ori.$nom_photo['lenom'].".".$nom_photo['lextension'];
    
    // supression physique des fichiers
    unlink($dossier_ori.$nom_photo['lenom'].".".$nom_photo['lextension']);
    unlink($dossier_gd.$nom_photo['lenom'].".jpg");
    unlink($dossier_mini.$nom_photo['lenom'].".jpg");
}

#recuperation des images de l'utilisateurs
$sql = "SELECT p.*, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
        GROUP BY p.id
        ORDER BY p.id DESC LIMIT $debut,$elements_par_page;
    ";
$recup_sql = $bdd->query($sql) or die (print_r($bdd->erroInfo()));

// récupération de toutes les rubriques pour le formulaire d'insertion
$sql="SELECT * FROM rubriques ORDER BY lintitule ASC;";
$recup_section = $bdd->prepare($sql) or die (print_r(errorInfo()));
$recup_section->execute();


?>
<!DOCTYPE html>
<html>
    <?php
      include "includes/head.php";
    ?>
    <body>
         <div class="wrap">
             <?php include 'includes/header.php'; ?>
          
             <div class="content">
                 <h3>Bonjour Super Administrator</h3>
                  
                 <div class="pagina"><?= $pagina ?></div><!--Affichage de Pagination-->
                 
                 <div id="lesphotos">
                     <?php
                     while($ligne = $recup_sql->fetch()){
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".".$ligne['lextension']."' target='_blank'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".jpg' alt='' /></a>";
                 echo "<p>".$ligne['ladesc']."<br /><br />
				 
                 <a href='modif.php?id=".$ligne['id']."'><img src='img/modifier.png' alt='modifier' /></a> <img onclick='supprime(".$ligne['id'].");' src='img/supprimer.png' alt='supprimer' />
                     </p>";
					 $sections = explode('|||',$ligne['lintitule']);
                 //$idsections = explode(',',$ligne['idrub']);
                 foreach($sections AS $key => $valeur){
                     echo " $valeur<br/>";
                 }
                 echo "</div>";
               }
               ?>
                 </div>
             </div>
            <div id="bas"></div>
         
         <?php
			include 'includes/footer.php';
		 ?>
         </div>
    </body>
</html>
