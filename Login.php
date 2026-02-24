<?php
session_start();
if (isset($_SESSION['user'])) { header("Location: dashboard.php"); exit(); }

$error = "";
$remembered = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
        $found = null;
        foreach ($users as $u) {
            if ($u['email'] === $email) { $found = $u; break; }
        }
        if ($found && password_verify($password, $found['password'])) {
            $_SESSION['user'] = $found['name'];
            $_SESSION['email'] = $found['email'];
            $_SESSION['login_time'] = date('Y-m-d H:i:s');

            // Cookie: remember email for 30 days
            if (isset($_POST['remember'])) {
                setcookie('remember_email', $email, time() + (30 * 24 * 3600), '/');
            } else {
                setcookie('remember_email', '', time() - 3600, '/');
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="styles.css">
  <title>Login</title>
</head>
<body>
<div class="box">
  <h2>Login</h2>
  <?php if ($error): ?><div class="error">⚠ <?= $error ?></div><?php endif; ?>
  <form method="POST">
    <label>Email</label>
    <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $remembered) ?>">
    <label>Password</label>
    <input type="password" name="password">
    <div class="remember">
      <input type="checkbox" name="remember" <?= $remembered ? 'checked' : '' ?>> Remember me
    </div>
    <button type="submit">Login</button>
  </form>
  <p>No account? <a href="signup.php">Sign Up</a></p>
</div>
</body>
</html>