<?php
// ============================================================
// ADMIN DASHBOARD
// admin/dashboard.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Get stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM messages WHERE is_read = 0"))['c'];
$total_admins   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM admins"))['c'];

// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT o.*, u.first_name, u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CreativeKit3A Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f0f2f5; display:flex; min-height:100vh; }

    /* SIDEBAR */
    .sidebar {
      width: 240px; background:#1a1a2e; color:#fff;
      display:flex; flex-direction:column; min-height:100vh; position:fixed;
    }
    .sidebar-brand {
      padding: 24px 20px; font-size:1.1rem; font-weight:700;
      border-bottom:1px solid rgba(255,255,255,0.1); color:#c8a951;
    }
    .sidebar-brand span { display:block; font-size:0.75rem; color:#aaa; font-weight:400; margin-top:2px; }
    .sidebar-nav { padding:16px 0; flex:1; }
    .sidebar-nav a {
      display:flex; align-items:center; gap:12px;
      padding:12px 20px; color:#ccc; text-decoration:none;
      font-size:0.9rem; transition:all 0.2s;
    }
    .sidebar-nav a:hover, .sidebar-nav a.active { background:rgba(200,169,81,0.15); color:#c8a951; }
    .sidebar-nav a i { width:18px; text-align:center; }
    .sidebar-nav .nav-section {
      padding:8px 20px; font-size:0.7rem; color:#666;
      text-transform:uppercase; letter-spacing:1px; margin-top:8px;
    }
    .sidebar-nav .superadmin-only { display: <?= isSuperAdmin() ? 'flex' : 'none' ?>; }
    .sidebar-footer {
      padding:16px 20px; border-top:1px solid rgba(255,255,255,0.1);
      font-size:0.8rem; color:#888;
    }
    .sidebar-footer a { color:#c8a951; text-decoration:none; }

    /* MAIN */
    .main { margin-left:240px; flex:1; padding:32px; }
    .page-header { margin-bottom:28px; }
    .page-header h2 { font-size:1.5rem; color:#1a1a2e; font-weight:700; }
    .page-header p { color:#888; font-size:0.9rem; margin-top:4px; }

    /* ROLE BADGE */
    .role-badge {
      display:inline-block; padding:2px 10px; border-radius:20px;
      font-size:0.75rem; font-weight:600; margin-left:8px;
      background: <?= isSuperAdmin() ? '#fff3cd' : '#d1ecf1' ?>;
      color: <?= isSuperAdmin() ? '#856404' : '#0c5460' ?>;
    }

    /* STAT CARDS */
    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px; margin-bottom:32px; }
    .stat-card {
      background:#fff; border-radius:12px; padding:24px 20px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06); display:flex; align-items:center; gap:16px;
    }
    .stat-icon {
      width:48px; height:48px; border-radius:10px;
      display:flex; align-items:center; justify-content:center; font-size:1.3rem; color:#fff;
    }
    .stat-icon.blue   { background:#3498db; }
    .stat-icon.green  { background:#2ecc71; }
    .stat-icon.orange { background:#e67e22; }
    .stat-icon.red    { background:#e74c3c; }
    .stat-icon.purple { background:#9b59b6; }
    .stat-info h3 { font-size:1.6rem; font-weight:700; color:#1a1a2e; }
    .stat-info p  { font-size:0.8rem; color:#888; margin-top:2px; }

    /* TABLE */
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px; }
    .card-header { padding:20px 24px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; }
    .card-header h3 { font-size:1rem; font-weight:700; color:#1a1a2e; }
    .card-header a { font-size:0.85rem; color:#c8a951; text-decoration:none; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 24px; text-align:left; font-size:0.88rem; }
    th { color:#888; font-weight:600; border-bottom:1px solid #f0f0f0; }
    td { border-bottom:1px solid #fafafa; color:#333; }
    tr:last-child td { border-bottom:none; }
    .badge {
      display:inline-block; padding:3px 10px; border-radius:20px;
      font-size:0.75rem; font-weight:600;
    }
    .badge-pending    { background:#fff3cd; color:#856404; }
    .badge-processing { background:#d1ecf1; color:#0c5460; }
    .badge-shipped    { background:#d4edda; color:#155724; }
    .badge-delivered  { background:#c3e6cb; color:#155724; }
    .badge-cancelled  { background:#f8d7da; color:#721c24; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-brand">
    CreativeKit3A
    <span>Admin Panel</span>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

    <div class="nav-section">Manage</div>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="messages.php"><i class="fas fa-envelope"></i> Messages
      <?php if ($total_messages > 0): ?>
        <span style="background:#e74c3c;color:#fff;border-radius:20px;padding:1px 7px;font-size:0.7rem;margin-left:auto;"><?= $total_messages ?></span>
      <?php endif; ?>
    </a>

    <?php if (isSuperAdmin()): ?>
    <div class="nav-section">Super Admin</div>
    <a href="admins.php" class="superadmin-only"><i class="fas fa-user-shield"></i> Manage Admins</a>
    <?php endif; ?>

    <div class="nav-section">Account</div>
    <a href="/CREATIVEKIT3A-WEBSITE/" target="_blank"><i class="fas fa-globe"></i> View Website</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
  <div class="sidebar-footer">
    Logged in as<br>
    <strong style="color:#fff;"><?= adminName() ?></strong>
    <span class="role-badge"><?= isSuperAdmin() ? 'Super Admin' : 'Admin' ?></span>
  </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main">
  <div class="page-header">
    <h2>Dashboard <?= isset($_GET['error']) ? '<span style="color:#e74c3c;font-size:0.9rem;">⚠️ Unauthorized access</span>' : '' ?></h2>
    <p>Welcome back, <?= adminName() ?>! Here's what's happening today.</p>
  </div>

  <!-- STAT CARDS -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-box"></i></div>
      <div class="stat-info"><h3><?= $total_products ?></h3><p>Total Products</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-users"></i></div>
      <div class="stat-info"><h3><?= $total_users ?></h3><p>Registered Users</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange"><i class="fas fa-shopping-bag"></i></div>
      <div class="stat-info"><h3><?= $total_orders ?></h3><p>Total Orders</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon red"><i class="fas fa-envelope"></i></div>
      <div class="stat-info"><h3><?= $total_messages ?></h3><p>Unread Messages</p></div>
    </div>
    <?php if (isSuperAdmin()): ?>
    <div class="stat-card">
      <div class="stat-icon purple"><i class="fas fa-user-shield"></i></div>
      <div class="stat-info"><h3><?= $total_admins ?></h3><p>Total Admins</p></div>
    </div>
    <?php endif; ?>
  </div>

  <!-- RECENT ORDERS -->
  <div class="card">
    <div class="card-header">
      <h3>Recent Orders</h3>
      <a href="orders.php">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
        <tr>
          <td>#<?= $order['id'] ?></td>
          <td><?= $order['first_name'] ? htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) : 'Guest' ?></td>
          <td>₱<?= number_format($order['total_amount'], 2) ?></td>
          <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($recent_orders) === 0): ?>
        <tr><td colspan="5" style="text-align:center;color:#aaa;padding:24px;">No orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>