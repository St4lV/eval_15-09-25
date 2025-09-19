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

function getUserFromEmail($email){
    global $connexion;
    $requete = $connexion->prepare("SELECT * FROM users WHERE email = ?");
    $requete->execute([$email]);
    $user = $requete->fetch(PDO::FETCH_ASSOC);
    return $user;
}
function loginUser($email, $password){
    global $connexion;

    if(!verifyEmailExist($email)){
        return ["msg" => "Cet email n'est associé à aucun compte", "code" => 404];
    }

    $user = getUserFromEmail($email);

    if (!password_verify($password, $user['password'])) {
        return ["msg" => "Mot de passe incorrect", "code" => 401];
    }

    return ["msg" => "Utilisateur connecté avec succès", "code" => 200,"prenom"=>$user['first_name']];
}

function addProductToCart($email, $category, $product){
    global $connexion;

    if(!verifyEmailExist($email)){
        return ["msg" => "Cet email n'est associé à aucun compte", "code" => 404];
    }

    $categories = getCategories();
    $category_id = null;
    foreach ($categories as $cat) {
        if (strtolower($cat['category_name']) === strtolower($category)) {
            $category_id = $cat['category_id'];
        }
    }

    if (!$category_id) {
        return ["msg" => "Catégorie inexistante", "code" => 404];
    }

    $articles = getArticles();
    $product_id = null;
    $product_price = null;
    foreach ($articles as $art) {
        if ($art['category_id'] == $category_id && strtolower($art['name']) === strtolower($product)) {
            $product_id = $art['id'];
            $product_price = $art['price'];
            break;
        }
    }
    if (!$product_id) {
        return ["msg" => "Produit inexistant dans cette catégorie", "code" => 404];
    }

    $requete_user = $connexion->prepare("SELECT id FROM users WHERE email = ?");
    $requete_user->execute([$email]);
    $user = $requete_user->fetch(PDO::FETCH_ASSOC);
    $user_id = $user['id'];

    $requete_cart = $connexion->prepare("SELECT id FROM orders WHERE user_id = ? AND status = 'pending'");
    $requete_cart->execute([$user_id]);
    $cart = $requete_cart->fetch(PDO::FETCH_ASSOC);
    $order_id = 1;
    if ($cart) {
        $order_id = $cart['id'];
        $requete_check_item = $connexion->prepare("SELECT id, quantity FROM order_items WHERE order_id = ? AND product_id = ?");
        $requete_check_item->execute([$order_id, $product_id]);
        $item = $requete_check_item->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $new_quantity = $item['quantity'] + 1;
            $requete_update_item = $connexion->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
            $requete_update_item->execute([$new_quantity, $item['id']]);
        } else {
            $requete_insert_item = $connexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $requete_insert_item->execute([$order_id, $product_id, 1, $product_price]);
        }
    } else {
        $total = $product_price;
        $requete_insert_order = $connexion->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
        $requete_insert_order->execute([$user_id, $total, 'pending']);
        $order_id = $connexion->lastInsertId();

        $requete_insert_item = $connexion->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $requete_insert_item->execute([$order_id, $product_id, 1, $product_price]);
    }

    updatePriceOrders($order_id);

    return ["msg" => "Produit ajouté au panier avec succès", "code" => 200];
}

function removeProductFromCart($email, $product_id){
    global $connexion;

    if(!verifyEmailExist($email)){
        return ["msg" => "Cet email n'est associé à aucun compte", "code" => 404];
    }

    $requete_user = $connexion->prepare("SELECT id FROM users WHERE email = ?");
    $requete_user->execute([$email]);
    $user = $requete_user->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ["msg" => "Utilisateur introuvable", "code" => 404];
    }
    $user_id = $user['id'];

    $requete_cart = $connexion->prepare("SELECT id FROM orders WHERE user_id = ? AND status = 'pending'");
    $requete_cart->execute([$user_id]);
    $cart = $requete_cart->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        return ["msg" => "Aucun panier en cours", "code" => 404];
    }

    $order_id = $cart['id'];

    $requete_check_item = $connexion->prepare("SELECT id, quantity FROM order_items WHERE order_id = ? AND product_id = ?");
    $requete_check_item->execute([$order_id, $product_id]);
    $item = $requete_check_item->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        if ($item['quantity'] > 1) {
            $new_quantity = $item['quantity'] - 1;
            $requete_update_item = $connexion->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
            $requete_update_item->execute([$new_quantity, $item['id']]);
        } else {
            $requete_delete_item = $connexion->prepare("DELETE FROM order_items WHERE id = ?");
            $requete_delete_item->execute([$item['id']]);
        }
    } else {
        return ["msg" => "Ce produit n'est pas présent dans le panier", "code" => 404];
    }

    updatePriceOrders($order_id);

    return ["msg" => "Produit retiré du panier avec succès", "code" => 200];
}


function updatePriceOrders($order_id){
    global $connexion;
    $articles_list = getArticles();

    $requete_order_items = $connexion->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $requete_order_items->execute([$order_id]);
    $order_items_list = $requete_order_items->fetchAll(PDO::FETCH_ASSOC);

    $total_order = 0;

    foreach($order_items_list as $i){
        $total_by_product = 0;

        foreach ($articles_list as $a){
            if($i['product_id'] == $a['id']){
                $total_by_product = $a['price'] * $i['quantity'];
                $requete_update_item = $connexion->prepare("UPDATE order_items SET price = ? WHERE id = ?");
                $requete_update_item->execute([$total_by_product, $i['id']]);
                $total_order += $total_by_product;
                break;
            }
        }
    }

    $requete_update_order = $connexion->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
    $requete_update_order->execute([$total_order, $order_id]);
}

function getOrderFromUser($email) {
    global $connexion;
    $user = getUserFromEmail($email);
    $requete_orders = $connexion->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'pending'");
    $requete_orders->execute([$user["id"]]);
    $order = ($requete_orders->fetchAll(PDO::FETCH_ASSOC))[0];

    $requete_order_items = $connexion->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $requete_order_items->execute([$order["id"]]);
    $order_items_list = $requete_order_items->fetchAll(PDO::FETCH_ASSOC);

    return ["order"=>$order,"items"=>$order_items_list];
}


?>