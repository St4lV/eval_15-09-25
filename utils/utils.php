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
//'<img src="data:image/png;base64,'.$base64.'" alt="T-shirt" />';

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
?>