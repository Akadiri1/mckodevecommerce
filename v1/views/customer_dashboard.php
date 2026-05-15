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
$customerHash = $_SESSION['customer_hash'] ?? '';

$users = selectContent($conn, 'read_users', ['id' => $customerId, 'visibility' => 'show']);
if (empty($users)) {
    // Session invalid — log out
    unset($_SESSION['customer_id'], $_SESSION['customer_hash'], $_SESSION['customer_name']);
    header('Location: ' . $baseUrl . '/customer-login');
    exit;
}
$customer = $users[0];

// Fetch orders for this customer
$orders = selectContentDesc($conn, 'read_orders', ['input_email' => $customer['input_email'], 'visibility' => 'show'], 'id', 50);

// Order statistics
$totalOrders    = count($orders);
$pendingOrders  = 0;
$deliveredOrders = 0;
foreach ($orders as $o) {
    $st = strtolower($o['input_status'] ?? '');
    if (in_array($st, ['pending', 'processing', 'paid'])) $pendingOrders++;
    if ($st === 'delivered') $deliveredOrders++;
}

// Status badge helper
function statusBadge($status) {
    $st = strtolower($status ?? 'pending');
    $map = [
        'paid'       => ['#dcfce7', '#15803d', 'Paid'],
        'pending'    => ['#fef9c3', '#854d0e', 'Pending'],
        'processing' => ['#dbeafe', '#1d4ed8', 'Processing'],
        'shipped'    => ['#ede9fe', '#6d28d9', 'Shipped'],
        'delivered'  => ['#dcfce7', '#15803d', 'Delivered'],
        'cancelled'  => ['#fee2e2', '#b91c1c', 'Cancelled'],
        'refunded'   => ['#f3f4f6', '#374151', 'Refunded'],
    ];
    $cfg = $map[$st] ?? ['#f3f4f6', '#374151', ucfirst($st)];
    return "<span style='background:{$cfg[0]};color:{$cfg[1]};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap;'>{$cfg[2]}</span>";
}

include APP_PATH . "/views/includes/header.php";
?>

<section style="min-height:100vh;background:#f9f9f7;padding-top:120px;padding-bottom:80px;">
  <div class="container" style="max-width:1000px;margin:0 auto;padding:0 20px;">

    <!-- Header row -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:36px;">
      <div>
        <h1 class="heading-02" style="margin-bottom:4px;">
          Welcome back, <?= htmlspecialchars($customer['input_firstname'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="p-01 color-gray"><?= htmlspecialchars($customer['input_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <a href="<?= $baseUrl ?>/customer-logout"
         style="padding:10px 22px;border:1.5px solid #072708;background:#fff;color:#072708;border-radius:7px;font-family:inherit;font-size:14px;font-weight:600;text-decoration:none;transition:background 0.2s;"
         onmouseover="this.style.background='#072708';this.style.color='#fff';"
         onmouseout="this.style.background='#fff';this.style.color='#072708';">
        Sign Out
      </a>
    </div>

    <!-- Stat cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:40px;">
      <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <div class="p-01 color-gray" style="margin-bottom:6px;">Total Orders</div>
        <div style="font-size:32px;font-weight:700;color:#072708;"><?= $totalOrders ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <div class="p-01 color-gray" style="margin-bottom:6px;">Pending</div>
        <div style="font-size:32px;font-weight:700;color:#b45309;"><?= $pendingOrders ?></div>
      </div>
      <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <div class="p-01 color-gray" style="margin-bottom:6px;">Delivered</div>
        <div style="font-size:32px;font-weight:700;color:#15803d;"><?= $deliveredOrders ?></div>
      </div>
    </div>

    <!-- Two-column layout -->
    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

      <!-- Orders table -->
      <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h2 class="heading-03" style="margin-bottom:20px;">Order History</h2>

        <?php if (empty($orders)): ?>
          <div style="text-align:center;padding:40px 20px;color:#888;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:12px;">
              <rect x="2" y="3" width="20" height="14" rx="2"/>
              <path d="M8 21h8M12 17v4"/>
            </svg>
            <p class="p-01 color-gray">No orders yet.</p>
            <a href="<?= $baseUrl ?>/products" style="color:#072708;font-weight:600;text-decoration:underline;">Start shopping</a>
          </div>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
              <thead>
                <tr style="border-bottom:2px solid #f0f0ec;">
                  <th style="text-align:left;padding:10px 8px;color:#888;font-weight:600;">Order ID</th>
                  <th style="text-align:left;padding:10px 8px;color:#888;font-weight:600;">Date</th>
                  <th style="text-align:right;padding:10px 8px;color:#888;font-weight:600;">Total</th>
                  <th style="text-align:center;padding:10px 8px;color:#888;font-weight:600;">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                  <tr style="border-bottom:1px solid #f5f5f2;">
                    <td style="padding:12px 8px;font-weight:600;color:#072708;">
                      #<?= htmlspecialchars($order['hash_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td style="padding:12px 8px;color:#555;">
                      <?= htmlspecialchars($order['date_created'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td style="padding:12px 8px;text-align:right;font-weight:600;">
                      <?= htmlspecialchars($shop_symbol, ENT_QUOTES, 'UTF-8') ?><?= htmlspecialchars(number_format((float)($order['input_total'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td style="padding:12px 8px;text-align:center;">
                      <?= statusBadge($order['input_status'] ?? 'pending') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Profile form -->
      <div style="background:#fff;border-radius:12px;padding:28px;box-shadow:0 1px 8px rgba(7,39,8,0.06);">
        <h2 class="heading-03" style="margin-bottom:20px;">Profile</h2>

        <form id="profileForm" onsubmit="handleProfileUpdate(event)">

          <div style="margin-bottom:16px;">
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">First name</label>
            <input type="text" name="firstname" id="pFirstname"
                   value="<?= htmlspecialchars($customer['input_firstname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   style="width:100%;padding:11px 13px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:14px;box-sizing:border-box;"
                   required>
          </div>

          <div style="margin-bottom:16px;">
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Last name</label>
            <input type="text" name="lastname" id="pLastname"
                   value="<?= htmlspecialchars($customer['input_lastname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   style="width:100%;padding:11px 13px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:14px;box-sizing:border-box;"
                   required>
          </div>

          <div style="margin-bottom:16px;">
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Email</label>
            <input type="email"
                   value="<?= htmlspecialchars($customer['input_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   style="width:100%;padding:11px 13px;border:1.5px solid #e8e8e3;border-radius:7px;font-family:inherit;font-size:14px;box-sizing:border-box;background:#f5f5f2;color:#888;"
                   disabled>
            <span style="font-size:12px;color:#aaa;">Email cannot be changed.</span>
          </div>

          <div style="margin-bottom:20px;">
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Phone</label>
            <input type="tel" name="phone" id="pPhone"
                   value="<?= htmlspecialchars($customer['input_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   style="width:100%;padding:11px 13px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:14px;box-sizing:border-box;"
                   placeholder="+1 (555) 000-0000">
          </div>

          <div id="profileError"
               style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:10px 12px;border-radius:7px;margin-bottom:14px;font-size:13px;">
          </div>
          <div id="profileSuccess"
               style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 12px;border-radius:7px;margin-bottom:14px;font-size:13px;">
          </div>

          <button type="submit" id="profileBtn"
                  style="width:100%;padding:12px;background:#072708;color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;">
            Save Changes
          </button>

        </form>
      </div>

    </div>
  </div>
</section>

<script>
(function () {
  var base = window.VENORA_BASE_URL || '';

  window.handleProfileUpdate = function (e) {
    e.preventDefault();
    var errBox  = document.getElementById('profileError');
    var succBox = document.getElementById('profileSuccess');
    var btn     = document.getElementById('profileBtn');
    errBox.style.display  = 'none';
    succBox.style.display = 'none';

    btn.textContent = 'Saving…';
    btn.disabled = true;

    var payload = {
      firstname: document.getElementById('pFirstname').value.trim(),
      lastname:  document.getElementById('pLastname').value.trim(),
      phone:     document.getElementById('pPhone').value.trim()
    };

    fetch(base + '/customer-update-profile', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.success) {
        succBox.textContent   = 'Profile updated successfully.';
        succBox.style.display = 'block';
      } else {
        errBox.textContent   = res.message || 'Update failed. Please try again.';
        errBox.style.display = 'block';
      }
    })
    .catch(function () {
      errBox.textContent   = 'Something went wrong. Please try again.';
      errBox.style.display = 'block';
    })
    .finally(function () {
      btn.textContent = 'Save Changes';
      btn.disabled = false;
    });
  };
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
