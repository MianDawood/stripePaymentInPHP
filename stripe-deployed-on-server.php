<?php
require_once('vendor/autoload.php');
require './admin/process/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $token = $_POST['stripeToken'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $shippingAddress = $_POST['shippingaddress'];
    $totalpayment = $_POST['totalpayment'];
    $payment_method = 'Stripe';
    
    \Stripe\Stripe::setApiKey('sk_test_51Pqk4b02W89ZYu6Idxs9e4NfJIZqAsGnfWBcqalGbonmAngUik348fxbW4YBHJvKxHVp978PGaSkRwgXJMRnNziL006EFIP3Fu');

$token = $_POST['stripeToken'];

try {
  $charge = \Stripe\Charge::create([
    'amount' => $totalpayment * 100, // Amount in cents
    'currency' => 'usd',
    'description' => "payment for $name having email $email for cart Checkout",
    'source' => $token,
]);
    // Save charge details to database, then redirect
    $sql1 = "INSERT INTO customers (name, email, address, shipping_address) VALUES ('$name', '$email', '$address', '$shippingAddress')";
    if ($connect->query($sql1) !== TRUE) {
        echo "Error: " . $sql1 . "<br>" . $connect->error;
        exit; 
    } else {
        $last_id = $connect->insert_id;
    }
    
      $cartItems = isset($_POST['cartItems']) ? json_decode($_POST['cartItems'], true) : [];

    $totalPayment = 0; 

    foreach ($cartItems as $item) {
        $productId = $item['id'];
        $customerId = $last_id;
        $quantity = $item['quantity'];
        $price = $item['price'];
        $totalPrice =  $price * $quantity;
        $totalPayment += $totalPrice; 

        $sql = "INSERT INTO orders (product_id, customer_id, quantity, price, total_price, payment_method) VALUES ('$productId', '$customerId', '$quantity', '$price', '$totalPrice', '$payment_method')";
        if ($connect->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $connect->error;
            exit; 
        }
    }
    header('Location:cart.php?msg=success');
} catch (\Stripe\Exception\CardException $e) {
    echo 'Error: ' . $e->getMessage();
}

    


 
  
}




?>