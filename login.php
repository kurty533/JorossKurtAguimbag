<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "portal"; // Replace with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = trim($_POST['Email']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($email) || empty($password)) {
        echo "<script>
            alert('Please fill in both email and password.');
            window.history.back();
        </script>";
        exit();
    }

    // Prepare a query to check user credentials
    $query = "SELECT id, first_name, last_name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Successful login
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
            echo "<script>
                alert('Login successful! Redirecting to the portal.');
                window.location.href = 'index.html'; // Replace with your dashboard file
            </script>";
        } else {
            // Invalid password
            echo "<script>
                alert('Incorrect password. Please try again.');
                window.history.back();
            </script>";
        }
    } else {
        // User not found
        echo "<script>
            alert('Email not registered.');
            window.history.back();
        </script>";
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISCC Student Portal Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #aaff78;
        }
        .container {
            width: 300px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .title {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .form-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-btn:hover {
            background-color: #0056b3;
        }
        .extra-links {
            margin-top: 10px;
            font-size: 12px;
        }
        .extra-links a {
            color: #007bff;
            text-decoration: none;
        }
        .extra-links a:hover {
            text-decoration: underline;
        }
        .note {
            margin-top: 15px;
            font-size: 12px;
            color: #777;
        }
        .success-message {
            margin-top: 15px;
            font-size: 14px;
            color: green;
        }
        .error-message {
            margin-top: 15px;
            font-size: 14px;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html">
            <img src="images/logo.png" alt="Logo" class="logo">
        </a>
        <div class="title">Login to your portal</div>
        <form id="loginForm" action="login.php" method="POST">
            <div class="form-group">
                <label for="Email">Email</label>
                <input type="text" id="Email" name="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">LOGIN</button>
        </form>
        <div class="extra-links">
            <p>Don't have an account? <a href="signup.php">Register Now</a></p>
        </div>
        <div class="note">
            Note: Your default password is your surname in lowercase.
        </div>
    </div>
</body>
</html>
