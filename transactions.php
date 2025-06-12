<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: transactions.php");
    exit();
}

// Search and Date Filter
$search = $_GET['search'] ?? '';
$from_date = $_GET['from'] ?? '';
$to_date = $_GET['to'] ?? '';

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Build SQL with filters
$sql = "SELECT * FROM transactions WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if (!empty($search)) {
    $sql .= " AND description LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($from_date)) {
    $sql .= " AND date >= ?";
    $params[] = $from_date;
    $types .= "s";
}

if (!empty($to_date)) {
    $sql .= " AND date <= ?";
    $params[] = $to_date;
    $types .= "s";
}

$total_sql = $sql; // For total count (without LIMIT)
$sql .= " ORDER BY date DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

// Prepare and execute
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get total for pagination
$stmt = $conn->prepare($total_sql);
$stmt->bind_param(substr($types, 0, -2), ...array_slice($params, 0, -2)); // Remove LIMIT params
$stmt->execute();
$total_result = $stmt->get_result();
$total_transactions = $total_result->num_rows;
$total_pages = ceil($total_transactions / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Your Full Transaction History</h2>
    <a href="home.php" class="btn">← Back to Dashboard</a>

    <!-- Filter Form -->
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search description..." value="<?= htmlspecialchars($search) ?>">
        <input type="date" name="from" value="<?= htmlspecialchars($from_date) ?>">
        <input type="date" name="to" value="<?= htmlspecialchars($to_date) ?>">
        <button type="submit">Filter</button>
    </form>

    <div class="transaction-history">
        <?php if (count($transactions) === 0): ?>
            <p>No transactions found.</p>
        <?php else: ?>
            <?php foreach ($transactions as $t): ?>
                <div class="transaction <?= $t['amount'] < 0 ? 'expense' : 'income' ?>">
                    <span class="description"><?= htmlspecialchars($t['description']) ?></span>
                    <span class="amount"><?= ($t['amount'] > 0 ? '+' : '-') . '₦' . number_format(abs($t['amount']), 2) ?></span>
                    <a href="transactions.php?delete=<?= $t['id'] ?>" class="delete-btn" title="Delete" onclick="return confirm('Delete this transaction?')">x</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&from=<?= $from_date ?>&to=<?= $to_date ?>"
               class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
