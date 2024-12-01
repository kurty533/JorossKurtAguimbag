
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = "";     // Change this to your database password
$dbname = "portal"; // Change this to your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>
            alert('Please fill out all fields.');
            window.history.back();
        </script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            alert('Invalid email format.');
            window.history.back();
        </script>";
        exit();
    }

    if ($password !== $confirmPassword) {
        echo "<script>
            alert('Passwords do not match.');
            window.history.back();
        </script>";
        exit();
    }

    // Check if the email already exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>
            alert('Email is already registered.');
            window.history.back();
        </script>";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $insertQuery = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
            alert('Signup successful! Redirecting to login page.');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "<script>
            alert('Error occurred during signup. Please try again.');
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
    <title>ISCC Student Portal Sign Up</title>
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
            width: 90%; /* Make it responsive */
            max-width: 400px; /* Restrict the maximum width */
            max-height: 100%; /* Limit the height to 90% of the viewport */
             /* Enable scrolling for excess content */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            box-sizing: border-box; 
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
        .signup-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .signup-btn:hover {
            background-color: #218838;
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
        <div class="title">Create Your Account</div>
        <form id="signupForm" action="signup.php" method="POST">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <button type="submit" class="signup-btn">SIGN UP</button>
        </form>
        <div class="extra-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
        <div id="successMessage" class="success-message" style="display: none;">
            Sign up successful! Redirecting to login...
        </div>
        <div id="errorMessage" class="error-message" style="display: none;">
            Please fill out all fields correctly.
        </div>
    </div>
</body>
</html>