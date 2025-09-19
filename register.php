<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Création de compte</h1>
            <fieldset id="form-fieldset">
                <form method="post" action="login.php" id="connect-form">
                    <div>
                        <label for="pseudo"><h3>Votre pseudo<span class="req-red-star">*</span></h3></label>
                        <input name="pseudo" type="text" required>
                    </div>
                    <div>
                        <label for="email"><h3>Votre email<span class="req-red-star">*</span></h3></label>
                        <input name="email" type="email" required>
                    </div>
                    <div>
                        <label for="password"><h3>Votre mot de passe<span class="req-red-star">*</span></h3></label>
                        <input name="password" type="password" required>
                    </div>
                    <div>
                        <label for="confirm-password"><h3>Confirmez votre mot de passe<span class="req-red-star">*</span></h3></label>
                        <input name="confirm-password" type="password" required>
                    </div>
                    <input type="submit" name='valider' value="Créer un compte">
                </form>
            </fieldset>
            <p>Déjà un compte ? <a href="/login.php">Se connecter</a></p>
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>