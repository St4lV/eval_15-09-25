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

$message="";
if (isset($_POST['valider'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $adress = trim($_POST['addresse']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm-password'];

    $result=[];

    if (empty($nom) || empty($prenom) || empty($adress) || empty($email) || empty($password) || empty($confirm)) {
        $message = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'email n'est pas valide.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
        $message = "Le mot de passe doit contenir au minimum 8 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
    } else {
        $result=registerUser($nom, $prenom, $adress, $email, $password);
        if ($result['code']==403) {
            $message = $result['msg'];
        } else {
            session_start();
            $_SESSION["email"] = $email;
            $_SESSION["prenom"] = $prenom;
            header("location :my-account.php");
        }
        
    }
    


}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Créer un compte</h1>
            <fieldset id="form-fieldset">
                <form method="post" action="register.php" id="connect-form">
                    <div>
                        <label for="nom"><h3>Votre Nom<span class="req-red-star">*</span></h3></label>
                        <input name="nom" type="text" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="prenom"><h3>Votre Prénom<span class="req-red-star">*</span></h3></label>
                        <input name="prenom" type="text" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="addresse"><h3>Votre Adresse<span class="req-red-star">*</span></h3></label>
                        <input name="addresse" type="text" required value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="email"><h3>Votre email<span class="req-red-star">*</span></h3></label>
                        <input name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="password"><h3>Votre mot de passe<span class="req-red-star">*</span></h3></label>
                        <input name="password" type="password" required>
                    </div>
                    <div>
                        <label for="confirm-password"><h3>Confirmez votre mot de passe<span class="req-red-star">*</span></h3></label>
                        <input name="confirm-password" type="password" required>
                    </div>
                    <div>
                        <p><?=$message?></p>
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