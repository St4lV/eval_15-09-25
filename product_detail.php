<?php
require_once "utils/utils.php";
$categories= getCategories();
$products = getArticles();

require_once "header.php";
require_once "footer.php";
$category="";
if (isset($_GET["category"])) {
    $category = htmlspecialchars($_GET["category"]);
}

$product="";
if (isset($_GET["category"])) {
    $product = htmlspecialchars($_GET["product"]);
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
    <title>Document</title>
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
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>