<?php
// ============================================================
// ADMIN USERS (With Promote to Admin Functionality)
// admin/users.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// BAN / UNBAN / DELETE Actions
if (isset($_GET['ban']))    { mysqli_query($conn, "UPDATE users SET status='banned' WHERE id=".(int)$_GET['ban']);   $success = 'User banned successfully.'; }
if (isset($_GET['unban']))  { mysqli_query($conn, "UPDATE users SET status='active' WHERE id=".(int)$_GET['unban']); $success = 'User unbanned successfully.'; }
if (isset($_GET['delete'])) { mysqli_query($conn, "DELETE FROM users WHERE id=".(int)$_GET['delete']);              $success = 'User permanently deleted.'; }

// PROMOTE TO ADMIN CRITICAL MECHANISM
if (isset($_GET['promote'])) {
    $user_id = (int)$_GET['promote'];
    
    // Fetch user details from 'users' table
    $u_res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id LIMIT 1");
    if ($user = mysqli_fetch_assoc($u_res)) {
        $email = $user['email'];
        
        // Ensure email doesn't already exist in 'admins' table
        $check = mysqli_query($conn, "SELECT id FROM admins WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'This user email is already registered inside the Admin team!';
        } else {
            // Copy user info into the admins table securely
            $stmt = mysqli_prepare($conn, "INSERT INTO admins (first_name, last_name, email, password, role, status) VALUES (?, ?, ?, ?, 'admin', 'active')");
            mysqli_stmt_bind_param($stmt, 'ssss', $user['first_name'], $user['last_name'], $user['email'], $user['password']);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = 'User "' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '" has been promoted to Admin successfully!';
            } else {
                $error = 'Promotion database error: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = 'Selected user profile not found.';
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users Management | CreativeKit3A Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
    .container { max-width: 1100px; margin: 0 auto; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .header h2 { margin: 0; color: #FF6B00; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; }
    .btn-sm { padding: 4px 8px; font-size: 0.8rem; }
    .btn-warning { background: #ff9800; color: white; }
    .btn-success { background: #4caf50; color: white; }
    .btn-danger { background: #f44336; color: white; }
    .btn-info { background: #00bcd4; color: white; }
    .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: 600; }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .alert-danger { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    .card { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
    .card-header { background: #fff; padding: 15px 20px; border-bottom: 1px solid #eee; }
    .card-header h3 { margin: 0; font-size: 1.1rem; color: #424242; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th, td { padding: 12px 20px; border-bottom: 1px solid #eee; }
    th { background: #f8f9fa; font-weight: 600; color: #666; }
    tr:hover { background: #fbfbfb; }
    .badge { padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .badge-active { background: #e8f5e9; color: #2e7d32; }
    .badge-banned { background: #ffebee; color: #c62828; }
    .actions-cell { display: flex; gap: 5px; flex-wrap: nowrap; }
  </style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2><i class="fas fa-users"></i> Registered Customers Management</h2>
    <a href="dashboard.php" class="btn" style="background: #424242; color:white;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">
      <h3>All Customers Listed in System (<?= mysqli_num_rows($users) ?>)</h3>
    </div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Status</th>
          <th>Joined Date</th>
          <th>Actions Panel</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><strong><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
          <td><span class="badge badge-<?= $u['status'] ?>"><?= ucfirst($u['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
          <td>
            <div class="actions-cell">
              <a href="?promote=<?= $u['id'] ?>" class="btn btn-info btn-sm" onclick="return confirm('Are you sure you want to promote this customer to an Admin profile?')"><i class="fas fa-user-shield"></i> Make Admin</a>
              
              <?php if ($u['status'] === 'banned'): ?>
                <a href="?unban=<?= $u['id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-unlock"></i> Unban</a>
              <?php else: ?>
                <a href="?ban=<?= $u['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Ban this user account?')"><i class="fas fa-ban"></i> Ban</a>
              <?php endif; ?>
              
              <a href="?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user permanently?')"><i class="fas fa-trash"></i> Delete</a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($users) === 0): ?>
        <tr>
          <td colspan="7" style="text-align: center; color: #888; padding: 30px;">No registered user accounts found in database.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>