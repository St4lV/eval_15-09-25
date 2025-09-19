<?php
require_once "utils/utils.php";
require_once "header.php";
require_once "footer.php";

if (!isset($_GET["session_id"])) {
    die("Session manquante");
}

$session_id = $_GET["session_id"];

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Authorization: Basic " . base64_encode($_ENV["STRIPE_API_KEY_PRIVATE"] . ":") . "\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents("https://api.stripe.com/v1/checkout/sessions/$session_id", false, $context);

if ($response === false) {
    die("Impossible de contacter Stripe");
}

$session = json_decode($response, true);

$result="";

if ($session["payment_status"] === "paid") {
    $order_id = $session['metadata']['order_id'] ?? null;
    if ($order_id) {
        $requete = $connexion->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $requete->execute([$order_id]);
    }
    $result = "Paiement validé !";
} else {
    $result = "Le paiement échoué.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?= displayHeader(); ?>
    <main>
        <div id="main-comp">
            <h1><?=$result?></h1>

        </div>
    </main>
    <?= displayFooter(); ?>
</body>
</html>