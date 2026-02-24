<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_email', '', time() - 3600, '/');
    header("Location: login.php"); exit();
}

$name       = $_SESSION['user'];
$email      = $_SESSION['email'];
$login_time = $_SESSION['login_time'];
$search     = $_GET['search'] ?? '';  // GET method demo
?>
<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="styles.css">
  <title>Dashboard</title>
</head>
<body>

<div class="card">
  <h3>Session Data</h3>
  <p class="info">User: <span><?= htmlspecialchars($name) ?></span></p>
  <p class="info">Email: <span><?= htmlspecialchars($email) ?></span></p>
  <p style="margin-top:10px;"><a href="?logout=1" style="color:red;font-size:13px;">Logout</a></p>
</div>

</div>

</body>
</html>