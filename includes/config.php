<?php

// constant contenant la racine du site
define("CHEMIN_RACINE", "http://localhost/eric_test_juin/");

// nom des dossiers de destination des images (chemin relatif)
$dossier_ori = "images/originales/"; // dossier de l'image originale
$dossier_gd = "images/affichees/"; // dossier de l'image pour affichage
$dossier_mini = "images/miniatures/"; // dossier des miniatures

// taille des images d'affichage proportionnelle en pixels
$grande_large = 900; // taille maximale en largeur
$grande_haute = 720; // taille maximale en hauteur

// taille des miniatures coupées et centrées en pixels
$mini_large = 150; // taille maximale en largeur
$mini_haute = 150; // taille maximale en hauteur

// qualité de l'image d'affichage (jpg de 0 à 100)
$grande_qualite = 85;

// qualité de l'image de la miniature (jpg de 0 à 100)
$mini_qualite = 70;

// formats acceptés en minuscule dans un tableau, séparé par des ','
$formats_acceptes = array('jpg','jpeg','png');

$get_pagination = "pg";
$get_pagination_membre = "pag";

$elements_par_page = 20;
$elements_par_page_membre = 20;

$categories = array(1=>"Animaux",
					 2=>"Architectures",
					 3=>"Artistiques",
					 4=>"Personnes",
					 5=>"Paysages",
					 6=>"Sports",
					 7=>"Technologies",
					 8=>"Transports",
					 9=>"Divers");
 