<?php

//INITIALISATION DE LA BASE DE DONNÉES

try
{
	$bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
	die('Erreur : ' .$e->getMessage());
}

//VÉRIFICATION DES DONNÉES ENTRÉES SI ELLES EXISTENT

if(isset($_POST['pseudo'])&&isset($_POST['message']))
{
	//SÉCURISATION DES ENTRÉES POUR ÉVITER INJECTIONS SQL ET FAILLE XSS
	$pseudo_entree = htmlspecialchars($_POST['pseudo']);
	$message_entree = htmlspecialchars($_POST['message']);
	//AJOUT DANS LA BDD DU NOUVEAU POST
	$ins = $bdd->prepare('INSERT INTO minichat(pseudo,message,date_post)VALUES(:pseudoVal,:messageVal,NOW())') or die(print_r($bdd->errorInfo()));
	$ins->execute(array(
		'pseudoVal'=>$pseudo_entree,
		'messageVal'=>$message_entree
	));
}

//REQUÈTE DE TOUS LES MESSAGES POSTÉS EN ORDRE INVERSÉ

$req = $bdd->query('SELECT pseudo, message, DATE_FORMAT(date_post, \'Le %d/%m/%Y à %Hh%i\') AS date_fr FROM minichat ORDER BY ID DESC');

?>
<!DOCTYPE html>
<html>
<head>
	<title>Mini-Chat</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="styleChat.css">
</head>
<body>
	<form id="chat" method="post" action="minichat.php">
		<label for="pseudo">Pseudo : </label><br />

		<input type="text" name="pseudo" value="<?php if(isset($_POST['pseudo'])){echo htmlspecialchars($_POST['pseudo']);} //AJOUT DU PSEUDO SÉCURISÉ SI DÉJÀ POSTÉ ?>" /><br />
		<label for="message">Message : </label><br />
		<input type="text" name="message" /><br />
		<input type="submit" name="" value="Envoyer"  />
	</form>
	<div id="chat">
	<?php
	
	//BOUCLE POUR LISTER LES DIFFÉRENTS MESSAGES EXTRAITS DE LA BDD

	while($donnee = $req->fetch())
	{
		echo '<div class="chatLigne"><em>' . $donnee['date_fr'] . '</em><strong> ' . $donnee['pseudo'] . ' : </strong>' . $donnee['message'] . '</div>';
	}

	$req->closeCursor();

	?>
	</div>
<p>Ajout capital !</p>
</body>
</html>
