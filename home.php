<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include("connect.php");

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Add new transaction
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['description'], $_POST['amount'])) {
    $desc = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    if ($desc !== "" && $amount != 0) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $user_id, $desc, $amount);
        $stmt->execute();
    }
}

// Get transactions
$result = $conn->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$result->bind_param("i", $user_id);
$result->execute();
$transactions = $result->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$balance = 0;
$income = 0;
$expense = 0;
foreach ($transactions as $t) {
    $balance += $t['amount'];
    if ($t['amount'] > 0) {
        $income += $t['amount'];
    } else {
        $expense += abs($t['amount']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense Tracker</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>
    <div class="container">
        <div class="tracker">
            <h2>💰 Expense Tracker</h2>
            <p>Welcome, <strong><?= htmlspecialchars($user_name) ?></strong> | <a href="logout.php">Logout</a></p>

            <h4>Your Balance</h4>
            <h1>₦<?= number_format($balance, 2) ?></h1>

            <div class="inc-exp-container">
                <div>
                    <h4>Income</h4>
                    <p class="money plus">₦<?= number_format($income, 2) ?></p>
                </div>
                <div>
                    <h4>Expenses</h4>
                    <p class="money minus" >₦<?= number_format($expense, 2) ?></p>
                </div>
            </div>

<h3><a href="transactions.php" class="history-link">View Full Transaction History</a></h3>



            <h3>Add New Transaction</h3>
            <form method="post" id="form">
                <input type="text" name="description" placeholder="Enter description" required>
                <input type="number" name="amount" step="any" placeholder="Enter amount (e.g. -200, 500)" required>
                <button class="btn">Add Transaction</button>
            </form>
        </div>
    </div>
</body>
</html>
