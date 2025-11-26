<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: /index.php');
    exit;
}

$db = new PDO('mysql:host=localhost;dbname=???????????', '???????????', '???????????!?.,');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $stmt = $db->prepare("INSERT INTO comments (username, comment) VALUES (?, ?)");
    $stmt->execute([$_SESSION['username'], $_POST['comment']]);
}

if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: ?page=comments');
    exit;
}

$comments = $db->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll();
$markers = $db->query("SELECT * FROM markers")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="sudoku.js"></script>
    <script src="chess.js"></script>

    <style>
        :root {
            --primary: #1a73e8;
            --primary-light: #e8f0fe;
            --text: #202124;
            --text-light: #5f6368;
            --bg: #ffffff;
            --sidebar-bg: #f8f9fa;
            --card-bg: #ffffff;
            --border: #dadce0;
            --success: #34a853;
            --danger: #ea4335;
        }
        [data-theme="dark"] {
            --primary: #8ab4f8;
            --primary-light: #202124;
            --text: #e8eaed;
            --text-light: #9aa0a6;
            --bg: #202124;
            --sidebar-bg: #282a2d;
            --card-bg: #35363a;
            --border: #5f6368;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Google Sans', 'Segoe UI', sans-serif;
            transition: background 0.3s, color 0.3s;
        }
        body {
            display: flex;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
        }
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            padding: 24px 0;
            height: 100vh;
            position: fixed;
        }
        .sidebar-header {
            padding: 0 24px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 500;
        }
        .sidebar-menu {
            list-style: none;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.95rem;
            border-radius: 0 25px 25px 0;
            margin: 0 8px;
        }
        .sidebar-menu li a:hover, 
        .sidebar-menu li a.active {
            background: var(--primary-light);
            color: var(--primary);
        }
        .sidebar-menu li a i {
            margin-right: 16px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        /* Main Content */
        .content {
            flex: 1;
            margin-left: 280px;
            padding: 32px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 1.75rem;
            font-weight: 500;
            color: var(--text);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
        }
        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text);
        }
        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--card-bg);
            color: var(--text);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn {
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn:hover {
            opacity: 0.9;
        }
        /* Comments */
        .comment-list {
            margin-top: 24px;
        }
        .comment-item {
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .comment-author {
            font-weight: 500;
            color: var(--text);
        }
        .comment-date {
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .comment-text {
            color: var(--text);
            line-height: 1.6;
        }
        .comment-actions {
            margin-top: 12px;
            display: flex;
            gap: 16px;
        }
        .comment-actions a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .comment-actions a:hover {
            color: var(--primary);
        }
        .comment-actions a.delete {
            color: var(--danger);
        }
        /* Map */
        #map {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }
        /* Theme Toggle */
        .theme-toggle {
            background: transparent;
            border: none;
            color: var(--text);
            cursor: pointer;
            font-size: 1.25rem;
        }
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
                padding: 16px 0;
            }
            .content {
                margin-left: 240px;
                padding: 24px;
            }
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="https://via.placeholder.com/40" alt="Logo">
            <h2>Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?page=dashboard" class="<?= ($_GET['page'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="?page=map" class="<?= ($_GET['page'] ?? '') === 'map' ? 'active' : '' ?>">
                <i class="fas fa-map-marked-alt"></i> Harita
            </a></li>
            <li><a href="?page=sudoku" class="<?= ($_GET['page'] ?? '') === 'sudoku' ? 'active' : '' ?>">
    <i class="fas fa-th"></i> Sudoku
            </a></li>
            <li><a href="?page=chess" class="<?= ($_GET['page'] ?? '') === 'chess' ? 'active' : '' ?>">
    <i class="fas fa-chess"></i> Satranç
            </a></li>

            <li><a href="?page=comments" class="<?= ($_GET['page'] ?? '') === 'comments' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i> Yorumlar
            </a></li>
            <li><a href="logout.php" style="color: var(--danger);">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <h1><?= ucfirst($_GET['page'] ?? 'Dashboard') ?></h1>
            <div class="user-avatar" title="<?= htmlspecialchars($_SESSION['username']) ?>">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </div>
        </div>

        <?php
        $page = $_GET['page'] ?? 'dashboard';
        switch ($page) {
            
            case 'sudoku':
    echo '<div class="card">
            <h2 class="card-title"><i class="fas fa-th"></i> Sudoku</h2>
            <iframe src="sudoku.html" width="100%" height="750" style="border: none; border-radius: 12px;"></iframe>
          </div>';
             break;
             
             case 'chess':
    echo '<div class="card">
            <h2 class="card-title"><i class="fas fa-chess"></i> Satranç Oyunu</h2>
            <iframe src="chess.html" width="100%" height="750" style="border: none; border-radius: 12px;"></iframe>
          </div>';
            break;


            case 'dashboard':
                echo '<div class="card">
                        <h2 class="card-title"><i class="fas fa-chart-line"></i> Genel Bakış</h2>
                        <p>Hoş geldin, <strong>' . htmlspecialchars($_SESSION['username']) . '</strong>! Panel istatistiklerin:</p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px;">
                            <div style="background: var(--primary-light); padding: 16px; border-radius: 8px;">
                                <h3 style="color: var(--primary); margin-bottom: 8px;">Yorumlar</h3>
                                <p style="font-size: 2rem; font-weight: bold;">' . count($comments) . '</p>
                            </div>
                            <div style="background: var(--primary-light); padding: 16px; border-radius: 8px;">
                                <h3 style="color: var(--primary); margin-bottom: 8px;">Markerlar</h3>
                                <p style="font-size: 2rem; font-weight: bold;">' . count($markers) . '</p>
                            </div>
                        </div>
                      </div>';
                break;

            case 'map':
                echo '<div class="card">
                        <h2 class="card-title"><i class="fas fa-map-marked-alt"></i> Harita Yönetimi</h2>
                        <div id="map"></div>
                        <form method="POST">
                            <div class="form-group">
                                <label>Enlem</label>
                                <input type="text" class="form-control" name="lat" placeholder="41.0082" required>
                            </div>
                            <div class="form-group">
                                <label>Boylam</label>
                                <input type="text" class="form-control" name="lng" placeholder="28.9784" required>
                            </div>
                            <div class="form-group">
                                <label>Not</label>
                                <input type="text" class="form-control" name="note" placeholder="Konum açıklaması" required>
                            </div>
                            <button type="submit" class="btn"><i class="fas fa-plus"></i> Marker Ekle</button>
                        </form>
                      </div>';
                break;

            case 'comments':
                echo '<div class="card">
                        <h2 class="card-title"><i class="fas fa-comments"></i> Yorum Yönetimi</h2>
                        <form method="POST">
                            <div class="form-group">
                                <label>Yeni Yorum</label>
                                <textarea class="form-control" name="comment" placeholder="Yorumunuzu yazın..."></textarea>
                            </div>
                            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Gönder</button>
                        </form>
                        <div class="comment-list">';

                foreach ($comments as $comment) {
                    echo '<div class="comment-item">
                            <div class="comment-header">
                                <span class="comment-author">' . htmlspecialchars($comment['username']) . '</span>
                                <span class="comment-date">' . $comment['created_at'] . '</span>
                            </div>
                            <p class="comment-text">' . htmlspecialchars($comment['comment']) . '</p>
                            <div class="comment-actions">
                                <a href="?page=comments&delete=' . $comment['id'] . '" class="delete"><i class="fas fa-trash"></i> Sil</a>
                                <a href="#"><i class="fas fa-edit"></i> Düzenle</a>
                            </div>
                          </div>';
                }
                echo '</div></div>';
                break;
        }
        ?>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        
        if (document.getElementById('map')) {
            const map = L.map('map', {
                preferCanvas: true,
                renderer: L.canvas()
            }).setView([41.0082, 28.9784], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            <?php foreach ($markers as $marker): ?>
                L.marker([<?= $marker['lat'] ?>, <?= $marker['lng'] ?>])
                    .addTo(map)
                    .bindPopup(`<b><?= htmlspecialchars($marker['note']) ?></b>`);
            <?php endforeach; ?>

            setTimeout(() => map.invalidateSize(), 100);
        }

        const html = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);

        function toggleTheme() {
            const newTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    </script>
</body>
</html>
