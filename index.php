<?php
session_start();

// Database connection (update credentials)
require_once 'db.php';


$tasks = getConfig($pdo, 'tasks') ?? [];
$quote_message = getConfig($pdo, 'quote_message') ?? '';
$success_title = getConfig($pdo, 'success_title') ?? '★ PROTOCOL COMPLETE ★';
$success_message = getConfig($pdo, 'success_message') ?? 'Your NullF0rm has been inscribed.';
$placeholders = getConfig($pdo, 'placeholders') ?? [];

// Check if user has already submitted
$alreadySubmitted = isset($_SESSION['already_submitted']) && $_SESSION['already_submitted'] === true;
$submitted_handle = htmlspecialchars($_SESSION['handle'] ?? '', ENT_QUOTES);
$submitted_wallet = htmlspecialchars($_SESSION['wallet'] ?? '', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NullF0rms - WHITELIST PROTOCOL</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&amp;family=VT323&amp;display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #000;
            --ink: #fff;
            --light: #ccc;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: 'VT323', monospace;
            font-size: 20px;
        }

        .crt {
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(to bottom, rgba(255, 255, 255, 0.07) 0px, transparent 2px, transparent 4px);
            pointer-events: none;
            z-index: 100;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-bottom: 8px solid var(--ink);
        }

        h1 {
            font-family: 'Press Start 2P', system-ui;
            font-size: clamp(48px, 10vw, 90px);
            line-height: 1;
        }

        .btn-initialize {
            font-family: 'Press Start 2P', system-ui;
            font-size: 18px;
            padding: 20px 50px;
            border: 5px solid var(--ink);
            background: transparent;
            color: var(--ink);
        }

        .quest-card {
            border: 3px solid var(--ink);
            background: #111;
        }

        .quest-card.done {
            border-color: #0f0;
            opacity: 0.85;
        }

        #progress {
            height: 12px;
            background: #222;
            border: 2px solid var(--ink);
            margin: 20px 0;
        }

        #hp {
            height: 100%;
            background: var(--ink);
            width: 0%;
            transition: width 0.6s ease;
        }
    </style>
</head>

<body>
    <div class="crt"></div>

    <?php if ($alreadySubmitted): ?>
        <!-- ALREADY INSCRIBED SCREEN -->
        <div class="hero">
            <div class="container">
                <h1><?= htmlspecialchars($success_title) ?></h1>
                <p class="lead mt-4">You have already inscribed your NullF0rm.</p>

                <div class="border border-3 border-white p-4 mt-5 mx-auto" style="max-width:520px;">
                    <strong>X Handle:</strong> <?= $submitted_handle ?><br><br>
                    <strong>Wallet:</strong> <?= $submitted_wallet ?>
                </div>

                <p class="mt-5"><?= htmlspecialchars($success_message) ?></p>
                <a href="index.php" class="btn btn-initialize mt-4">REFRESH STATUS</a>
            </div>
        </div>
    <?php else: ?>
        <section class="hero">
            <div class="container">
                <h1 class="mb-4">FORM YOUR<br>NULL.</h1>
                <p class="lead mb-5 fs-3">Pure monochrome 1-bit entities.<br>Complete the 4 tasks to claim your slot in the
                    void.</p>
                <a href="#tasks" class="btn btn-initialize btn-lg">▶ INITIALIZE QUESTS</a>
            </div>
        </section>

        <section id="tasks" class="py-5">
            <div class="container">
                <!-- Progress -->
                <div class="text-center mb-4">
                    <div id="progress">
                        <div id="hp"></div>
                    </div>
                    <strong id="hpLabel">0/4 COMPLETE</strong>
                </div>

                <div id="questList" class="row g-4"></div>

                <!-- Save Form -->
                <div id="save" class="mt-5 p-5 border border-4 border-white bg-black">
                    <h3 class="text-center mb-4">SAVE TO CHAIN</h3>
                    <form id="saveForm" action="save.php" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">X USERNAME</label>
                                <input type="text" class="form-control" name="xhandle" placeholder="@yourhandle" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ETH WALLET ADDRESS</label>
                                <input type="text" class="form-control" name="wallet" placeholder="0x..." maxlength="42"
                                    required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-initialize w-100 mt-4">▣ SAVE TO PROTOCOL</button>
                    </form>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <footer class="text-center py-5 border-top border-white">
        <b>NullF0rms</b> - RAW 1-BIT MONOCHROME ENTITIES • 2026
    </footer>

    <!-- Success Modal -->
    <div class="modal fade" id="winModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-black text-white border border-4 border-white">
                <div class="modal-body text-center p-5">
                    <h3><?= htmlspecialchars($success_title) ?></h3>
                    <div id="winGrid" class="d-flex flex-wrap justify-content-center gap-3 my-4"></div>
                    <p><?= htmlspecialchars($success_message) ?></p>
                    <div id="winWallet" class="border border-white p-3 mt-4 text-break"></div>
                    <button class="btn btn-light mt-4" data-bs-dismiss="modal">RETURN TO SYSTEM</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CONFIG = {
            tasks: <?= json_encode($tasks) ?>,
            quoteMessage: <?= json_encode($quote_message) ?>,
            placeholders: <?= json_encode($placeholders) ?>
        };

        let state = { done: Array(4).fill(false) };

        // Populate Tasks
        const questList = document.getElementById('questList');
        CONFIG.tasks.forEach((task, i) => {
            const div = document.createElement('div');
            div.className = 'col-md-6';
            div.innerHTML = `
                <div class="quest-card p-4 h-100">
                    <img src="${CONFIG.placeholders[i] || 'https://picsum.photos/id/201/160/160'}" 
                         class="mb-3" style="height:85px;object-fit:contain;">
                    <h5>${task.title}</h5>
                    <p>${task.desc}</p>
                    <button class="btn btn-initialize w-100 mt-3" data-i="${i}">${task.btn}</button>
                </div>
            `;
            questList.appendChild(div);
        });

        // Task Handler
        questList.addEventListener('click', e => {
            const btn = e.target.closest('button[data-i]');
            if (!btn) return;
            const i = parseInt(btn.dataset.i);
            if (state.done[i]) return;

            window.open(CONFIG.tasks[i].url, '_blank');
            btn.textContent = 'VERIFYING...';
            btn.disabled = true;

            setTimeout(() => {
                state.done[i] = true;
                btn.textContent = '✓ COMPLETE';
                btn.closest('.quest-card').classList.add('done');
                refreshUI();
            }, 2200);
        });

        function refreshUI() {
            const completed = state.done.filter(Boolean).length;
            document.getElementById('hp').style.width = (completed * 25) + '%';
            document.getElementById('hpLabel').textContent = `${completed}/4`;
        }

        // Form Submission - Improved
        document.getElementById('saveForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            // Remove old error if exists
            const oldErr = document.getElementById('formError');
            if (oldErr) oldErr.remove();

            const formData = new FormData(e.target);
            const wallet = formData.get('wallet').trim();

            if (!wallet.startsWith('0x') || wallet.length !== 42) {
                showError("INVALID ETH ADDRESS");
                return;
            }

            try {
                const res = await fetch('save.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    document.getElementById('winWallet').innerHTML =
                        `${formData.get('xhandle')}<br>${wallet}`;
                    new bootstrap.Modal(document.getElementById('winModal')).show();
                    setTimeout(() => location.reload(), 5000);
                } else {
                    showError(data.message || 'Submission failed');
                }
            } catch (err) {
                showError("Connection error. Please try again.");
            }
        });

        function showError(msg) {
            const errDiv = document.createElement('div');
            errDiv.id = 'formError';
            errDiv.className = 'alert alert-danger mt-3 text-center';
            errDiv.textContent = msg;
            document.getElementById('save').appendChild(errDiv);
        }
        // Init
        refreshUI();
    </script>
</body>

</html>