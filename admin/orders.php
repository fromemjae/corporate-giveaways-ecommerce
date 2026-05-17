<?php
// ============================================================
// ADMIN ORDERS
// admin/orders.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// UPDATE order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status   = $_POST['status'] ?? 'pending';
    $allowed  = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
        $success = 'Order status updated.';
    }
}

// DELETE order
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM orders WHERE id = $id");
    $success = 'Order deleted.';
}

// VIEW single order
$view_order = $order_items = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $view_order = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT o.*, u.first_name, u.last_name, u.email, u.phone
         FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = $id"));
    $order_items = mysqli_query($conn,
        "SELECT oi.*, p.name as product_name, p.image
         FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $id");
}

// Status filter
$status_filter = $_GET['status'] ?? '';
$where = $status_filter
    ? "WHERE o.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'"
    : '';
$orders = mysqli_query($conn,
    "SELECT o.*, u.first_name, u.last_name
     FROM orders o LEFT JOIN users u ON o.user_id = u.id
     $where ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders | CreativeKit3A Admin</title>
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
    .page-header { margin-bottom:28px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .page-header h2 { font-size:1.5rem; color:#1a1a2e; font-weight:700; }
    .btn { padding:8px 14px; border-radius:8px; border:none; cursor:pointer; font-size:0.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#1a1a2e; color:#fff; } .btn-primary:hover { background:#c8a951; color:#1a1a2e; }
    .btn-danger  { background:#e74c3c; color:#fff; }
    .btn-info    { background:#3498db; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:0.78rem; }
    .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:0.88rem; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px; }
    .card-header { padding:20px 24px; border-bottom:1px solid #f0f0f0; }
    .card-header h3 { font-size:1rem; font-weight:700; color:#1a1a2e; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 20px; text-align:left; font-size:0.87rem; }
    th { color:#888; font-weight:600; border-bottom:1px solid #f0f0f0; }
    td { border-bottom:1px solid #fafafa; color:#333; }
    tr:last-child td { border-bottom:none; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .badge-pending    { background:#fff3cd; color:#856404; }
    .badge-processing { background:#d1ecf1; color:#0c5460; }
    .badge-shipped    { background:#cce5ff; color:#004085; }
    .badge-delivered  { background:#d4edda; color:#155724; }
    .badge-cancelled  { background:#f8d7da; color:#721c24; }
    /* Filters */
    .filters { display:flex; gap:8px; flex-wrap:wrap; }
    .filters a { padding:6px 14px; border-radius:20px; font-size:0.8rem; font-weight:600; text-decoration:none; background:#f0f0f0; color:#555; transition:all 0.2s; }
    .filters a:hover, .filters a.active { background:#1a1a2e; color:#fff; }
    /* Order detail */
    .order-detail { background:#fff; border-radius:12px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px; }
    .order-detail h3 { font-size:1.1rem; color:#1a1a2e; margin-bottom:16px; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
    .detail-item label { display:block; font-size:0.78rem; color:#888; font-weight:600; text-transform:uppercase; margin-bottom:4px; }
    .detail-item span { font-size:0.92rem; color:#333; }
    .status-form { display:flex; align-items:center; gap:8px; }
    .status-form select { padding:8px 12px; border:1.5px solid #ddd; border-radius:8px; font-size:0.88rem; outline:none; font-family:inherit; }
    .status-form select:focus { border-color:#c8a951; }
    .item-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f0f0f0; }
    .item-row:last-child { border-bottom:none; }
    .item-img { width:44px; height:44px; object-fit:cover; border-radius:6px; background:#f0f0f0; }
    .item-name { flex:1; font-size:0.9rem; color:#333; }
    .item-qty  { font-size:0.85rem; color:#888; white-space:nowrap; }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">CreativeKit3A <span>Admin Panel</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <div class="nav-section">Manage</div>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
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
    <h2>Orders</h2>
    <div class="filters">
      <a href="orders.php"           class="<?= !$status_filter              ? 'active':'' ?>">All</a>
      <a href="?status=pending"      class="<?= $status_filter==='pending'    ? 'active':'' ?>">Pending</a>
      <a href="?status=processing"   class="<?= $status_filter==='processing' ? 'active':'' ?>">Processing</a>
      <a href="?status=shipped"      class="<?= $status_filter==='shipped'    ? 'active':'' ?>">Shipped</a>
      <a href="?status=delivered"    class="<?= $status_filter==='delivered'  ? 'active':'' ?>">Delivered</a>
      <a href="?status=cancelled"    class="<?= $status_filter==='cancelled'  ? 'active':'' ?>">Cancelled</a>
    </div>
  </div>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <!-- ORDER DETAIL -->
  <?php if ($view_order): ?>
  <div class="order-detail">
    <h3>Order #<?= $view_order['id'] ?> Details</h3>
    <div class="detail-grid">
      <div class="detail-item">
        <label>Customer</label>
        <span><?= $view_order['first_name'] ? htmlspecialchars($view_order['first_name'].' '.$view_order['last_name']) : 'Guest' ?></span>
      </div>
      <div class="detail-item"><label>Email</label><span><?= htmlspecialchars($view_order['email'] ?? '—') ?></span></div>
      <div class="detail-item"><label>Phone</label><span><?= htmlspecialchars($view_order['phone'] ?? '—') ?></span></div>
      <div class="detail-item">
        <label>Total Amount</label>
        <span style="font-weight:700;color:#1a1a2e;font-size:1rem;">₱<?= number_format($view_order['total_amount'],2) ?></span>
      </div>
      <div class="detail-item"><label>Date</label><span><?= date('M d, Y h:i A', strtotime($view_order['created_at'])) ?></span></div>
      <div class="detail-item">
        <label>Update Status</label>
        <form method="POST" class="status-form">
          <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">
          <select name="status">
            <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $view_order['status']===$s ? 'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save</button>
        </form>
      </div>
    </div>

    <?php if ($view_order['notes']): ?>
    <p style="color:#555;font-size:0.9rem;margin-bottom:16px;"><strong>Notes:</strong> <?= htmlspecialchars($view_order['notes']) ?></p>
    <?php endif; ?>

    <h4 style="font-size:0.82rem;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Items Ordered</h4>
    <?php if ($order_items && mysqli_num_rows($order_items) > 0): ?>
      <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
      <div class="item-row">
        <?php if ($item['image']): ?>
          <img src="<?= htmlspecialchars($item['image']) ?>" class="item-img" alt="">
        <?php endif; ?>
        <div class="item-name"><?= htmlspecialchars($item['product_name'] ?? 'Deleted Product') ?></div>
        <div class="item-qty">x<?= $item['quantity'] ?> &nbsp; ₱<?= number_format($item['price'],2) ?></div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="color:#aaa;font-size:0.88rem;">No items recorded for this order.</p>
    <?php endif; ?>

    <div style="margin-top:20px;display:flex;gap:8px;">
      <a href="orders.php" class="btn" style="background:#eee;color:#333;"><i class="fas fa-arrow-left"></i> Back</a>
      <a href="?delete=<?= $view_order['id'] ?>" class="btn btn-danger"
         onclick="return confirm('Delete order #<?= $view_order['id'] ?>?')"><i class="fas fa-trash"></i> Delete Order</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDERS TABLE -->
  <div class="card">
    <div class="card-header"><h3>All Orders (<?= mysqli_num_rows($orders) ?>)</h3></div>
    <table>
      <thead>
        <tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($o = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td><strong>#<?= $o['id'] ?></strong></td>
          <td><?= $o['first_name'] ? htmlspecialchars($o['first_name'].' '.$o['last_name']) : 'Guest' ?></td>
          <td>₱<?= number_format($o['total_amount'],2) ?></td>
          <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
          <td style="white-space:nowrap">
            <a href="?view=<?= $o['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
            <a href="?delete=<?= $o['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete order #<?= $o['id'] ?>?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($orders) === 0): ?>
        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:32px;">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>