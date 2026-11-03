<?php
$host = 'localhost';
$username = 'your_db_username';
$password = 'your_db_password';
$dbname = 'your_database_name';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for security
$conn->set_charset("utf8mb4");







<?php
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        
        // 1. Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email); // "ss" means two string parameters
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "Username or Email is already taken.";
            $stmt->close();
        } else {
            $stmt->close();
            
            // 2. Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 3. Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password); // "sss" for three strings
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        .form-container { width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        .error { color: red; }
        .success { color: green; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; }
        .input-group input { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php if (!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        <?php if (!empty($success)): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
        
        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>





<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        
        // Prepare query with a placeholder
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username); // "s" means one string type variable
        $stmt->execute();
        
        // Get the result set
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Verify password using the securely fetched hash
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        .form-container { width: 300px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; }
        .error { color: red; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; }
        .input-group input { width: 100%; padding: 8px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

