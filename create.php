<?php
require 'db.php';

$error = '';
$name = '';
$email = '';
$gender = $_POST['gender'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email) VALUES (?, ?)"
            );

            $stmt->execute([$name, $email]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Unable to add this user. Please try again.';
        }
    }
        $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, gender) VALUES (?, ?, ?)"
        );

        $stmt->execute([$name, $email, $gender]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Neon Core</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand">
        <span class="brand-icon">N</span>
        <span>NEON<span class="brand-light">CORE</span></span>
    </a>

    <nav class="nav-links">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="index.php#users" class="nav-link">Manage Users</a>
        <a href="create.php" class="nav-link active">Add User</a>
    </nav>

    <div class="system-status">
        <span class="status-dot"></span>
        SYSTEM ONLINE
    </div>
</header>

<main class="main-content form-page">

    <div class="breadcrumb">
        <a href="index.php">Dashboard</a>
        <span>/</span>
        <span>Add User</span>
    </div>

    <section class="form-panel panel">
        <div class="form-heading">
            <div class="large-icon">＋</div>
            <p class="eyebrow">NEW DATABASE ENTRY</p>
            <h1>Add <span class="gradient-text">New User.</span></h1>
            <p class="welcome-text">
                Register a new account in your user directory.
            </p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="create.php" class="user-form">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter full name"
                maxlength="255"
                value="<?= htmlspecialchars($name) ?>"
                autocomplete="name"
                required
            >

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter email address"
                maxlength="254"
                value="<?= htmlspecialchars($email) ?>"
                autocomplete="email"
                required
            >

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Save User
                </button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>

        <p class="form-note">
            <span class="status-dot"></span>
            Your information will be saved to the connected database.
        </p>
    </section>

    <footer class="page-footer">
        <span>NEON CORE</span>
        <span>NEW RECORD / CREATE</span>
    </footer>

    <label for="gender">Gender</label>
        <select name="gender" id="gender" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Non-binary">Non-binary</option>
        <option value="Prefer not to say">Prefer not to say</option>
    </select>

</main>
</body>
</html>