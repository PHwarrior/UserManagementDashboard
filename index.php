<?php
session_start();
require 'db.php';

// Create a token to help protect delete requests.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// DELETE USER
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_id'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        exit('Invalid request. Please return to the dashboard and try again.');
    }

    $id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);

    if ($id !== false && $id > 0) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: index.php");
    exit;
}

// GET USERS
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// COUNT USERS
$totalUsers = count($users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neon Core | User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand">
        <span class="brand-icon">N</span>
        <span>NEON<span class="brand-light">CORE</span></span>
    </a>

    <nav class="nav-links">
        <a href="index.php" class="nav-link active">Dashboard</a>
        <a href="#users" class="nav-link">Manage Users</a>
        <a href="create.php" class="nav-link">Add User</a>
    </nav>

    <div class="system-status">
        <span class="status-dot"></span>
        SYSTEM ONLINE
    </div>
</header>

<main class="main-content">

    <section class="welcome-section">
        <div>
            <p class="eyebrow">USER MANAGEMENT SYSTEM / 01</p>
            <h1>Command <span class="gradient-text">Center.</span></h1>
            <p class="welcome-text">
                Manage your users in a smarter, futuristic workspace.
            </p>
        </div>

        <a href="create.php" class="btn btn-primary">
            <span>＋</span> Add New User
        </a>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">TOTAL USERS</span>
                <span class="stat-icon purple-icon">♙</span>
            </div>
            <h2><?= $totalUsers ?></h2>
            <p>Registered accounts</p>
        </article>

        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">DATABASE</span>
                <span class="stat-icon blue-icon">▤</span>
            </div>
            <h2 class="status-heading">Connected</h2>
            <p>crud_db / MySQL</p>
        </article>

        <article class="stat-card">
            <div class="stat-top">
                <span class="stat-label">SYSTEM STATUS</span>
                <span class="stat-icon green-icon">✓</span>
            </div>
            <h2 class="status-heading">Operational</h2>
            <p>Dashboard is running</p>
        </article>
    </section>

    <section class="panel users-panel" id="users">

        <div class="panel-heading">
            <div>
                <p class="eyebrow">DATABASE DIRECTORY</p>
                <h2>User Records</h2>
            </div>

            <span class="record-count">
                <?= $totalUsers ?> RECORD<?= $totalUsers === 1 ? '' : 'S' ?>
            </span>
        </div>

        <div class="table-toolbar">
            <div class="search-box">
                <span>⌕</span>
                <input
                    type="search"
                    id="userSearch"
                    placeholder="Search name or email..."
                    aria-label="Search users"
                >
            </div>

            <a href="create.php" class="btn btn-outline">
                + Create User
            </a>
        </div>

        <div class="table-wrapper">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>USER</th>
                        <th>EMAIL ADDRESS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($totalUsers > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row">
                            <td class="id-cell">
                                #<?= htmlspecialchars((string)$user['id']) ?>
                            </td>

                            <td>
                                <div class="user-info">
                                    <div class="avatar">
                                        <?= htmlspecialchars(
                                            strtoupper(substr(trim($user['name']), 0, 1))
                                        ) ?>
                                    </div>
                                    <span class="user-name">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </span>
                                </div>
                            </td>

                            <td class="email-cell">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="update.php?id=<?= (int)$user['id'] ?>"
                                        class="btn btn-edit"
                                    >Edit</a>

                                    <form
                                        method="POST"
                                        action="index.php"
                                        class="inline-form"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="delete_id"
                                            value="<?= (int)$user['id'] ?>"
                                        >

                                        <button type="submit" class="btn btn-delete">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <tr id="noResults" style="display:none;">
                        <td colspan="4" class="empty-state">
                            No matching users found.
                        </td>
                    </tr>

                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">
                            <div class="empty-icon">⌕</div>
                            <strong>No users found</strong>
                            <p>Add your first user to get started.</p>
                            <a href="create.php" class="btn btn-primary">
                                Add First User
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="panel-footer">
            <span>SECURE DATABASE ACCESS</span>
            <span>PHP / PDO / MYSQL</span>
        </div>

    </section>

    <footer class="page-footer">
        <span>NEON CORE © <?= date('Y') ?></span>
        <span>DESIGNED FOR THE NEXT GENERATION</span>
    </footer>

</main>

<script>
const searchInput = document.getElementById('userSearch');
const userRows = document.querySelectorAll('.user-row');
const noResults = document.getElementById('noResults');

if (searchInput) {
    searchInput.addEventListener('input', function () {
        const searchText = this.value.toLowerCase().trim();
        let visibleCount = 0;

        userRows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();
            const matches = rowText.includes(searchText);

            row.style.display = matches ? '' : 'none';

            if (matches) {
                visibleCount++;
            }
        });

        if (noResults) {
            noResults.style.display =
                visibleCount === 0 ? '' : 'none';
        }
    });
}
</script>

</body>
</html>