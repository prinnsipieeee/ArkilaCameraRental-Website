<?php
session_start();

// Set content type to HTML
header("Content-Type: text/html; charset=utf-8");

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $fullname = $_POST['firstname'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $payment = $_POST['payment'];
    $cartItems = json_decode($_POST['cartItems'], true);
    $totalAmount = $_POST['totalAmount'];

    // Save order details in session (simulating a database)
    if (!isset($_SESSION['orders'])) {
        $_SESSION['orders'] = [];
    }
    $_SESSION['orders'][] = [
        'fullname' => $fullname,
        'email' => $email,
        'address' => $address,
        'city' => $city,
        'zip' => $zip,
        'payment' => $payment,
        'cartItems' => $cartItems,
        'totalAmount' => $totalAmount,
    ];

    // Redirect back to index.php with JavaScript alert instead of directly
    echo "<script>
            alert('Thank you for purchasing! Your order has been placed successfully.');
            window.location.href = 'index.php';
          </script>";
    exit; // Ensure no further code is executed
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="shortcut icon" href="images/logo.png" type="">
<title>Check-Out</title>
<style>
body {
  font-family: Arial;
  font-size: 17px;
  padding: 8px;
  background-color: #FFFFFF; /* White background */
  color: #000000; /* Black text color */
}

* {
  box-sizing: border-box;
}

.row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  margin: 0 -16px;
}

.col-25 {
  -ms-flex: 25%; /* IE10 */
  flex: 25%;
}

.col-50 {
  -ms-flex: 50%; /* IE10 */
  flex: 50%;
}

.col-75 {
  -ms-flex: 75%; /* IE10 */
  flex: 75%;
}

.col-25,
.col-50,
.col-75 {
  padding: 0 16px;
}

.container {
  background-color: #f9f9f9; /* Light gray background */
  padding: 5px 20px 15px 20px;
  border: 1px solid #ccc; /* Light gray border */
  border-radius: 3px;
}

input[type=text], input[type=email], input[type=number], input[type=file] {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

/* Disable the spinner for number input */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none; /* Remove Chrome and Safari spinner */
    margin: 0; /* Remove margin */
}

label {
  margin-bottom: 10px;
  display: block;
}

.icon-container {
  margin-bottom: 20px;
  padding: 7px 0;
  font-size: 24px;
}

.btn {
  background-color: #FBBB34; /* Yellow button */
  color: #FFFFFF; /* White text on button */
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 3px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #FF9C00; /* Orange button on hover */
}

a {
  color: #2196F3; /* Default blue for links */
}

hr {
  border: 1px solid #ccc; /* Light gray border */
}

span.price {
  float: right;
  color: grey;
}

/* Navbar styling */
nav {
  background-color: #FBBB34; /* Yellow background */
  overflow: hidden;
}

nav a {
  float: left;
  display: block;
  color: white;
  text-align: center;
  padding: 14px 20px;
  text-decoration: none;
}

nav a:hover {
  background-color: #FF9C00; /* Orange on hover */
}

/* Responsive layout */
@media (max-width: 800px) {
  .row {
    flex-direction: column-reverse;
  }
  .col-25 {
    margin-bottom: 20px;
  }
}
</style>
</head>
<body>

<!-- Navbar with options -->
<nav>
  <a class="nav-link" href="index.php">Go back to Homepage</a>
  <a href="#" onclick="exitPage()">Exit</a>
</nav>

<h2>Checkout Form</h2>
<p>Input the right address and choose if Cash On Delivery (COD) or GCash. Thank you!</p>

<div class="row">
  <div class="col-75">
    <div class="container">
      <form id="checkout-form" method="POST" onsubmit="return validateForm()">
        <div class="row">
          <div class="col-50">
            <h3>Billing Address</h3>
            <label for="fname"><i class="fa fa-user"></i> Full Name</label>
            <input type="text" id="fname" name="firstname" placeholder="John M. Doe" required>
            <label for="email"><i class="fa fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" placeholder="john@example.com" required>
            <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
            <input type="text" id="adr" name="address" placeholder="542 W. 15th Street" required>
            <div class="row">
              <div class="col-50">
                <label for="city"><i class="fa fa-institution"></i> City</label>
                <input type="text" id="city" name="city" placeholder="New York" required>
              </div>
              <div class="col-50">
                <label for="zip">Zip</label>
                <input type="number" id="zip" name="zip" placeholder="10001" required min="0" pattern="\d*" title="Please enter valid zip code." style="width: 50%;">
              </div>
            </div>

            <h3>Payment Method</h3>
            <label>
              <input type="radio" name="payment" value="COD" checked required> Cash on Delivery (COD)
            </label><br>
            <label>
              <input type="radio" name="payment" value="GCash" required> GCash Payment
            </label><br>

            <div id="gcash-screenshot" style="display: none;">
              <label for="screenshot">Upload GCash Payment Screenshot (required for GCash payments)</label>
              <input type="file" id="screenshot" name="screenshot" accept="image/*" required>
            </div>
          </div>

          <div class="col-50">
            <h3>Payment</h3>
            <label for="gcash">Scan GCash QR Code</label>
            <img src="images/gcash.png" alt="GCash QR Code" style="width:100%; height:auto; display:block;">
          </div>
          
        </div>
        <label>
          <input type="checkbox" checked="checked" name="sameadr"> Shipping address same as billing
        </label>
        <input type="submit" value="Complete Order" class="btn">
        <input type="hidden" name="cartItems" id="cart-items-hidden">
        <input type="hidden" name="totalAmount" id="total-amount-hidden">
      </form>
    </div>
  </div>
  <div class="col-25">
    <div class="container">
      <h4>Cart <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i> <b id="cart-count">0</b></span></h4>
      <ul id="cart-items">
        <!-- Cart items will be displayed here -->
      </ul>
      <hr>
      <p>Total <span class="price" style="color:black"><b id="cart-total">$0</b></span></p>
    </div>
  </div>
</div>

<script>
// Function to handle payment method selection
const paymentRadios = document.querySelectorAll('input[name="payment"]');
const gcashScreenshotDiv = document.getElementById('gcash-screenshot');

paymentRadios.forEach(radio => {
  radio.addEventListener('change', () => {
    if (radio.value === 'GCash') {
      gcashScreenshotDiv.style.display = 'block';  // Show when GCash is selected
    } else {
      gcashScreenshotDiv.style.display = 'none';   // Hide for other options
    }
  });
});

// Trigger change event to set initial state
document.querySelector('input[name="payment"]:checked').dispatchEvent(new Event('change'));

// Function to validate the form before submission
function validateForm() {
    const email = document.getElementById('email').value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple regex for email validation
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        return false; 
    }

    // Retrieve cart data from localStorage
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    const totalAmount = parseFloat(localStorage.getItem('totalAmount')) || 0;

    // Check if cart is empty
    if (cartItems.length === 0) {
        alert('Your cart is empty. Please add items before completing the order.');
        return false;
    }

    // Store cart items and total amount to hidden inputs before form submission
    document.getElementById('cart-items-hidden').value = JSON.stringify(cartItems);
    document.getElementById('total-amount-hidden').value = totalAmount;

    // Additional validation for GCash payment screenshot if GCash is selected
    const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
    const screenshotInput = document.getElementById('screenshot');

    if (paymentMethod === 'GCash' && !screenshotInput.files.length) {
        alert('Please upload a screenshot of your GCash payment.');
        return false; 
    }

    return true; // Allow form submission
}

// Retrieve cart data from localStorage
const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
const totalAmount = parseFloat(localStorage.getItem('totalAmount')) || 0;

const cartCount = document.getElementById('cart-count');
const cartTotal = document.getElementById('cart-total');
const cartItemsList = document.getElementById('cart-items');

// Displaying cart items dynamically
cartItems.forEach(item => {
    const li = document.createElement('li');
    li.innerText = `${item.name} x${item.quantity} - $${item.total.toFixed(2)}`;
    cartItemsList.appendChild(li);
});

// Updating total amount and item count
cartCount.innerText = cartItems.reduce((acc, item) => acc + item.quantity, 0); // Total items count
cartTotal.innerText = `$${totalAmount.toFixed(2)}`; // Format total amount to 2 decimal places
</script>

</body>
</html>