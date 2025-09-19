<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";

session_start();
if (isset($_SESSION["email"]) && isset($_SESSION["prenom"])) {
    header("Location: my-account.php");
} else {
    session_unset();
    session_destroy();
}

$message = "";
if (isset($_POST['valider'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'email n'est pas valide.";
    } else {
        $result=loginUser($email, $password);
        if ($result['code']!=200) {
            $message = "Email ou mot de passe incorrect";
        } else {
            session_start();
            $_SESSION["email"] = $email;
            $_SESSION["prenom"] = $result['prenom'];
            header("Location: my-account.php");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Se connecter</h1>
            <fieldset id="form-fieldset">
                <form method="post" action="login.php" id="connect-form">
                    <div>
                        <label for="email"><h3>Votre email <span class="req-red-star">*</span></h3></label>
                        <input name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="password"><h3>Votre mot de passe <span class="req-red-star">*</span></h3></label>
                        <input name="password" type="password" required>
                    </div>
                    <div>
                        <p><?= $message ?></p>
                    </div>
                    <input type="submit" name='valider' value="Connexion">
                </form>
            </fieldset>
            <p id="switch-login-register">Pas encore de compte ? <br><a href="/register.php">Créer un compte</a></p>
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>
