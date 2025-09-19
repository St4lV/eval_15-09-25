<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";

session_start();

if (!(isset($_SESSION["email"]) && isset($_SESSION["prenom"]))) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$cart = getOrderFromUser($_SESSION["email"]);
$total = $cart["order"]["total_amount"];
$order_id = $cart["order"]["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = http_build_query([
        "payment_method_types[]" => "card",
        "line_items[0][price_data][currency]" => "eur",
        "line_items[0][price_data][product_data][name]" => "Commande #".$order_id,
        "line_items[0][price_data][unit_amount]" => intval($total * 100),
        "line_items[0][quantity]" => 1,
        "mode" => "payment",
        "success_url" => $_ENV["SERVER_URL"]."/confirmation.php?session_id={CHECKOUT_SESSION_ID}",
        "cancel_url" => $_ENV["SERVER_URL"]."/cart.php",
        "metadata[order_id]" => $order_id
    ]);

    $opts = [
        "http" => [
            "method" => "POST",
            "header" => "Authorization: Basic " . base64_encode($_ENV["STRIPE_API_KEY_PRIVATE"] . ":") . "\r\n" .
                        "Content-Type: application/x-www-form-urlencoded\r\n",
            "content" => $data,
        ]
    ];

    $context = stream_context_create($opts);
    $response = file_get_contents("https://api.stripe.com/v1/checkout/sessions", false, $context);

    if ($response === false) {
        $err = error_get_last();
        header("Content-Type: application/json");
        echo json_encode(["error" => "Impossible de contacter Stripe: ".$err['message']]);
        exit;
    }

    $session = json_decode($response, true);

    header("Content-Type: application/json");
    echo json_encode(["id" => $session["id"]]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?= displayHeader(); ?>
    <main>
        <div id="main-comp">
            <h1>Paiement de votre commande</h1>
            <p>Total : <?=$cart["order"]["total_amount"] ?> €</p>
            <button id="checkout-button">Payer avec Stripe</button>
        </div>
    </main>
    <?= displayFooter(); ?>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
    const stripe = Stripe("<?=$_ENV["STRIPE_API_KEY_PUBLIC"]?>");

    document.getElementById("checkout-button").addEventListener("click", async () => {
        const res = await fetch("checkout.php", { method: "POST" });
        const session = await res.json();
        if (session.id) {
            stripe.redirectToCheckout({ sessionId: session.id });
        } else {
            alert("Erreur : " + session.error);
        }
    });
    </script>
</body>
</html>
