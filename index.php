<?php
session_start();
include("connect.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $user_name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            header("Location: home.php");
            exit();
        } else {
            $errors[] = "Incorrect password.";
        }
    } else {
        $errors[] = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Expense Tracker</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="tracker">
        <h2>Login</h2>
        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>
</body>
</html>
