<?php
// Include database connection
include 'db_connect.php';

$error_message = ""; // Initialize the error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // SQL query to get the user record
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user data

        // Compare the entered password with the stored plain text password
        if ($password === $user['password']) {
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "User not found!";
    }

    $stmt->close(); // Close the statement
}

$conn->close(); // Close the database connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARKILA-Camera Rental</title>
    <link rel="shortcut icon" href="img/logo-1.png">

    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url(img/log-1.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: rgba(31, 31, 31, 0.8);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            border-radius: 10px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            color: #b3b3b3;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            color: #ffffff;
            background-color: #333333;
        }

        .form-group input:focus {
            outline: none;
            border: 2px solid #4a90e2;
        }

        .login-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: dimgray;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .login-button:hover {
            background-color: #357ABD;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .register-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: dimgray;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .register-button:hover {
            background-color: #3e8e41;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: dimgray;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background-color: darkgrey;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }



    </style>

    <style>
        /* Add fade-in animation */
        body {
            opacity: 0;
            transition: opacity 0.5s ease-in;
        }
        body.loaded {
            opacity: 1;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>
    </style>
</head>
<body>
    <div class="login-container">
    <h2><i class="fa-solid fa-user fa-2x"></i></h2>
    <form action="admin_login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword" onclick="togglePasswordVisibility()"></i>
            </div>
        </div>
        <button type="submit" class="login-button">LOG IN</button>
        <button class="register-button" onclick="window.location.href='register.php'">REGISTER</button>
        <div class="error-message">
            <?= htmlspecialchars($error_message); ?>
        </div>
    </form>
</div>
    </form>

   <!--  <button class="back-button" onclick="goBack()" ><i class="fa-solid fa-house"></i></button>

    <script>
        function goBack() {
            window.location.href = 'index.html'; // Navigate to index.html
        }
    </script> -->

    <script>
        // Add fade-in effect when the page loads
        document.addEventListener("DOMContentLoaded", function () {
            document.body.classList.add("loaded");
        });
    </script>

</body>
</html>

