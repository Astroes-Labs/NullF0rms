<?php

session_start();

// ==================== ADMIN PASSWORD ====================
$admin_password = 'somethingStrong'; // ← CHANGE THIS TO SOMETHING STRONG

if (!isset($_SESSION['admin_logged_in'])) {
    if (($_POST['password'] ?? '') === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Admin Login - NullF0rms</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>body { background:#000; color:#fff; font-family: 'VT323', monospace; }</style>
        </head>
        <body class="d-flex align-items-center justify-content-center vh-100">
            <form method="POST" class="border border-white p-5 text-center">
                <h2>ADMIN ACCESS</h2>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" class="btn btn-light">LOGIN</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Database Connection
require_once 'db.php';

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';

    // Update General Config
    if (isset($_POST['update_config'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'config_') === 0) {
                $config_key = substr($key, 7);
                $stmt = $pdo->prepare("INSERT INTO nullforms_config (config_key, config_value) 
                                      VALUES (?, ?) ON DUPLICATE KEY UPDATE config_value = ?");
                $stmt->execute([$config_key, $value, $value]);
            }
        }
        $message = "✅ General config updated!";
    }

    // Update Tasks
    if (isset($_POST['update_tasks'])) {
        $tasks = [];
        for ($i = 0; $i < count($_POST['task_title']); $i++) {
            if (!empty($_POST['task_title'][$i])) {
                $tasks[] = [
                    'title' => $_POST['task_title'][$i],
                    'desc'  => $_POST['task_desc'][$i],
                    'url'   => $_POST['task_url'][$i],
                    'btn'   => $_POST['task_btn'][$i]
                ];
            }
        }
        $stmt = $pdo->prepare("INSERT INTO nullforms_config (config_key, config_value) 
                              VALUES ('tasks', ?) ON DUPLICATE KEY UPDATE config_value = ?");
        $stmt->execute([json_encode($tasks), json_encode($tasks)]);
        $message = "✅ Tasks updated!";
    }

    // Reset All Submissions
    if (isset($_POST['reset_submissions'])) {
        $pdo->exec("TRUNCATE TABLE nullforms_submissions");
        $message = "🗑️ All submissions have been reset.";
    }
}

// Load Data
$configStmt = $pdo->query("SELECT * FROM nullforms_config");
$configRows = $configStmt->fetchAll(PDO::FETCH_ASSOC);

$tasks = [];
foreach ($configRows as $row) {
    if ($row['config_key'] === 'tasks') {
        $tasks = json_decode($row['config_value'], true) ?: [];
        break;
    }
}

$submissionsStmt = $pdo->query("SELECT * FROM nullforms_submissions ORDER BY timestamp DESC");
$submissions = $submissionsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NullF0rms Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&amp;family=VT323&amp;display=swap" rel="stylesheet">
    <style>
        body { background:#000; color:#fff; font-family: 'VT323', monospace; }
        .nav-tabs .nav-link { color:#fff; border-color:#fff; }
        .nav-tabs .nav-link.active { background:#fff; color:#000; }
        .table { background:#111; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>NULLF0RMS ADMIN</h1>
        <a href="index.php" class="btn btn-outline-light">← Back to Site</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="adminTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#config">General Config</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tasks">Tasks Editor</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#submissions">Submissions Log</a></li>
    </ul>

    <div class="tab-content">
        <!-- General Config Tab -->
        <div class="tab-pane fade show active" id="config">
            <form method="POST">
                <?php foreach ($configRows as $row): 
                    if ($row['config_key'] === 'tasks') continue; ?>
                    <div class="mb-3">
                        <label class="form-label"><strong><?= htmlspecialchars($row['config_key']) ?></strong></label>
                        <textarea name="config_<?= htmlspecialchars($row['config_key']) ?>" 
                                  class="form-control" rows="3"><?= htmlspecialchars($row['config_value']) ?></textarea>
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_config" class="btn btn-success btn-lg">Save Config</button>
            </form>
        </div>

        <!-- Tasks Editor Tab -->
        <div class="tab-pane fade" id="tasks">
            <form method="POST">
                <div id="tasksContainer">
                    <?php foreach ($tasks as $i => $task): ?>
                        <div class="border border-white p-4 mb-4 task-row">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Title</label>
                                    <input type="text" name="task_title[]" class="form-control" value="<?= htmlspecialchars($task['title']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Button Text</label>
                                    <input type="text" name="task_btn[]" class="form-control" value="<?= htmlspecialchars($task['btn']) ?>">
                                </div>
                                <div class="col-12">
                                    <label>Description</label>
                                    <input type="text" name="task_desc[]" class="form-control" value="<?= htmlspecialchars($task['desc']) ?>">
                                </div>
                                <div class="col-12">
                                    <label>URL</label>
                                    <input type="text" name="task_url[]" class="form-control" value="<?= htmlspecialchars($task['url']) ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn btn-outline-light mb-4" onclick="addNewTask()">+ Add New Task</button>
                <button type="submit" name="update_tasks" class="btn btn-success btn-lg">Save All Tasks</button>
            </form>
        </div>

        <!-- Submissions Log Tab -->
        <div class="tab-pane fade" id="submissions">
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by handle or wallet...">
            
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>X Handle</th>
                        <th>Wallet</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody id="submissionsTable">
                    <?php foreach ($submissions as $sub): ?>
                        <tr>
                            <td><?= htmlspecialchars($sub['timestamp']) ?></td>
                            <td><?= htmlspecialchars($sub['xhandle']) ?></td>
                            <td><?= htmlspecialchars($sub['wallet']) ?></td>
                            <td><?= htmlspecialchars($sub['ip']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="POST" onsubmit="return confirm('Are you sure? This cannot be undone!')">
                <button type="submit" name="reset_submissions" class="btn btn-danger">Reset All Submissions</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Simple search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#submissionsTable tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    function addNewTask() {
        const container = document.getElementById('tasksContainer');
        const newTask = document.createElement('div');
        newTask.className = 'border border-white p-4 mb-4 task-row';
        newTask.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><label>Title</label><input type="text" name="task_title[]" class="form-control"></div>
                <div class="col-md-6"><label>Button Text</label><input type="text" name="task_btn[]" class="form-control"></div>
                <div class="col-12"><label>Description</label><input type="text" name="task_desc[]" class="form-control"></div>
                <div class="col-12"><label>URL</label><input type="text" name="task_url[]" class="form-control"></div>
            </div>
        `;
        container.appendChild(newTask);
    }
</script>
</body>
</html>