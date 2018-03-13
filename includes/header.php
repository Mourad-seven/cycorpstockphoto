<header>
<?php   var_dump($_SESSION['lenom']) ?>
<div class="logo">
   <?php
      echo "<img src='".CHEMIN_RACINE."img/logo.png' />";
   ?>
</div>
  <div class="connect">
            <?php
            // si on est pas (ou plus) connecté
            if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
                ?>
                <form action="" name="connection" method="POST">
                    <input type="text" name="lelogin" required />
                    <input type="password" name="lepass" required />
                    <input type="submit" value="Connexion" />
                </form>
                <?php
                // sinon on est connecté
            }else{

                // texte d'accueil
                echo "<span>Bonjour ".$_SESSION['lenom']. " | </span>";
                echo "<span class='link'><a href='deconnect.php'>Déconnexion</a></span><br />";
                echo "<span>Vous êtes connecté en tant que <span class='blo'>".$_SESSION['nom_perm']."</span></span><br />";


                // liens  suivant la permission utilisateur
                switch($_SESSION['laperm']){
                    // si on est l'admin
                    case 0 :
                       echo "<span class='link'><a href='admin.php'>Administrer le site</a> | <a href='membre.php'>Espace Client</a></span>";
                        break;
                    // si on est modérateur
                    case 1:
                        echo "<span class='link'><a href='modere.php'>Modérer le site</a> | <a href='membre.php'>Espace Client</a></span>";
                        break;
                    // si autre droit (ici simple utilisateur)
                    default :
                        echo "<span class='link'><a href='membre.php'>Espace Client</a></span>";
                }
            }

            ?>
            <br /><br />
            <form>
               <input type="search" name="search" id="recherche" placeholder="Recherche" class="rech" />
            </form>
        </div>
        <div class="clear"></div>
  <br />
  <nav>
      <?php
           include "includes/menu.php";
      ?>
      <div class="clear"></div>
  </nav>
</header>
