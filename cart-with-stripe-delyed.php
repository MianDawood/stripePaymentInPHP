<?php
require './admin/process/connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php require './include/head.php'; ?>
<?php require './include/hometop.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

<style>
    body {
        font-family: 'Kosugi', sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    nav ul li a {
        color: #fff;
        text-decoration: none;
    }

    a:hover {
        color: white;
        text-decoration: none;
    }

    .cart-page {
        margin-top: 50px;
        margin: auto;
        max-width: 1200px;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .cart-page h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    #cart {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    #cart li {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    #cart li img {
        width: 100px;
        height: auto;
        margin-right: 15px;
    }

    #cart li .cart-item-details {
        flex: 1;
    }

    #cart li .cart-item-details p {
        margin: 5px 0;
    }

    #cart li .cart-button {
        background-color: #333;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #cart li .cart-button:hover {
        background-color: #c82333;
    }

    #total {
        font-size: 20px;
        font-weight: bold;
        margin-top: 20px;
        text-align: right;
    }

    .cart-button {
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .cart-button:hover {
        background-color: #141414;
    }

    .btn-secondary {
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-secondary:hover {
        background-color: #141414;
    }

    .btn-primary {
        background-color: #333;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-primary:hover {
        background-color: #141414;
    }

    @media (max-width: 768px) {
        .cart-page {
            padding: 10px;
            margin-top: 20px;
        }

        #cart {
            padding: 0;
        }

        #cart li {
            flex-direction: column;
            align-items: flex-start;
            padding: 10px;
            margin: 10px 0;
        }

        #cart li img {
            margin: 0 0 10px 0;
            width: 80px;
        }

        #total {
            font-size: 18px;
            text-align: left;
        }
    }
</style>

<div class="cart-page container">
    <h2>Shopping Cart</h2>

    <div id="cart"></div>
    <p id="total"></p>
    <div class="row">
        <div class="col-md-12 text-left">
           <button id="checkout-button" class="checkout-btn">Proceed to Checkout</button>
        </div>
    </div>
</div>

<!-- Button to trigger the modal -->


<!-- Modal Structure -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Secure Checkout</h2>
    <p id="modaltotal"></p>
    <form action="stripe.php" method="post" id="payment-form">
        <div class="form-row">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>
        </div>
        <div class="form-row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
        </div>
      
        <div class="form-row">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" placeholder="Your Address" required>
             
        </div>
        
          <div class="form-row">
            <label for="address">Shipping Address</label>
            <input type="text" id="shippingaddress" name="shippingaddress" placeholder="Your Shipping Address" >
             
        </div>
        <h5>Payment Method:</h5>
        <div class="form-row">
             <input type="hidden" id="totalpayment" name="totalpayment">
             <input type="hidden" name="cartItems" id="cartItemsField">
            <label for="card-element">Credit or Debit Card</label>
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
        </div>
        <button type="submit" class="submit-btn">Pay Now</button>
    </form>
  </div>
</div>

<style>
/* General Styles */
body {
  font-family: 'Arial', sans-serif;
  background-color: #f9f9f9;
  color: #333;
}

button.checkout-btn {
  background-color: #007bff;
  color: white;
  padding: 12px 24px;
  font-size: 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button.checkout-btn:hover {
  background-color: #0056b3;
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 30px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: #000;
}

/* Form Styles */
form {
  margin-top: 20px;
}

.form-row {
  margin-bottom: 20px;
}

label {
  font-size: 14px;
  color: #555;
  margin-bottom: 8px;
  display: block;
}

input[type="text"],
input[type="email"],
#card-element {
  background-color: #f1f1f1;
  padding: 12px;
  border-radius: 4px;
  border: 1px solid #ccc;
  font-size: 16px;
  width: 100%;
}

input[type="text"]:focus,
input[type="email"]:focus,
#card-element:focus {
  outline: none;
  border-color: #007bff;
}

#card-errors {
  color: #e74c3c;
  margin-top: 10px;
  font-size: 14px;
}

button.submit-btn {
  background-color: #28a745;
  color: white;
  padding: 12px 20px;
  font-size: 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button.submit-btn:hover {
  background-color: #218838;
}

.row-split {
  display: flex;
  justify-content: space-between;
}

.split {
  flex: 0 0 48%; /* Each split takes up 48% of the width */
}

.split input[type="text"] {
  width: 100%;
}
</style>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script>
    function displayCart() {
        const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
        const cartElement = document.getElementById('cart');
        const totalElement = document.getElementById('total');
        const modaltotalElement = document.getElementById('modaltotal');
        const totalpaymentfield = document.getElementById('totalpayment');
        const checkoutButton = document.getElementById('checkout-button');

        let totalPrice = 0;
        cartElement.innerHTML = '';

        if (cartItems.length === 0) {
            cartElement.innerHTML = '<p>Your cart is empty <a href="https://sneakerbratz.com/">Add Products</a></p>';
             checkoutButton.style.display = 'none'; 
              totalElement.style.display = 'none'; 
        } else {
            cartItems.forEach(item => {
                const itemContainer = document.createElement('li');
                itemContainer.classList.add('cart-item');

                const imageContainer = document.createElement('div');
                imageContainer.classList.add('image-container');

                const img = document.createElement('img');
                img.src = './admin/assets/images/products/' + item.image;
                img.alt = item.name;
                img.classList.add('cart-item-image');
                imageContainer.appendChild(img);

                const detailsContainer = document.createElement('div');
                detailsContainer.classList.add('cart-item-details');

                const name = document.createElement('p');
                name.textContent = `Name: ${item.name}`;
                detailsContainer.appendChild(name);
                
                const desc = document.createElement('p');
                desc.textContent = `Description: ${item.desc}`;
                detailsContainer.appendChild(desc);
                
                const quantity = document.createElement('p');
                quantity.textContent = `Quantity: ${item.quantity}`;
                detailsContainer.appendChild(quantity);

                const size = document.createElement('p');
                size.textContent = `Size: ${item.size}`;
                detailsContainer.appendChild(size);

                const price = document.createElement('p');
                // price.textContent = `Price: $${item.price.toFixed(2)}`;
                price.textContent = `Price: $${item.price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                detailsContainer.appendChild(price);

                const removeBtn = document.createElement('button');
                removeBtn.textContent = 'Remove';
                removeBtn.className = "cart-button";
                removeBtn.onclick = () => {
                    cartItems.splice(cartItems.indexOf(item), 1);
                    saveCart(cartItems);
                    displayCart();
                };
                detailsContainer.appendChild(removeBtn);

                itemContainer.appendChild(imageContainer);
                itemContainer.appendChild(detailsContainer);
                cartElement.appendChild(itemContainer);

                totalPrice += item.price * item.quantity;
                
            });
        }
         totalElement.textContent = `Total Price: $${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        // totalElement.textContent = `Total Price: $${totalPrice.toFixed(2)}`;
        // modaltotalElement.textContent = `Total Price: $${totalPrice.toFixed(2)}`;
        modaltotalElement.textContent = `Total Price: $${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        totalpaymentfield.value = totalPrice.toFixed(2);

        document.getElementById('cartItemsField').value = JSON.stringify(cartItems);
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function clearCart() {
        localStorage.removeItem('cart');
    }
    if (window.location.search.includes('success')) {
        clearCart();
    }
    displayCart();
</script>

<script src="https://js.stripe.com/v3/"></script>

<!-- JavaScript for Modal and Stripe -->
<script>
    // Modal functionality
    var modal = document.getElementById("paymentModal");
    var btn = document.getElementById("checkout-button");
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Stripe integration
    var stripe = Stripe('pk_test_51Pqk4b02W89ZYu6I6DK9sEnqSTpCN8fSqjS4g1ZyDJ6JyeBqw2Y740YJocVW2A1mMT4243HeAT6G5V7zFtu4GhxH00vNMOXzgP');
    var elements = stripe.elements();

    // Create an instance of the card Element.
    var card = elements.create('card', {
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(result.token);
            }
        });
    });

    // Submit the form with the token ID.
    function stripeTokenHandler(token) {
        var form = document.getElementById('payment-form');

        // Add Stripe Token to hidden input
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Add additional form data (Name, Email, Address)
        var nameInput = document.createElement('input');
        nameInput.setAttribute('type', 'hidden');
        nameInput.setAttribute('name', 'name');
        nameInput.setAttribute('value', document.getElementById('name').value);
        form.appendChild(nameInput);

        var emailInput = document.createElement('input');
        emailInput.setAttribute('type', 'hidden');
        emailInput.setAttribute('name', 'email');
        emailInput.setAttribute('value', document.getElementById('email').value);
        form.appendChild(emailInput);

        var addressInput = document.createElement('input');
        addressInput.setAttribute('type', 'hidden');
        addressInput.setAttribute('name', 'address');
        addressInput.setAttribute('value', document.getElementById('address').value);
        form.appendChild(addressInput);

        // Submit the form
        form.submit();
    }
</script>

<?php require './include/footer.php'; ?>

