<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<!-- cart.php -->

<!-- cart.php -->

<!-- Button to trigger the modal -->
<button id="checkout-button" class="checkout-btn">Proceed to Checkout</button>

<!-- Modal Structure -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Secure Checkout</h2>
    <form action="charge.php" method="post" id="payment-form">
        <div class="form-row">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>
        </div>
        <div class="form-row">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
        </div>
        <div class="form-row">
            <label for="address">Shipping Address</label>
            <input type="text" id="address" name="address" placeholder="Your Shipping Address" required>
        </div>
        <div class="form-row">
            <label for="card-element">Credit or Debit Card</label>
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
        </div>
        <div class="form-row">
            <label for="card-expiry">Expiration Date</label>
            <input type="text" id="card-expiry" placeholder="MM / YY" required>
        </div>
        <div class="form-row">
            <label for="card-cvc">CVC</label>
            <input type="text" id="card-cvc" placeholder="CVC" required>
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
</style>

<!-- Include Stripe.js -->
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
    var stripe = Stripe('your-publishable-key-here');
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





</body>
</html>