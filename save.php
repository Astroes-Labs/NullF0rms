<?php
// save.php
session_start();
header('Content-Type: application/json');

require_once 'db.php';   // ← Shared connection

// Helper function
function getConfig($pdo, $key) {
    $stmt = $pdo->prepare("SELECT config_value FROM nullforms_config WHERE config_key = ?");
    $stmt->execute([$key]);
    $value = $stmt->fetchColumn();
    return $value !== false ? json_decode($value, true) : null;
}

// Load messages
$success_message = getConfig($pdo, 'success_message') ?? 'Your NullF0rm has been inscribed.';
$duplicate_error = getConfig($pdo, 'duplicate_error') ?? 'You have already inscribed with this X handle or wallet.';

// ======================
// FORM PROCESSING
// ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$xhandle = trim($_POST['xhandle'] ?? '');
$wallet = strtoupper(trim($_POST['wallet'] ?? ''));

if (empty($xhandle) || empty($wallet) || !str_starts_with($wallet, '0X') || strlen($wallet) !== 42) {
    echo json_encode(['success' => false, 'message' => 'Invalid X handle or ETH address.']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];

// ======================
// IP RATE LIMITING (max 2 submissions per IP per day)
// ======================
$stmt = $pdo->prepare("SELECT COUNT(*) FROM nullforms_submissions 
                       WHERE ip = ? AND DATE(timestamp) = CURDATE()");
$stmt->execute([$ip]);
if ($stmt->fetchColumn() >= 2) {
    echo json_encode(['success' => false, 'message' => 'Too many attempts from this IP today.']);
    exit;
}

// ======================
// DUPLICATE CHECK (using DB unique constraints + query)
// ======================
$stmt = $pdo->prepare("SELECT id FROM nullforms_submissions WHERE xhandle = ? OR wallet = ?");
$stmt->execute([strtolower($xhandle), $wallet]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => $duplicate_error]);
    exit;
}

// ======================
// SAVE SUBMISSION
// ======================
$stmt = $pdo->prepare("INSERT INTO nullforms_submissions (xhandle, wallet, ip) VALUES (?, ?, ?)");
$stmt->execute([strtolower($xhandle), $wallet, $ip]);

// Mark session
$_SESSION['already_submitted'] = true;
$_SESSION['handle'] = $xhandle;
$_SESSION['wallet'] = $wallet;

echo json_encode([
    'success' => true,
    'message' => $success_message
]);

exit;