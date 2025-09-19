<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";
$products = getArticles();

session_start();
if (!(isset($_SESSION["email"]) && isset($_SESSION["prenom"]))) {
    session_unset();
    session_destroy();
    header("Location: login.php");
}

if (isset($_POST["remove"])) {
    removeProductFromCart($_SESSION["email"], $_POST["product_id"]);
}
$cart = getOrderFromUser( $_SESSION["email"]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
    <meta name="description" content="Site e commerce fictif dans le cadre de l'evaluation backend Ifocop de la formation DIW. "/>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?=displayHeader();?>
    <main>
        <div id="main-comp">
            <h1>Mon panier</h1>
            <?php if(!empty($cart["items"])){ ?>
            <fieldset id="cart-items">
            <?php
                
                foreach ($cart['items'] as $i){
                    $act_product;
                    foreach ($products as $p){                        
                        if ($p["id"] === $i["product_id"]){
                            $act_product = $p;
                            break;
                        }
                    }
                    ?>
                    <article>
                        <h2 class="cart-item-title">
                            <span><?=$p["name"]." x ".$i["quantity"]?></span>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="product_id" value="<?= $i["product_id"]?>">
                                <input type="submit" name="remove" value="X">
                            </form>
                        </h2>
                        <div class="cart-item-container">
                            <img src="<?=getImageFromCDN($p['image_url'])?>" class="cart-item-img">
                            <p class="cart-item-desc"><?=$p["description"]?></p>
                            <div>
                                <p><?=($p["price"]*$i["quantity"])." €"?></p>
                            </div>
                        </div>
                    </article>
                    <hr>
                    <?php
                }
            ?>
            <h2>Total : <?=$cart["order"]["total_amount"]?> €</h2>
            <h3><a href="/checkout.php">Procéder au paiement</a></h3>
            </fieldset><?php } else { ?>
                <h2>Le panier est vide</h2>
                <a href="/products.php">Ajouter des articles</a>
            <?php }?>
        </div>
    </main>
    
    <?=displayFooter();?>
    <script src="javascript/index.js"></script>
</body>
</html>