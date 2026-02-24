<?php
session_start();
if (isset($_SESSION['user'])) { header("Location: dashboard.php"); exit(); }

$errors = []; $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (empty($name))                                   $errors[] = "Name is required.";
    elseif (strlen($name) < 3)                          $errors[] = "Name must be at least 3 characters.";
    if (empty($email))                                  $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password))                               $errors[] = "Password is required.";
    elseif (strlen($password) < 5)                      $errors[] = "Password must be at least 5 characters.";
    if ($password !== $confirm)                         $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $users = file_exists('users.json') ? json_decode(file_get_contents('users.json'), true) : [];
        foreach ($users as $u) {
            if ($u['email'] === $email) { $errors[] = "Email already registered."; break; }
        }
        if (empty($errors)) {
            $users[] = ['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)];
            file_put_contents('users.json', json_encode($users));
            $success = "Account created! <a href='login.php'>Login here</a>.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="styles.css">
  <title>Sign Up</title>
</head>

<body>
    
<div class="box">
  <h2>Sign Up</h2>
  <?php foreach ($errors as $e): ?><div class="error">⚠ <?= $e ?></div><?php endforeach; ?>
  <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
  <form method="POST">
    <label>Full Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    <label>Email</label>
    <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    <label>Password</label>
    <input type="password" name="password">
    <label>Confirm Password</label>
    <input type="password" name="confirm">
    <button type="submit">Create Account</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>

</html>
