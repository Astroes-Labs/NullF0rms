<?php
session_start();

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
    <title>NullF0rms — WHITELIST PROTOCOL</title>

    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    
    <!-- Optional: Better favicon support -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="NullF0rms — WHITELIST PROTOCOL" />
    <meta property="og:description"
        content="Pure monochrome 1-bit entities. Complete the 4 tasks to claim your slot in the void." />
    <meta property="og:image" content="https://yourdomain.com/images/preview.jpg" />
    <meta property="og:url" content="https://yourdomain.com/index.php" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="NullF0rms" />

    <!-- Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="NullF0rms — WHITELIST PROTOCOL" />
    <meta name="twitter:description"
        content="Pure monochrome 1-bit entities. Complete the 4 tasks to claim your slot in the void." />
    <meta name="twitter:image" content="https://yourdomain.com/images/preview.jpg" />
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
    </style>
</head>

<body>
    <div class="crt"></div>

    <?php if ($alreadySubmitted): ?>
        <!-- ALREADY INSCRIBED SCREEN -->
        <div class="hero">
            <div class="container">
                <h1>★ PROTOCOL COMPLETE ★</h1>
                <p class="lead mt-4">You have already inscribed your NullF0rm.</p>

                <div class="border border-3 border-white p-4 mt-5 mx-auto" style="max-width:520px;">
                    <strong>X Handle:</strong> <?= $submitted_handle ?><br><br>
                    <strong>Wallet:</strong> <?= $submitted_wallet ?>
                </div>

                <p class="mt-5">Welcome to the void.</p>
                <a href="index.php" class="btn btn-initialize mt-4">REFRESH STATUS</a>
            </div>
        </div>

    <?php else: ?>
        <!-- NORMAL PAGE (Tasks + Form) -->
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
                <!-- Your tasks and unit status here (keep as is) -->
                <div id="questList" class="row g-4"></div>

                <!-- Save Form -->
                <div id="save" class="mt-5 p-5 border border-4 border-white bg-black">
                    <h3 class="text-center mb-4">SAVE TO CHAIN</h3>
                    <form action="save.php" method="POST">
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
        <b>NullF0rms</b> — RAW 1-BIT MONOCHROME ENTITIES • 2026
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // === CONFIG ===
        const CONFIG = {
            tasks: [
                {
                    title: "FOLLOW ON X",
                    desc: "Follow the official NullF0rms account.",
                    url: "https://x.com/NullF0rms",           // ← Update with real handle
                    btn: "FOLLOW"
                },
                {
                    title: "LIKE + RETWEET",
                    desc: "Like and repost the launch announcement.",
                    url: "https://x.com/NullF0rms/status/YOUR_POST_ID",
                    btn: "ENGAGE"
                },
                {
                    title: "COMMENT ON POST",
                    desc: "Leave a comment under the launch post.",
                    url: "https://x.com/NullF0rms/status/YOUR_POST_ID",
                    btn: "COMMENT"
                },
                {
                    title: "QUOTE RETWEET",
                    desc: "Quote the announcement post.",
                    url: "https://x.com/intent/tweet?text=" + encodeURIComponent(
                        "Just joined the @NullF0rms whitelist 🔥\n\n" +
                        "Raw 1-bit monochrome entities dropping soon.\n\n" +
                        "Don't miss this one.\n" +
                        "Join now at www.NullF0rms.xyz"
                    ),
                    btn: "QUOTE"
                }
            ]
        };

        const PLACEHOLDERS = [
            "https://picsum.photos/id/1015/160/160",
            "https://picsum.photos/id/133/160/160",
            "https://picsum.photos/id/160/160/160",
            "https://picsum.photos/id/201/160/160"
        ];

        let state = {
            done: Array(4).fill(false)
        };

        // Populate Tasks
        const questList = document.getElementById('questList');
        CONFIG.tasks.forEach((task, i) => {
            const div = document.createElement('div');
            div.className = 'col-md-6';
            div.innerHTML = `
                <div class="quest-card p-4 h-100">
                    <img src="${PLACEHOLDERS[i]}" class="mb-3" style="height:85px;object-fit:contain;">
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

            if (completed === 4) {
                document.getElementById('save').classList.remove('locked');
                document.getElementById('lockmsg').style.display = 'none';
            }
        }

        // Save Form
        document.getElementById('saveBtn').addEventListener('click', async () => {
            const wallet = document.getElementById('wallet').value.trim();
            let handle = document.getElementById('xhandle').value.trim();
            const err = document.getElementById('err');

            if (!handle) {
                err.textContent = "ENTER X HANDLE";
                err.style.display = 'block';
                return;
            }
            if (!wallet.startsWith('0x') || wallet.length !== 42) {
                err.textContent = "INVALID ETH ADDRESS";
                err.style.display = 'block';
                return;
            }

            err.style.display = 'none';
            if (!handle.startsWith('@')) handle = '@' + handle;

            // Send to PHP
            const formData = new FormData();
            formData.append('xhandle', handle);
            formData.append('wallet', wallet);

            try {
                const res = await fetch('save.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    document.getElementById('winWallet').innerHTML = `${handle}<br>${wallet}`;
                    new bootstrap.Modal(document.getElementById('winModal')).show();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (e) {
                alert("Connection error. Please try again.");
            }
        });

        // Init
        refreshUI();
    </script>

    <!-- WIN MODAL -->
    <div class="modal fade" id="winModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-black text-white border border-4 border-white">
                <div class="modal-body text-center p-5">
                    <h3>★ PROTOCOL COMPLETE ★</h3>
                    <div id="winGrid" class="d-flex flex-wrap justify-content-center gap-3 my-4"></div>
                    <p>Your NullF0rm has been inscribed.</p>
                    <div id="winWallet" class="border border-white p-3 mt-4 text-break"></div>
                    <button class="btn btn-light mt-4" data-bs-dismiss="modal">RETURN TO SYSTEM</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>