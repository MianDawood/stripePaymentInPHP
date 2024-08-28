<?php

require_once('vendor/autoload.php');

\Stripe\Stripe::setApiKey('sk_test_51Pqk4b02W89ZYu6Idxs9e4NfJIZqAsGnfWBcqalGbonmAngUik348fxbW4YBHJvKxHVp978PGaSkRwgXJMRnNziL006EFIP3Fu');

// Assume you have a session or database storing cart items
session_start();
$cart = $_SESSION['cart']; // Example cart data
$totalAmount = calculateTotalAmount($cart); // Implement this function to calculate the total amount in cents

$token = $_POST['stripeToken'];

try {
    $charge = \Stripe\Charge::create([
        'amount' => $totalAmount, // Amount in cents
        'currency' => 'usd',
        'description' => 'Cart checkout',
        'source' => $token,
    ]);

    // Save charge details to database, then redirect
    header("Location: success.php?charge_id=" . $charge->id);
    exit();

} catch (\Stripe\Exception\CardException $e) {
    echo 'Error: ' . $e->getMessage();
}

function calculateTotalAmount($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total * 100; // Convert to cents
}




?>