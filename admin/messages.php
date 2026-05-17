<?php
// ============================================================
// ADMIN MESSAGES / INQUIRIES
// admin/messages.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// DELETE message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM messages WHERE id = $id");
    $success = 'Message deleted.';
}

// MARK as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE id = $id");
}

// VIEW single message
$view = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE id = $id");
    $view = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM messages WHERE id = $id"));
}

$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC");
$unread   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM messages WHERE is_read = 0"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages | CreativeKit3A Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',sans-serif; background:#f0f2f5; display:flex; min-height:100vh; }
    .sidebar { width:240px; background:#1a1a2e; color:#fff; display:flex; flex-direction:column; min-height:100vh; position:fixed; }
    .sidebar-brand { padding:24px 20px; font-size:1.1rem; font-weight:700; border-bottom:1px solid rgba(255,255,255,0.1); color:#c8a951; }
    .sidebar-brand span { display:block; font-size:0.75rem; color:#aaa; font-weight:400; margin-top:2px; }
    .sidebar-nav { padding:16px 0; flex:1; }
    .sidebar-nav a { display:flex; align-items:center; gap:12px; padding:12px 20px; color:#ccc; text-decoration:none; font-size:0.9rem; transition:all 0.2s; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background:rgba(200,169,81,0.15); color:#c8a951; }
    .sidebar-nav a i { width:18px; text-align:center; }
    .sidebar-nav .nav-section { padding:8px 20px; font-size:0.7rem; color:#666; text-transform:uppercase; letter-spacing:1px; margin-top:8px; }
    .sidebar-footer { padding:16px 20px; border-top:1px solid rgba(255,255,255,0.1); font-size:0.8rem; color:#888; }
    .main { margin-left:240px; flex:1; padding:32px; }
    .page-header { margin-bottom:28px; display:flex; align-items:center; justify-content:space-between; }
    .page-header h2 { font-size:1.5rem; color:#1a1a2e; font-weight:700; }
    .btn { padding:8px 14px; border-radius:8px; border:none; cursor:pointer; font-size:0.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#1a1a2e; color:#fff; }
    .btn-danger { background:#e74c3c; color:#fff; }
    .btn-info { background:#3498db; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:0.78rem; }
    .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:0.88rem; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px; }
    .card-header { padding:20px 24px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; }
    .card-header h3 { font-size:1rem; font-weight:700; color:#1a1a2e; }
    .card-body { padding:24px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 20px; text-align:left; font-size:0.87rem; }
    th { color:#888; font-weight:600; border-bottom:1px solid #f0f0f0; }
    td { border-bottom:1px solid #fafafa; color:#333; }
    tr:last-child td { border-bottom:none; }
    tr.unread td { background:#fffdf0; font-weight:600; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .badge-unread { background:#fff3cd; color:#856404; }
    .badge-read   { background:#d4edda; color:#155724; }
    .msg-preview { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#666; font-weight:400; }
    /* VIEW panel */
    .msg-view { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:28px; margin-bottom:24px; }
    .msg-view h3 { font-size:1.1rem; color:#1a1a2e; margin-bottom:4px; }
    .msg-view .meta { font-size:0.82rem; color:#888; margin-bottom:20px; }
    .msg-view .meta span { margin-right:16px; }
    .msg-view .body { font-size:0.93rem; color:#444; line-height:1.7; white-space:pre-wrap; background:#f8f9fa; padding:16px; border-radius:8px; }
    .unread-badge { background:#e74c3c; color:#fff; border-radius:20px; padding:1px 7px; font-size:0.7rem; margin-left:auto; }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">CreativeKit3A <span>Admin Panel</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <div class="nav-section">Manage</div>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages
      <?php if ($unread > 0): ?><span class="unread-badge"><?= $unread ?></span><?php endif; ?>
    </a>
    <?php if (isSuperAdmin()): ?>
    <div class="nav-section">Super Admin</div>
    <a href="admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
    <?php endif; ?>
    <div class="nav-section">Account</div>
    <a href="/CREATIVEKIT3A-WEBSITE/" target="_blank"><i class="fas fa-globe"></i> View Website</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
  <div class="sidebar-footer">Logged in as <strong style="color:#fff"><?= adminName() ?></strong></div>
</aside>

<main class="main">
  <div class="page-header">
    <h2>Messages & Inquiries</h2>
  </div>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <!-- VIEW SINGLE MESSAGE -->
  <?php if ($view): ?>
  <div class="msg-view">
    <h3><?= htmlspecialchars($view['subject'] ?: '(No Subject)') ?></h3>
    <div class="meta">
      <span><i class="fas fa-user"></i> <?= htmlspecialchars($view['name']) ?></span>
      <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($view['email']) ?></span>
      <?php if ($view['phone']): ?><span><i class="fas fa-phone"></i> <?= htmlspecialchars($view['phone']) ?></span><?php endif; ?>
      <span><i class="fas fa-clock"></i> <?= date('M d, Y h:i A', strtotime($view['created_at'])) ?></span>
    </div>
    <div class="body"><?= htmlspecialchars($view['message']) ?></div>
    <div style="margin-top:16px;display:flex;gap:8px;">
      <a href="mailto:<?= htmlspecialchars($view['email']) ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
      <a href="messages.php" class="btn" style="background:#eee;color:#333;">← Back to All</a>
      <a href="?delete=<?= $view['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i> Delete</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- MESSAGES TABLE -->
  <div class="card">
    <div class="card-header">
      <h3>All Messages (<?= mysqli_num_rows($messages) ?>) — <?= $unread ?> unread</h3>
    </div>
    <table>
      <thead>
        <tr><th>From</th><th>Subject</th><th>Preview</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php
        mysqli_data_seek($messages, 0);
        while ($m = mysqli_fetch_assoc($messages)):
        ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread' ?>">
          <td>
            <div style="font-weight:600"><?= htmlspecialchars($m['name']) ?></div>
            <div style="font-size:0.78rem;color:#888"><?= htmlspecialchars($m['email']) ?></div>
          </td>
          <td><?= htmlspecialchars($m['subject'] ?: '(No Subject)') ?></td>
          <td class="msg-preview"><?= htmlspecialchars($m['message']) ?></td>
          <td><span class="badge badge-<?= $m['is_read'] ? 'read' : 'unread' ?>"><?= $m['is_read'] ? 'Read' : 'Unread' ?></span></td>
          <td style="white-space:nowrap"><?= date('M d, Y', strtotime($m['created_at'])) ?></td>
          <td style="white-space:nowrap">
            <a href="?view=<?= $m['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
            <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($messages) === 0): ?>
        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:32px;">No messages yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>