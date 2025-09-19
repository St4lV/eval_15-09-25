<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";

session_start();
if (!(isset($_SESSION["email"]) && isset($_SESSION["prenom"]))) {
    session_unset();
    session_destroy();
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Mon compte</h1>
            <hr>
            <h2>Bienvenue <?=$_SESSION["prenom"]?></h2>
            <p><a href="/cart.php">Mon panier</a></p>
            <p><a href="/logout.php">Déconnexion</a></p>
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>