<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/connect.php';
require_once 'includes/fonctions.php';

#preparation de la pagination
   $recup_nb_photo = "SELECT COUNT(*) nb FROM photo WHERE utilisateur_id =". $_SESSION['id'];
   $tot = $bdd->query($recup_nb_photo);
   $maligne = $tot->fetch();
	
   $nb_total = $maligne['nb'];
	
   //verification de la pagination
	
   if(isset($_GET[$get_pagination_membre]) && ctype_digit($_GET[$get_pagination_membre]))
    {
	$page_actu = $_GET[$get_pagination_membre];
	}
	else
	{
	$page_actu = 1;
	}
   //creation de la variable de debut a mettre dans la limite
  $debut = ($page_actu-1)*$elements_par_page_membre;
  
  #Initialisation de la pagination
  $$get_pagination_membre = "pag"; 
  $pagina = pagination($nb_total, $page_actu, $elements_par_page_membre, $get_pagination_membre);
  
//*******fin pagination*********//

# si on est pas (ou plus) connecté
if(!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    header("location: deconnect.php");
}

# si on a envoyé le formulaire et qu'un fichier est bien attaché
if(isset($_POST['letitre'])&&isset($_FILES['lefichier'])){
    
    $letitre = traite_chaine($_POST['letitre']);
    $ladesc = traite_chaine($_POST['ladesc']);
    
    # récupération des paramètres du fichier uploadé
    $limage = $_FILES['lefichier'];

    # appel de la fonction d'envoi de l'image
    $upload = upload_originales($limage,$dossier_ori,$formats_acceptes);
    
    // si $upload n'est pas un tableau c'est qu'on a une erreur
    if(!is_array($upload)){
        // on affiche l'erreur
        echo $upload;
        
    // si on a pas d'erreur, 
    }else{
        
        // création de la grande image qui garde les proportions
        $gd_ok = creation_img($dossier_ori, $upload['nom'],$upload['extension'],$dossier_gd,$grande_large,$grande_haute,$grande_qualite);
        
        // création de la miniature centrée et coupée
        $min_ok = creation_img($dossier_ori, $upload['nom'],$upload['extension'],$dossier_mini,$mini_large,$mini_haute,$mini_qualite,false);
        
        // si la création des 2 images sont effectuées
        if($gd_ok==true && $min_ok==true){
            
          
			 $sql= "INSERT INTO photo (lenom,lextension,lepoids,lalargeur,lahauteur,letitre,ladesc,utilisateur_id) 
			 VALUES (?,?,?,?,?,?,?,?);";
			 $lespics = $bdd->prepare($sql) or die (print_r(errorInfo()));
			 $lesphotos = $lespics->execute(array($upload['nom'],$upload['extension'],$upload['poids'],$upload['largeur'],$upload['hauteur'],$letitre,$ladesc,$_SESSION['id']));
			 
			 $id_photo = $bdd->lastInsertId();
			 
			 if(isset($_POST['section'])){
            foreach($_POST['section'] AS $clef => $valeur){
                if(ctype_digit($valeur))
				{
                    $bdd->query("INSERT INTO photo_has_rubriques VALUES ($id_photo,$valeur);")or die(print_r(errorInfo()));
                }
            }
            }
            
        }else{
            echo 'Erreur lors de la création des images redimenssionnées';
        }
        
    }    
}

// si on confirme la suppression
if(isset($_GET['delete'])&& ctype_digit($_GET['delete'])){
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
    $sql_30="DELETE FROM photo WHERE id = $idphoto AND utilisateur_id = $idutil;";
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
        WHERE p.utilisateur_id = ".$_SESSION['id']."
        GROUP BY p.id
        ORDER BY p.id DESC LIMIT $debut,$elements_par_page_membre;
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
                 Vous êtes connecté en tant que <span><?php echo $_SESSION['nom_perm']?></span><br /><br />
                 <h2>Vous dans l'espace membre</h3>
                 
                 <div id="formulaire">
                <form action="" enctype="multipart/form-data" method="POST" name="onposte">
                    <input type="text" name="letitre" required /><br/>
                   <!-- <input type="hidden" name="MAX_FILE_SIZE" value="50000000" /> -->
                    <input type="file" name="lefichier" required /><br/>
                  
                    <textarea name="ladesc"></textarea><br/>
                    
                     <?php
                    // affichage des sections
                    while($ligne = $recup_section->fetch())
					{
                        echo $ligne['lintitule']." : <input type='checkbox' name='section[]' value='".$ligne['id']."' > - ";
                    }
                    ?><br/>
                    <input type="submit" value="Envoyer le fichier" />
                </form>
            </div>
                 
                 <div class="pagina"><?= $pagina ?></div><!--Affichage de Pagination-->
                 
                 <div id="lesphotos">
                     <?php
                     while($ligne = $recup_sql->fetch()){
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".".$ligne['lextension']."' target='_blank'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".jpg' alt='' /></a>";
                 echo "<p><span>".$ligne['ladesc']."</span><br /><br />
				 
                 <a href='modif.php?id=".$ligne['id']."'><img src='img/modifier.png' alt='modifier' /></a> <img onclick='supprime(".$ligne['id'].");' src='img/supprimer.png' alt='supprimer' />
                     </p>";
					 $sections = explode('|||',$ligne['lintitule']);
                 //$idsections = explode(',',$ligne['idrub']);
                 foreach($sections AS $key => $valeur){
                     echo " $valeur<br/>";
                 }
			     echo"<span class='name'>By ".$_SESSION['lenom']."</span>";
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
