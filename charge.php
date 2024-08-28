<?php
require_once('vendor/autoload.php');

\Stripe\Stripe::setApiKey('sk_test_51Pqk4b02W89ZYu6Idxs9e4NfJIZqAsGnfWBcqalGbonmAngUik348fxbW4YBHJvKxHVp978PGaSkRwgXJMRnNziL006EFIP3Fu');

$token = $_POST['stripeToken'];

$charge = \Stripe\Charge::create([
    'amount' => 24000, // Amount in cents
    'currency' => 'usd',
    'description' => 'Example charge',
    'source' => $token,
]);

echo '<h1>Successfully charged $50.00!</h1>';



?>