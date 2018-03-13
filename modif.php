<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/connect.php';
require_once 'includes/fonctions.php';

// si on est pas (ou plus) connecté
if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    header("location: deconnect.php");
}

// si il existe un id de type get et qu'il est numérique
if(isset($_GET['id'])&&  ctype_digit($_GET['id'])){
    $idphoto = $_GET['id'];
}else{
   header("location: membre.php");
}

// si on a envoyé le formulaire et qu'un fichier est bien attaché
if(isset($_POST['letitre'])){
    
    // traitement des chaines de caractères
    $letitre = traite_chaine($_POST['letitre']);
    $ladesc = traite_chaine($_POST['ladesc']);
    
    // mise à jour du titre et du texte
	$rek = $bdd->prepare("UPDATE photo SET letitre=?, ladesc=? WHERE id=?") or die (print_r(errorInfo()));
	$rek->execute(array($letitre, $ladesc, $idphoto));
    
    // supression dans la table photo_has_rubrique (sans l'utilisation de la clef étrangère)
	$sql_20="DELETE FROM photo_has_rubriques WHERE photo_id = $idphoto;";
	$sql2 = $bdd->prepare($sql_20) or die (print_r(errorInfo()));
	$sql2->execute();
    
    // vérification de l'existence des sections cochées dans le formulaire
            if(isset($_POST['section'])){
            foreach($_POST['section'] AS $clef => $valeur){
                if(ctype_digit($valeur)){
                    // insertion dans la table photo_has_rubrique
					$sqli = "INSERT INTO photo_has_rubriques VALUES (?,?);";
					$pic = $bdd->prepare($sqli) or die (print_r(errorInfo()));
					$pic->execute(array($idphoto,$valeur));
                }
            }
            }
            header("Location: admin.php");
}


// récupérations des images de l'utilisateur connecté dans la table photo avec leurs sections même si il n'y a pas de sections sélectionnées (jointure externe avec LEFT)
$sql = "SELECT p.*, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
        WHERE p.utilisateur_id
            AND p.id = $idphoto
        GROUP BY p.id
        ORDER BY p.id DESC;
    ";
$recup_sql = $bdd->prepare($sql) or die (print_r($bdd->erroInfo()));
$recup_sql->execute();

$recup_photo = $recup_sql->fetch();

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
                 Vous êtes connecté en tant que <span><?php echo $_SESSION['nom_perm']?></span>
                 <h2>Vous avez le droit de modifier</h3>
                 
                 <div id="formulaire">
                <form action="" method="POST" name="onposte">
                    <input type="text" name="letitre" value="<?php echo $recup_photo['letitre'] ?>" required /><br/><br />
 
                    <textarea name="ladesc"><?php echo $recup_photo['ladesc'] ?></textarea><br/><br />
                    
                    
                    <?php
                    
                    // récupération des sections de l'image dans un tableau
                    $recup_sect_img = explode(',',$recup_photo['idrub']);
                    
                    
                    // affichage des sections
					while($ligne = $recup_section->fetch())
					{
						if(in_array($ligne['id'], $recup_sect_img))
						{
							$coche = "checked";
						}
						else
						{
							$coche = "";
						}
                        echo $ligne['lintitule']." : <input type='checkbox' name='section[]' value='".$ligne['id']."' $coche > | ";
                    }
                    
                    echo "<br /><br /><img src='".CHEMIN_RACINE.$dossier_mini.$recup_photo['lenom'].".jpg' alt='' />";
					
                    ?><br/>
                    <input type="submit" value="Envoyer le fichier" />
                </form>
                
            </div>
            
           </div> 
             
             <?php
		        include 'includes/footer.php';
		     ?>
        </div>
    </body>
</html>