<?php

define('USER','root');
define('PASSW','root');
define('DSN','mysql:host=localhost;dbname=cycorpstockphoto;');

$bdd = new PDO(DSN, USER, PASSW);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$bdd->exec('SET NAMES utf8');

try
{
	$bdd = new PDO(DSN, USER, PASSW);
	$bdd->exec('SET NAMES utf8');
	#$bdd->beginTransaction();
}
catch(PDOException $e)
{
	echo'Échec lors de la connexion :' .$e->getMessage();
}
