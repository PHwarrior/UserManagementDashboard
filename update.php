<?php
require 'db.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    header("Location: index.php");
    exit;
}

// Find the user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
$stmt = $pdo->prepare(
    "UPDATE users SET name = ?, email = ?, gender = ? WHERE id = ?"
);

$stmt->execute([$name, $email, $gender, $id]);

if (!$user) {
    header("Location: index.php");
    exit;
}

$error = '';
$name = $user['name'];
$email = $user['email'];

// Save changes
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
                "UPDATE users SET name = ?, email = ? WHERE id = ?"
            );

            $stmt->execute([$name, $email, $id]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Unable to update this user. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Neon Core</title>
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
        <a href="create.php" class="nav-link">Add User</a>
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
        <a href="index.php#users">Users</a>
        <span>/</span>
        <span>Edit User</span>
    </div>

    <section class="form-panel panel">
        <div class="form-heading">
            <div class="large-icon">✎</div>
            <p class="eyebrow">DATABASE MODIFICATION</p>
            <h1>Edit <span class="gradient-text">User.</span></h1>
            <p class="welcome-text">
                Update the information for user #<?= (int)$id ?>.
            </p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="user-form">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
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
                maxlength="254"
                value="<?= htmlspecialchars($email) ?>"
                autocomplete="email"
                required
            >

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Save Changes
                </button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>

        <p class="form-note">
            <span class="status-dot"></span>
            Changes will be saved to your existing user record.
        </p>
    </section>

    <footer class="page-footer">
        <span>NEON CORE</span>
        <span>RECORD #<?= (int)$id ?> / EDIT</span>
    </footer>
    
    <select name="gender" required>
    <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
    <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
    <option value="Non-binary" <?= $user['gender'] === 'Non-binary' ? 'selected' : '' ?>>Non-binary</option>
    <option value="Prefer not to say" <?= $user['gender'] === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
    </select>

</main>
</body>
</html>