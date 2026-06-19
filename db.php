<?php
// db.php - Central Database Configuration

$host = 'localhost';
$db   = 'nullform';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Make $pdo available globally
global $pdo;

// db.php - Add / Replace this function
function getConfig($pdo, $key, $default = null) {
    $stmt = $pdo->prepare("SELECT config_value FROM nullforms_config WHERE config_key = ?");
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();

    if ($value === false) {
        return $default;
    }

    // Try to decode as JSON first (for tasks, placeholders, etc.)
    $decoded = json_decode($value, true);

    // If json_decode failed, return the raw string (for messages, titles, etc.)
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        return $value;
    }

    return $decoded;
}