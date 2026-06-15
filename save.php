<?php
// save.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$xhandle = trim($_POST['xhandle'] ?? '');
$wallet  = strtoupper(trim($_POST['wallet'] ?? ''));

if (empty($xhandle) || empty($wallet) || !str_starts_with($wallet, '0X') || strlen($wallet) !== 42) {
    $_SESSION['form_errors'] = ["Invalid X handle or ETH address."];
    header("Location: index.php");
    exit;
}

// Save data...
$data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'xhandle'   => $xhandle,
    'wallet'    => $wallet,
    'ip'        => $_SERVER['REMOTE_ADDR']
];

$file = 'submissions.json';
$existing = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$existing[] = $data;
file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));

// Mark user as submitted
$_SESSION['already_submitted'] = true;
$_SESSION['handle'] = $xhandle;
$_SESSION['wallet'] = $wallet;

header("Location: index.php");
exit;
?>