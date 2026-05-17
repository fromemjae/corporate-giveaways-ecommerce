<?php
// ============================================================
// ADMIN LOGIN (DEBUG STABLE VERSION)
// admin/login.php
// ============================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check relative link path back to connection script
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = isset($_POST['email']) ? trim($_POST['POST_email'] ?? $_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password fields.';
    } else {
        // Query the admins table precisely
        $query = "SELECT * FROM admins WHERE email = ? LIMIT 1";
        $stmt  = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin  = mysqli_fetch_assoc($result);

            if ($admin) {
                // Read the status column
                if ($admin['status'] !== 'active') {
                    $error = 'Account Error: This administrator profile is currently marked as ' . htmlspecialchars($admin['status']) . '.';
                } 
                // Test the typed input string against the database hash record
                elseif (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id']   = $admin['id'];
                    $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Login Failed: Password verification failed. The typed password does not match the database secure hash signature.';
                }
            } else {
                $error = 'Login Failed: No administrator profile was found matching email: ' . htmlspecialchars($email);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = 'System Database Query Failure: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --primary: #FF6B00; --dark: #1A1A1A; --bg: #FFF8F3; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
    body { background: var(--bg); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .login-container { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); width: 100%; max-width: 420px; border-top: 5px solid var(--primary); }
    .login-logo { text-align: center; margin-bottom: 24px; }
    .logo-icon { font-size: 3rem; color: var(--primary); margin-bottom: 8px; }
    .login-logo h1 { font-size: 1.8rem; color: var(--dark); }
    h2 { font-size: 1.1rem; color: var(--dark); margin-bottom: 20px; text-align: center; }
    .alert-danger { background: #fff0f0; color: #c62828; border: 1px solid #ffcdd2; padding: 12px; border-radius: 6px; font-size: 0.88rem; margin-bottom: 20px; line-height: 1.4; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.85rem; color: #555; margin-bottom: 6px; font-weight: 600; }
    .input-wrap { position: relative; }
    .input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #aaa; }
    .input-wrap input { width: 100%; padding: 12px 14px 12px 40px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.92rem; }
    .btn-login { width: 100%; padding: 14px; background: var(--primary); color: #fff; border: none; border-radius: 6px; font-size: 0.95rem; font-weight: 600; cursor: pointer; }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-logo">
      <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
      <h1>CreativeKit3A</h1>
    </div>

    <h2>Sign in to continue</h2>

    <?php if ($error): ?>
    <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="admin@example.com" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>
      </div>
      <button type="submit" class="btn-login">LOGIN</button>
    </form>
  </div>
  
  <!--
  <div class="back-link">
    <a href="/CREATIVEKIT3A-WEBSITE/"><i class="fas fa-arrow-left"></i> Back to Website</a>
  </div>
    -->
</div>
</body>
</html>