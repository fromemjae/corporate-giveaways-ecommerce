<?php
// ============================================================
// ADMIN PRODUCTS
// admin/products.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// DELETE product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    $success = 'Product deleted successfully.';
}

// ADD / EDIT product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $status      = $_POST['status'] ?? 'active';
    $image       = trim($_POST['image'] ?? '');
    $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

    if (!$name || !$price) {
        $error = 'Name and price are required.';
    } else {
        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, image=?, status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'isssdssi', $category_id, $name, $slug, $description, $price, $image, $status, $id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (category_id, name, slug, description, price, image, status) VALUES (?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, 'isssdss', $category_id, $name, $slug, $description, $price, $image, $status);
        }
        mysqli_stmt_execute($stmt);
        $success = $id ? 'Product updated successfully.' : 'Product added successfully.';
    }
}

// Get product to edit
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $edit_id"));
}

$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products | CreativeKit3A Admin</title>
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
    .btn { padding:10px 18px; border-radius:8px; border:none; cursor:pointer; font-size:0.88rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#1a1a2e; color:#fff; } .btn-primary:hover { background:#c8a951; color:#1a1a2e; }
    .btn-danger  { background:#e74c3c; color:#fff; }
    .btn-warning { background:#f39c12; color:#fff; }
    .btn-sm { padding:6px 12px; font-size:0.8rem; }
    .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:0.88rem; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .alert-danger  { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.06); margin-bottom:24px; }
    .card-header { padding:20px 24px; border-bottom:1px solid #f0f0f0; }
    .card-header h3 { font-size:1rem; font-weight:700; color:#1a1a2e; }
    .card-body { padding:24px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 20px; text-align:left; font-size:0.87rem; }
    th { color:#888; font-weight:600; border-bottom:1px solid #f0f0f0; }
    td { border-bottom:1px solid #fafafa; color:#333; }
    tr:last-child td { border-bottom:none; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .badge-active   { background:#d4edda; color:#155724; }
    .badge-inactive { background:#f8d7da; color:#721c24; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { margin-bottom:16px; }
    .form-group label { display:block; font-size:0.85rem; font-weight:600; color:#444; margin-bottom:6px; }
    .form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:0.9rem; outline:none; font-family:inherit; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:#c8a951; }
    .product-img { width:48px; height:48px; object-fit:cover; border-radius:6px; background:#f0f0f0; }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">CreativeKit3A <span>Admin Panel</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <div class="nav-section">Manage</div>
    <a href="products.php" class="active"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
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
    <h2><?= $edit ? 'Edit Product' : 'Products' ?></h2>
    <?php if (!$edit && !isset($_GET['add'])): ?>
      <a href="?add=1" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
    <?php endif; ?>
  </div>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
  <?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

  <?php if ($edit || isset($_GET['add'])): ?>
  <div class="card">
    <div class="card-header"><h3><?= $edit ? 'Edit: ' . htmlspecialchars($edit['name']) : 'Add New Product' ?></h3></div>
    <div class="card-body">
      <form method="POST">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($edit['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category_id">
              <option value="">-- Select Category --</option>
              <?php
              $cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
              while ($cat = mysqli_fetch_assoc($cats)):
                $sel = ($edit && $edit['category_id'] == $cat['id']) ? 'selected' : '';
              ?>
              <option value="<?= $cat['id'] ?>" <?= $sel ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Price (₱) *</label>
            <input type="number" name="price" step="0.01" required value="<?= htmlspecialchars($edit['price'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status">
              <option value="active"   <?= ($edit['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= ($edit['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Image Path (e.g. /CREATIVEKIT3A-WEBSITE/assets/product.png)</label>
          <input type="text" name="image" value="<?= htmlspecialchars($edit['image'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="3"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <?= $edit ? 'Update Product' : 'Add Product' ?>
        </button>
        <a href="products.php" class="btn" style="background:#eee;color:#333;margin-left:8px;">Cancel</a>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <?php if (!isset($_GET['add'])): ?>
  <div class="card">
    <div class="card-header"><h3>All Products (<?= mysqli_num_rows($products) ?>)</h3></div>
    <table>
      <thead>
        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
        <tr>
          <td><?php if ($p['image']): ?><img src="<?= htmlspecialchars($p['image']) ?>" class="product-img" alt=""><?php else: ?>—<?php endif; ?></td>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
          <td>₱<?= number_format($p['price'], 2) ?></td>
          <td><span class="badge badge-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
          <td style="white-space:nowrap">
            <a href="?edit=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i> Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($products) === 0): ?>
        <tr><td colspan="6" style="text-align:center;color:#aaa;padding:32px;">No products yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</main>
</body>
</html>