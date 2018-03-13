<ul>
 <li><a href="./">Accueil</a></li>
<li><a href="">Catégories</a>
  <ul>
    <?php
       $req = "SELECT * FROM rubriques ORDER BY lintitule ASC";
       $rub_pics =$bdd->prepare($req) or die (print_r(errorInfo()));
       $rub_pics->execute();
       
       while($rubriques = $rub_pics->fetch())
       {
           $sous_categories = $rubriques['lintitule'];
		   $id_rub = $rubriques['id'];
		   
           echo"<li><a href='categories?idrubriques=".$id_rub."'>$sous_categories</a></li>";
       }
    ?>
   </ul>
 </li>
 <li><a href="contact.php">Nous Contacter</a></li>
</ul>