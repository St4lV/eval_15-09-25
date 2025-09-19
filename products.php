<?php
require_once "utils/utils.php";
$categories= getCategories();
$products = getArticles();

require_once "header.php";
require_once "footer.php";
$filter="";
if (isset($_GET["filter"])) {
    $filter = htmlspecialchars($_GET["filter"]);
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
            <label for="product-search">Chercher un produit :</label>
            <input name="product-search" type="search" value="<?=$filter?>">
        <?php
        function categoryObj($c_a){
            $articles = [];
            global $products;
            foreach( $products as $i ) {
                if ($i["category_id"]==$c_a["category_id"]){
                    array_push( $articles, $i );
                }
            }
        ?>
            <h2><?=$c_a["category_name"]?></h2>
            <article class="index-category-products">
        <?php
            foreach($articles as $i) {
        ?>
                <a class="index-product-container" href="/product_detail.php?category=<?=$c_a["category_name"]?>&product=<?=$i["name"]?>">
                    <h3><?=$i["name"]?></h3>
                    <img class="index-product-img" src="<?=getImageFromCDN($i['image_url'])?>" alt="<?=$i['name']?>">
                    <p><?=$i["price"]?>€</p>
                </a>
        <?php
            }
        ?>
            </article>
        <?php
        }

        foreach ($categories as $i){
            if ($filter == ""){
                categoryObj($i);
            } else {
                if ($filter == $i["category_name"]){
                    categoryObj($i);
                }
            }
        }
        ?>
        </div>
    </main>
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>