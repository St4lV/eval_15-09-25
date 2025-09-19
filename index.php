<?php
    require_once "utils/utils.php";
    $categories= getCategories();
    $products = getArticles();

    require_once "header.php";
    require_once "footer.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. " />
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Boutique de vêtements en ligne basée en France !</h1>
            <hr>
            <?php
            foreach( $categories as $i ) {
                $articles = [];
                foreach( $products as $j ) {
                    if ($j["category_id"]==$i["category_id"]){
                        array_push( $articles, $j );
                    }
                }
                //var_dump($articles);
                ?>
                <h2><?=$i["category_name"]?></h2>
                <div class="index-category-products">
                
                <?php
                for( $j =0; $j < 3; $j++ ){
                    if (!$articles[$j]){break;}
                ?>
                <a class="index-product-container" href="/product_detail.php?category=<?=$i["category_name"]?>&product=<?=$articles[$j]["name"]?>">
                    <h3><?=$articles[$j]["name"]?></h3>
                    <img class="index-product-img" src="<?=getImageFromCDN($articles[$j]['image_url'])?>" alt="<?=$articles[$j]['name']?>">
                    <p><?=$articles[$j]["price"]?>€</p>
                </a>
                <?php
                }
                ?>
                </div>
                <form method="get" action="products.php">
                    <input type="hidden" name="filter" value="<?= $i["category_name"] ?>">
                    <input type="submit" value="Voir plus de <?= $i["category_name"] ?>" class="index-see-more-products">
                </form>
                <hr>
                <?php
            }
            ?>
        </div>
    </main>

    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>