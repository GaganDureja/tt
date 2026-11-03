
<?php
session_start();

// Check whether user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        .navbar {
            background: #333;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            background: #dc3545;
            padding: 8px 15px;
            border-radius: 4px;
        }

        .dashboard {
            max-width: 600px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 10px #ddd;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <strong>My Dashboard</strong>
        <a href="logout.php">Logout</a>
    </div>

    <div class="dashboard">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

        <p>You have successfully logged in.</p>

        <p>Your User ID:
            <?php echo htmlspecialchars((string) $_SESSION['user_id']); ?>
        </p>

        <p>This page is accessible only to logged-in users.</p>
    </div>

</body>
</html>

.
.
.
.
.
.
.

<?php

session_start();

// Remove all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;

?>
