<?php
session_start();
include("connect.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Email already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Error creating account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup - Expense Tracker</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="tracker">
        <h2>Create Account</h2>
        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email Address" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm" placeholder="Confirm Password" required><br>
            <button class="btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
</div>
</body>
</html>
