<?php
$page_title = "My Account";
$bodyClass  = "page-light-navbar";

// Auth guard
if (empty($_SESSION['customer_id'])) {
    header('Location: ' . $baseUrl . '/customer-login');
    exit;
}

// Fetch customer data
$customerId   = (int)$_SESSION['customer_id'];
$users = selectContent($conn, 'read_users', ['id' => $customerId, 'visibility' => 'show']);
if (empty($users)) {
    unset($_SESSION['customer_id'], $_SESSION['customer_hash'], $_SESSION['customer_name']);
    header('Location: ' . $baseUrl . '/customer-login');
    exit;
}
$customer = $users[0];
$customerHash = $customer['hash_id'];

// Fetch orders
$orders = selectContentDesc($conn, 'read_orders', ['input_email' => $customer['input_email'], 'visibility' => 'show'], 'id', 50);

// Fetch addresses
$addresses = selectContent($conn, 'read_user_addresses', ['tb_link' => $customerHash, 'visibility' => 'show']);

// Stats
$totalOrders     = count($orders);
$pendingOrders   = 0;
$deliveredOrders = 0;
$spentAmount     = 0;
foreach ($orders as $o) {
    $st = strtolower($o['input_status'] ?? '');
    if (in_array($st, ['pending', 'processing', 'paid'])) $pendingOrders++;
    if ($st === 'delivered') $deliveredOrders++;
    if ($st !== 'cancelled') $spentAmount += (float)($o['input_total'] ?? 0);
}

function getStatusBadge($status) {
    $st = strtolower($status ?? 'pending');
    $map = [
        'paid'       => ['#dcfce7', '#15803d', 'Paid'],
        'pending'    => ['#fef9c3', '#854d0e', 'Pending'],
        'processing' => ['#dbeafe', '#1d4ed8', 'Processing'],
        'shipped'    => ['#ede9fe', '#6d28d9', 'Shipped'],
        'delivered'  => ['#dcfce7', '#15803d', 'Delivered'],
        'cancelled'  => ['#fee2e2', '#b91c1c', 'Cancelled'],
    ];
    $cfg = $map[$st] ?? ['#f3f4f6', '#374151', ucfirst($st)];
    return "<span class='v-badge' style='background:{$cfg[0]}; color:{$cfg[1]};'>{$cfg[2]}</span>";
}

$sym = htmlspecialchars($shop_symbol, ENT_QUOTES, "UTF-8");
include APP_PATH . "/views/includes/header.php";
?>

<style>
  :root {
    --dash-sidebar-w: 280px;
    --dash-bg: #f9f9f7;
    --dash-accent: var(--dark-green-colour, #072708);
    --dash-card-shadow: 0 4px 20px rgba(0,0,0,0.03);
  }
  .dash-wrapper {
    min-height: 100vh;
    background: var(--dash-bg);
    padding: 140px 0 80px;
  }
  .dash-container {
    display: grid;
    grid-template-columns: var(--dash-sidebar-w) 1fr;
    gap: 32px;
    align-items: start;
  }
  
  /* Sidebar Nav */
  .dash-nav {
    background: #fff;
    border-radius: 20px;
    padding: 16px;
    box-shadow: var(--dash-card-shadow);
    position: sticky;
    top: 120px;
    z-index: 100;
  }
  .dash-nav-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-radius: 12px;
    color: #666;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.25s ease;
    cursor: pointer;
    margin-bottom: 4px;
  }
  .dash-nav-link i { font-size: 18px; width: 22px; text-align: center; }
  .dash-nav-link:hover { background: #f4f6f4; color: var(--dash-accent); }
  .dash-nav-link.active { background: var(--dash-accent); color: #fff; box-shadow: 0 4px 12px rgba(7,39,8,0.15); }

  /* Content Cards */
  .dash-card {
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: var(--dash-card-shadow);
    margin-bottom: 24px;
    border: 1px solid rgba(0,0,0,0.01);
    animation: dashFadeIn 0.4s ease both;
  }
  @keyframes dashFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  .dash-heading { font-size: 24px; font-weight: 700; color: var(--dash-accent); margin-bottom: 24px; }
  
  /* Stats */
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
  .stat-card {
    background: #fff; border-radius: 20px; padding: 24px;
    display: flex; align-items: center; gap: 20px;
    box-shadow: var(--dash-card-shadow);
    border: 1px solid rgba(0,0,0,0.01);
  }
  .stat-icon {
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
  }
  .stat-info .label { font-size: 13px; color: #888; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
  .stat-info .value { font-size: 22px; font-weight: 800; color: var(--dash-accent); }

  /* Table Custom */
  .v-table-wrap { border-radius: 12px; }
  .v-table { width: 100%; border-collapse: collapse; }
  .v-table th { text-align: left; padding: 18px 14px; background: #f9f9f9; color: #888; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
  .v-table td { padding: 20px 14px; border-bottom: 1px solid #f1f1f1; font-size: 14px; }
  .v-badge { padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; }

  /* Order Mobile Cards */
  .order-mobile-list { display: none; flex-direction: column; gap: 16px; }
  .order-item-card { background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 20px; }
  .order-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #f5f5f5; padding-bottom: 12px; }
  .order-item-body { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .order-item-label { font-size: 12px; color: #888; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px; }
  .order-item-val { font-size: 14px; font-weight: 600; color: #333; }

  /* Modal */
  .v-modal {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000;
    display: none; align-items: center; justify-content: center; padding: 20px;
    backdrop-filter: blur(4px);
  }
  .v-modal.active { display: flex; }
  .v-modal-content {
    background: #fff; width: 100%; max-width: 550px; border-radius: 24px;
    padding: 36px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    max-height: 90vh; overflow-y: auto; position: relative;
  }
  .v-modal-close { position: absolute; top: 20px; right: 20px; font-size: 24px; cursor: pointer; color: #aaa; border: none; background: none; }

  /* Form Elements */
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
  .form-group { margin-bottom: 24px; }
  .form-label { 
    display: block; font-size: 11px; font-weight: 800; color: #999; 
    margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;
  }
  .form-input { 
    width: 100%; padding: 16px 20px; background: #fcfcfb;
    border: 1.5px solid #f0f0eb; border-radius: 14px;
    font-size: 14px; font-weight: 600; color: #333;
    outline: none; transition: all 0.25s ease; box-sizing: border-box;
  }
  .form-input:focus { background: #fff; border-color: var(--dash-accent); box-shadow: 0 0 0 5px rgba(7, 39, 8, 0.04); }
  .form-input:disabled { background: #f5f5f5; color: #aaa; cursor: not-allowed; }

  /* Buttons */
  .btn-save {
    background: var(--dash-accent); color: #fff; border: none; padding: 16px 32px;
    border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;
    font-size: 14px; display: inline-flex; align-items: center; gap: 8px; justify-content: center;
  }
  .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
  .btn-save:active { transform: scale(0.98); }

  /* Mobile Fixes */
  @media (max-width: 991px) {
    .v-table-wrap { display: none; }
    .order-mobile-list { display: flex; }
    
    .dash-wrapper { padding-top: 90px; padding-bottom: 40px; }
    .dash-container { grid-template-columns: 1fr; gap: 20px; }
    
    .dash-nav { 
      position: sticky; top: 60px; z-index: 1000;
      display: flex; overflow-x: auto; gap: 4px; padding: 8px; 
      background: #fff; border-radius: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      margin: 0 -20px 20px -20px; width: auto;
      scrollbar-width: none; 
    }
    .dash-nav::-webkit-scrollbar { display: none; }
    
    .dash-nav-link { 
      white-space: nowrap; padding: 10px 16px; font-size: 13px; border-radius: 10px;
      flex-shrink: 0; margin-bottom: 0; background: #f8f8f8; border: 1px solid #eee;
    }
    .dash-nav-link.active { background: var(--dash-accent); color: #fff; }
    
    .dash-card { padding: 24px 20px; }
    .dash-heading { font-size: 20px; }
    .stats-grid { grid-template-columns: 1fr; gap: 12px; }
    .form-row { grid-template-columns: 1fr; gap: 0; }
  }
</style>

<div class="dash-wrapper">
  <div class="container" style="max-width: 1200px;">
    
    <div class="dash-container">
      
      <!-- Dashboard Navigation -->
      <aside class="dash-nav" id="dashNav">
        <a class="dash-nav-link active" data-tab="overview"><i class="fas fa-th-large"></i> Overview</a>
        <a class="dash-nav-link" data-tab="orders"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a class="dash-nav-link" data-tab="profile"><i class="fas fa-user-circle"></i> Profile</a>
        <a class="dash-nav-link" data-tab="addresses"><i class="fas fa-map-marker-alt"></i> Addresses</a>
        <a class="dash-nav-link" data-tab="security"><i class="fas fa-shield-alt"></i> Security</a>
        <a href="<?= $baseUrl ?>/customer-logout" class="dash-nav-link" style="color: #c1121f; border-color: rgba(193,18,31,0.2);"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </aside>

      <!-- Main Content -->
      <main class="dash-content">
        
        <!-- Tab: Overview -->
        <div class="tab-pane active" id="tab-overview">
          <div class="dash-card">
            <h1 class="dash-heading" style="margin-bottom: 8px;">
              Hello, <?= htmlspecialchars($customer['input_firstname']) ?>!
              <?php if (($customer['input_verify'] ?? '0') === '1'): ?>
                <i class="fas fa-check-circle" style="color: #15803d; font-size: 18px; margin-left: 6px;" title="Verified Account"></i>
              <?php endif; ?>
            </h1>
            <p class="p-01 color-gray">Welcome to your account dashboard. Here you can manage your orders and update your information.</p>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon" style="background:#e8f5e9; color:#2e7d32;"><i class="fas fa-box"></i></div>
              <div class="stat-info"><div class="label">Orders</div><div class="value"><?= $totalOrders ?></div></div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background:#fff8e1; color:#f57c00;"><i class="fas fa-clock"></i></div>
              <div class="stat-info"><div class="label">Pending</div><div class="value"><?= $pendingOrders ?></div></div>
            </div>
            <div class="stat-card">
              <div class="stat-icon" style="background:#e3f2fd; color:#1976d2;"><i class="fas fa-wallet"></i></div>
              <div class="stat-info"><div class="label">Total Spent</div><div class="value"><?= formatPrice($spentAmount, $sym) ?></div></div>
            </div>
          </div>

          <div class="dash-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
              <h2 class="dash-heading" style="margin-bottom:0;">Recent Orders</h2>
              <a onclick="switchTab('orders')" style="cursor:pointer; color:var(--dash-accent); font-weight:700; font-size:13px;">VIEW ALL</a>
            </div>
            <?php if (empty($orders)): ?>
              <p class="p-01 color-gray">No orders found.</p>
            <?php else: ?>
              <!-- Desktop Table -->
              <div class="v-table-wrap">
                <table class="v-table">
                  <thead><tr><th>Order ID</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                  <tbody>
                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                      <tr>
                        <td style="font-weight:700; color:var(--dash-accent);">#<?= htmlspecialchars($order['hash_id']) ?></td>
                        <td><?= date('M d, Y', strtotime($order['date_created'])) ?></td>
                        <td><?= getStatusBadge($order['input_status']) ?></td>
                        <td style="font-weight:800;"><?= formatPrice($order['input_total'], $sym) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- Mobile Cards -->
              <div class="order-mobile-list">
                <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                  <div class="order-item-card">
                    <div class="order-item-header">
                      <div style="font-weight:800; color:var(--dash-accent);">#<?= htmlspecialchars($order['hash_id']) ?></div>
                      <?= getStatusBadge($order['input_status']) ?>
                    </div>
                    <div class="order-item-body">
                      <div><div class="order-item-label">Date</div><div class="order-item-val"><?= date('M d, Y', strtotime($order['date_created'])) ?></div></div>
                      <div style="text-align:right;"><div class="order-item-label">Amount</div><div class="order-item-val"><?= formatPrice($order['input_total'], $sym) ?></div></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Tab: Orders -->
        <div class="tab-pane" id="tab-orders" style="display:none;">
          <div class="dash-card">
            <h2 class="dash-heading">Order History</h2>
            <?php if (empty($orders)): ?>
              <div style="text-align:center; padding:60px 0;">
                <i class="fas fa-shopping-bag" style="font-size:64px; color:#f0f0ec; margin-bottom:20px;"></i>
                <p class="p-01 color-gray">You haven't placed any orders yet.</p>
                <a href="<?= $baseUrl ?>/products" class="btn-save" style="margin-top:20px; text-decoration:none;">Start Shopping</a>
              </div>
            <?php else: ?>
              <!-- Desktop Table -->
              <div class="v-table-wrap">
                <table class="v-table">
                  <thead><tr><th>Order ID</th><th>Date</th><th>Status</th><th>Method</th><th>Total</th></tr></thead>
                  <tbody>
                    <?php foreach ($orders as $order): ?>
                      <tr>
                        <td style="font-weight:700; color:var(--dash-accent);">#<?= htmlspecialchars($order['hash_id']) ?></td>
                        <td><?= date('M d, Y', strtotime($order['date_created'])) ?></td>
                        <td><?= getStatusBadge($order['input_status']) ?></td>
                        <td style="text-transform:capitalize; font-size:13px; color:#666;"><?= htmlspecialchars($order['select_payment_method'] ?? 'Online') ?></td>
                        <td style="font-weight:800;"><?= formatPrice($order['input_total'], $sym) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- Mobile Cards -->
              <div class="order-mobile-list">
                <?php foreach ($orders as $order): ?>
                  <div class="order-item-card">
                    <div class="order-item-header">
                      <div style="font-weight:800; color:var(--dash-accent);">#<?= htmlspecialchars($order['hash_id']) ?></div>
                      <?= getStatusBadge($order['input_status']) ?>
                    </div>
                    <div class="order-item-body">
                      <div><div class="order-item-label">Date</div><div class="order-item-val"><?= date('M d, Y', strtotime($order['date_created'])) ?></div></div>
                      <div style="text-align:right;"><div class="order-item-label">Method</div><div class="order-item-val" style="text-transform:capitalize;"><?= htmlspecialchars($order['select_payment_method'] ?? 'Online') ?></div></div>
                      <div style="grid-column:span 2; margin-top:8px; border-top:1px dashed #eee; padding-top:8px;">
                         <div class="order-item-label">Total Amount</div>
                         <div class="order-item-val" style="font-size:16px;"><?= formatPrice($order['input_total'], $sym) ?></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Tab: Profile -->
        <div class="tab-pane" id="tab-profile" style="display:none;">
          <div class="dash-card">
            <h2 class="dash-heading">Profile Settings</h2>
            <form id="profileForm" onsubmit="handleProfileUpdate(event)">
              <div class="form-row">
                <div class="form-group"><label class="form-label">First Name</label><input type="text" class="form-input" id="pFirstname" value="<?= htmlspecialchars($customer['input_firstname']) ?>" required></div>
                <div class="form-group"><label class="form-label">Last Name</label><input type="text" class="form-input" id="pLastname" value="<?= htmlspecialchars($customer['input_lastname']) ?>" required></div>
              </div>
              <div class="form-group"><label class="form-label">Email Address</label><input type="email" class="form-input" value="<?= htmlspecialchars($customer['input_email']) ?>" disabled><small style="color:#aaa; display:block; margin-top:6px;">Email address cannot be changed.</small></div>
              <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" class="form-input" id="pPhone" value="<?= htmlspecialchars($customer['input_phone']) ?>" placeholder="e.g. +234 800 000 0000"></div>
              <div id="profileRes" style="margin-bottom:20px;"></div>
              <button type="submit" id="profileBtn" class="btn-save">Save Changes</button>
            </form>
          </div>
        </div>

        <!-- Tab: Addresses -->
        <div class="tab-pane" id="tab-addresses" style="display:none;">
          <div class="dash-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:32px;">
              <h2 class="dash-heading" style="margin-bottom:0;">Address Book</h2>
              <button class="btn-save" style="padding:12px 24px; font-size:13px;" onclick="openAddressModal()"><i class="fas fa-plus"></i> Add New</button>
            </div>
            <?php if (empty($addresses)): ?>
              <div style="text-align:center; padding:50px 0; border:2px dashed #eee; border-radius:20px;"><i class="fas fa-map-marked-alt" style="font-size:48px; color:#f0f0ec; margin-bottom:16px;"></i><p class="p-01 color-gray">No addresses saved yet.</p></div>
            <?php else: ?>
              <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;" id="addressGrid">
                <?php foreach ($addresses as $addr): ?>
                  <div class="addr-card" id="addr-<?= $addr['hash_id'] ?>">
                    <?php if ($addr['input_is_default'] === '1'): ?><span class="addr-default-tag">DEFAULT</span><?php endif; ?>
                    <h3 style="font-size:16px; font-weight:700; margin-bottom:10px;"><?= htmlspecialchars($addr['input_label'] ?? 'Home') ?></h3>
                    <p style="font-size:14px; font-weight:600; margin-bottom:6px; color:#333;"><?= htmlspecialchars($addr['input_firstname'] . ' ' . $addr['input_lastname']) ?></p>
                    <p style="font-size:14px; color:#666; line-height:1.6; margin-bottom:16px;"><?= htmlspecialchars($addr['input_address']) ?><br><?= htmlspecialchars($addr['input_city']) ?>, <?= htmlspecialchars($addr['input_state']) ?><br><?= htmlspecialchars($addr['input_country']) ?> <?= htmlspecialchars($addr['input_postcode']) ?></p>
                    <div style="display:flex; gap:16px; border-top:1px solid #f5f5f5; padding-top:16px;"><a onclick="deleteAddress('<?= $addr['hash_id'] ?>')" style="color:#c1121f; font-size:13px; font-weight:700; cursor:pointer;"><i class="fas fa-trash-alt"></i> Delete</a></div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Tab: Security -->
        <div class="tab-pane" id="tab-security" style="display:none;">
          <div class="dash-card">
            <h2 class="dash-heading">Security</h2>
            <p class="p-01 color-gray" style="margin-bottom:32px;">Update your password to keep your account secure.</p>
            <form id="passwordForm" onsubmit="handlePasswordChange(event)">
              <div class="form-group"><label class="form-label">Current Password</label><input type="password" class="form-input" id="curPass" required placeholder="••••••••"></div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">New Password</label><input type="password" class="form-input" id="newPass" required placeholder="••••••••"></div>
                <div class="form-group"><label class="form-label">Confirm New Password</label><input type="password" class="form-input" id="cfmPass" required placeholder="••••••••"></div>
              </div>
              <div id="passRes" style="margin-bottom:20px;"></div>
              <button type="submit" id="passBtn" class="btn-save">Update Password</button>
            </form>
          </div>
        </div>

      </main>
    </div>
  </div>
</div>

<!-- Modal: Add Address -->
<div class="v-modal" id="addressModal">
  <div class="v-modal-content">
    <button class="v-modal-close" onclick="closeAddressModal()">✕</button>
    <h2 class="dash-heading" style="margin-bottom:28px;">Add New Address</h2>
    <form id="addressForm" onsubmit="handleAddressAdd(event)">
      <div class="form-group"><label class="form-label">Address Label</label><input type="text" class="form-input" id="addrLabel" placeholder="e.g. Home" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">First Name</label><input type="text" class="form-input" id="addrFirst" required></div>
        <div class="form-group"><label class="form-label">Last Name</label><input type="text" class="form-input" id="addrLast" required></div>
      </div>
      <div class="form-group"><label class="form-label">Street Address</label><input type="text" class="form-input" id="addrStreet" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">City</label><input type="text" class="form-input" id="addrCity" required></div>
        <div class="form-group"><label class="form-label">State</label><input type="text" class="form-input" id="addrState" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Country</label><input type="text" class="form-input" id="addrCountry" required></div>
        <div class="form-group"><label class="form-label">Postcode / ZIP</label><input type="text" class="form-input" id="addrPostcode"></div>
      </div>
      <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" class="form-input" id="addrPhone" required></div>
      <label style="display:flex; align-items:center; gap:10px; cursor:pointer; margin-bottom:24px;">
        <input type="checkbox" id="addrDefault" style="width:18px; height:18px; accent-color:var(--dash-accent);">
        <span style="font-size:14px; font-weight:600;">Set as default address</span>
      </label>
      <div id="addrRes" style="margin-bottom:20px;"></div>
      <button type="submit" id="addrBtn" class="btn-save" style="width:100%;">Save Address</button>
    </form>
  </div>
</div>

<script>
(function() {
  var base = window.VENORA_BASE_URL || '';
  window.switchTab = function(tabId) {
    document.querySelectorAll('.dash-nav-link').forEach(l => l.classList.toggle('active', l.dataset.tab === tabId));
    document.querySelectorAll('.tab-pane').forEach(p => p.style.display = (p.id === 'tab-' + tabId) ? 'block' : 'none');
    if (window.innerWidth < 991) {
      const activeLink = document.querySelector('.dash-nav-link.active');
      if (activeLink) activeLink.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      window.scrollTo({ top: document.querySelector('.dash-content').offsetTop - 110, behavior: 'smooth' });
    }
  };
  document.querySelectorAll('.dash-nav-link[data-tab]').forEach(link => {
    link.addEventListener('click', function() { switchTab(this.dataset.tab); });
  });
  window.handleProfileUpdate = function(e) {
    e.preventDefault();
    var btn = document.getElementById('profileBtn'), res = document.getElementById('profileRes');
    btn.disabled = true; btn.textContent = 'Saving...';
    fetch(base + '/customer-update-profile', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({
        firstname: document.getElementById('pFirstname').value.trim(),
        lastname:  document.getElementById('pLastname').value.trim(),
        phone:     document.getElementById('pPhone').value.trim()
      })
    }).then(r => r.json()).then(d => {
      res.innerHTML = d.success ? '<div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:10px; font-size:14px; font-weight:600;">✓ Profile updated!</div>' : '<div style="background:#ffebee; color:#c62828; padding:12px; border-radius:10px; font-size:14px; font-weight:600;">✗ ' + d.message + '</div>';
    }).finally(() => { btn.disabled = false; btn.textContent = 'Save Changes'; });
  };
  window.handlePasswordChange = function(e) {
    e.preventDefault();
    var btn = document.getElementById('passBtn'), res = document.getElementById('passRes');
    var n1 = document.getElementById('newPass').value, n2 = document.getElementById('cfmPass').value;
    if (n1 !== n2) { res.innerHTML = '<div style="color:#c62828; font-size:14px; font-weight:600;">✗ Passwords do not match.</div>'; return; }
    btn.disabled = true; btn.textContent = 'Updating...';
    fetch(base + '/customer-change-password', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ current_password: document.getElementById('curPass').value, new_password: n1 })
    }).then(r => r.json()).then(d => {
      if (d.success) { res.innerHTML = '<div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:10px; font-size:14px; font-weight:600;">✓ Password updated!</div>'; document.getElementById('passwordForm').reset(); }
      else { res.innerHTML = '<div style="background:#ffebee; color:#c62828; padding:12px; border-radius:10px; font-size:14px; font-weight:600;">✗ ' + d.message + '</div>'; }
    }).finally(() => { btn.disabled = false; btn.textContent = 'Update Password'; });
  };
  window.openAddressModal  = () => document.getElementById('addressModal').classList.add('active');
  window.closeAddressModal = () => { document.getElementById('addressModal').classList.remove('active'); document.getElementById('addressForm').reset(); };
  window.handleAddressAdd = function(e) {
    e.preventDefault();
    var btn = document.getElementById('addrBtn'), res = document.getElementById('addrRes');
    btn.disabled = true; btn.textContent = 'Saving...';
    fetch(base + '/customer-address-add', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({
        label: document.getElementById('addrLabel').value.trim(),
        firstname: document.getElementById('addrFirst').value.trim(),
        lastname: document.getElementById('addrLast').value.trim(),
        address: document.getElementById('addrStreet').value.trim(),
        city: document.getElementById('addrCity').value.trim(),
        state: document.getElementById('addrState').value.trim(),
        country: document.getElementById('addrCountry').value.trim(),
        postcode: document.getElementById('addrPostcode').value.trim(),
        phone: document.getElementById('addrPhone').value.trim(),
        is_default: document.getElementById('addrDefault').checked
      })
    }).then(r => r.json()).then(d => { if (d.success) { location.reload(); } else { res.innerHTML = '<div style="color:#c62828; font-weight:600;">✗ ' + d.message + '</div>'; btn.disabled = false; btn.textContent = 'Save Address'; } });
  };
  window.deleteAddress = function(hid) {
    if (!confirm('Delete this address?')) return;
    fetch(base + '/customer-address-delete', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ hash_id: hid })
    }).then(r => r.json()).then(d => { if (d.success) { document.getElementById('addr-' + hid).remove(); } });
  };
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
