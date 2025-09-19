<?php
require_once "database.php";

//Fonctions utilitaires :
function getImageFromCDN($url){
    $headers = [
        "api-key: ".$_ENV['CDN_API_KEY'],
    ];

    $context = stream_context_create([
        'http' => [
            'header' => $headers
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response !== false) {
        $base64 = base64_encode($response);
        return 'data:image/png;base64,'.$base64;
    } else {
        return "";
    }
}

//Requêtes SQL :

function getCategories(){
    global $connexion;
    $requete = "SELECT * FROM categories";
    $resultat = $connexion->query($requete);
    $data = $resultat->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}

function getArticles(){
    global $connexion;
    $requete = "SELECT * FROM products ORDER BY name ASC";
    $resultat = $connexion->query($requete);
    $data = $resultat->fetchAll(PDO::FETCH_ASSOC);
    return $data;
}

function verifyEmailExist($email){
    global $connexion;
    $requete = "SELECT email FROM users";
    $resultat = $connexion->query($requete);
    $data = $resultat->fetchAll(PDO::FETCH_ASSOC);
    $exist = false;
    foreach ($data as $i) {
        if ($i["email"] == $email) {
            $exist = true;
        }
    }
    return $exist;
}

function registerUser($nom, $prenom, $adress, $email, $password){
    global $connexion;
    if(verifyEmailExist($email)){
        return ["msg"=>"Cet email est déjà associé à un compte","code"=>403];
    }

    $requete = "INSERT INTO users (email, password, first_name, last_name, address) VALUES (:email, :password, :nom, :prenom, :addresse)";
    $register = $connexion->prepare($requete);

    $register->bindValue(':email', $email, PDO::PARAM_STR);
    $register->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
    $register->bindValue(':nom', $nom, PDO::PARAM_STR);
    $register->bindValue(':prenom', $prenom, PDO::PARAM_STR);
    $register->bindValue(':addresse', $adress, PDO::PARAM_STR);
    $register->execute();

    return ["msg"=>"Compte créé avec succès","code"=>200];
}

function loginUser($email, $password){
    global $connexion;

    if(!verifyEmailExist($email)){
        return ["msg" => "Cet email n'est associé à aucun compte", "code" => 404];
    }

    $requete = $connexion->prepare("SELECT * FROM users WHERE email = ?");
    $requete->execute([$email]);
    $user = $requete->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($password, $user['password'])) {
        return ["msg" => "Mot de passe incorrect", "code" => 401];
    }

    return ["msg" => "Utilisateur connecté avec succès", "code" => 200,"prenom"=>$user['first_name']];
}

?>