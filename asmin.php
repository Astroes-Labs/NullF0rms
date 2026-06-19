<?php
session_start();

// Simple password protection (change this!)
$admin_password = 'your_secure_password'; // ← CHANGE THIS

if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['password'] ?? '' === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        echo '<form method="POST" class="p-5 text-center">
                <h3>Admin Login</h3>
                <input type="password" name="password" class="form-control w-25 mx-auto mt-3" placeholder="Password">
                <button type="submit" class="btn btn-light mt-3">Login</button>
              </form>';
        exit;
    }
}

// Database connection (same as others)
$host = 'localhost'; $db = 'your_database'; $user = 'your_db_user'; $pass = 'your_db_password';
$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'config_') === 0) {
            $config_key = substr($key, 7);
            $stmt = $pdo->prepare("INSERT INTO nullforms_config (config_key, config_value) 
                                  VALUES (?, ?) ON DUPLICATE KEY UPDATE config_value = ?");
            $stmt->execute([$config_key, $value, $value]);
        }
    }
    echo "<div class='alert alert-success'>Config updated successfully!</div>";
}

// Load all config
$stmt = $pdo->query("SELECT * FROM nullforms_config");
$config = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NullF0rms Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container py-5">
    <h1 class="mb-4">NullF0rms Admin Panel</h1>
    <a href="index.php" class="btn btn-secondary mb-4">← Back to Site</a>

    <form method="POST">
        <?php foreach ($config as $row): ?>
            <div class="mb-3">
                <label class="form-label"><strong><?= htmlspecialchars($row['config_key']) ?></strong></label>
                <textarea name="config_<?= htmlspecialchars($row['config_key']) ?>" class="form-control" rows="4"><?= htmlspecialchars($row['config_value']) ?></textarea>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="update" class="btn btn-success btn-lg">Save All Changes</button>
    </form>
</div>
</body>
</html>