<?php
require_once "utils/utils.php";
$categories= getCategories();
$products = getArticles();

require_once "header.php";
require_once "footer.php";

session_start();
if (!(isset($_SESSION["email"]) && isset($_SESSION["prenom"]))) {
    session_unset();
    session_destroy();
}

$category="";
if (isset($_GET["category"])) {
    $category = htmlspecialchars($_GET["category"]);
}

$product="";
if (isset($_GET["category"])) {
    $product = htmlspecialchars($_GET["product"]);
}

$result = ["msg" =>""];
if (isset($_POST["valider"])) {
    if (!(isset($_SESSION["email"]) && isset($_SESSION["prenom"]))) {
        header("Location: login.php");
    } else {
        $result=addProductToCart($_SESSION["email"],$category,$product);
    }
}

$category_exist=false;
$product_exist=false;

$act_category;
$act_product;

foreach ($categories as $i) {
    if ($i["category_name"]==$category) {
        $category_exist=true;
        $act_category = $i;
    }
}

foreach ($products as $i) {
    if ($i["name"]==$product) {
        $product_exist=true;
        $act_product = $i;
    }
}

if(!($category_exist && $product_exist)) {
    header("Location: products.php");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$act_product["name"]?></title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h2><?=$act_product["name"]?></h2>
            <img src="<?=getImageFromCDN($act_product['image_url'])?>" alt="<?=$act_product['name']?>">
            <h3><?=$act_product["price"]?>€</h3>
            <p><?=$act_product["description"]?></p>
            <form action="product_detail.php?category=<?=$category?>&product=<?=$product?>" method="post">
                <input type="submit" name="valider" value="Ajouter au panier">
            </form>
            <p><?=$result["msg"]?></p>
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>