<?php
$page_title = "Reset Password";
$bodyClass  = "page-light-navbar";

$token    = htmlspecialchars(trim($_GET['token'] ?? ''), ENT_QUOTES, 'UTF-8');
$isValid  = false;
$errorMsg = '';

if (empty($token)) {
    $errorMsg = 'No reset token provided.';
} else {
    // Check token exists, is not used, and is not expired (1 hour window)
    // Updated to use the correct 'verify' table and 'token' column
    $resets = selectContent($conn, 'verify', [
        'token'      => $token,
        'token_type' => 'password_reset',
        'visibility' => 'show',
    ]);

    if (empty($resets)) {
        $errorMsg = 'This reset link is invalid or has already been used.';
    } else {
        $reset = $resets[0];
        // Check expiry: token_expiry from DB
        if (strtotime($reset['token_expiry']) < time()) {
            $errorMsg = 'This reset link has expired. Please request a new one.';
        } else {
            $isValid = true;
        }
    }
}

include APP_PATH . "/views/includes/header.php";
?>

<div style="height:100px;background:#f9f9f7;"></div>
<div style="min-height:calc(80vh - 100px);background:#f9f9f7;padding-bottom:80px;display:flex;align-items:center;">
  <div class="container" style="max-width:480px;margin:0 auto;padding:0 20px;">

    <?php if (!$isValid): ?>

      <!-- Invalid / expired token -->
      <div style="background:#fff;border-radius:14px;padding:48px 36px;box-shadow:0 1px 12px rgba(7,39,8,0.07);text-align:center;">
        <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <h1 class="heading-02" style="margin-bottom:12px;color:#072708;">Link Expired or Invalid</h1>
        <p class="p-01 color-gray" style="margin-bottom:32px;">
          <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
        </p>
        <a href="<?= $baseUrl ?>/customer-forgot-password"
           style="display:inline-block;padding:14px 28px;background:#072708;color:#fff;text-decoration:none;border-radius:7px;font-weight:600;font-size:15px;font-family:inherit;margin-bottom:12px;">
          Request New Link
        </a>
        <br>
        <a href="<?= $baseUrl ?>/customer-login" class="p-01"
           style="color:#072708;font-size:14px;text-decoration:underline;">
          Back to Sign In
        </a>
      </div>

    <?php else: ?>

      <!-- Valid token — show reset form -->
      <div style="text-align:center;margin-bottom:32px;">
        <h1 class="heading-02" style="margin-bottom:10px;">Set New Password</h1>
        <p class="p-01 color-gray">Choose a strong password for your account.</p>
      </div>

      <div style="background:#fff;border-radius:12px;padding:36px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">

        <div id="resetFormWrap">
          <form id="resetForm" onsubmit="handleReset(event)">
            <input type="hidden" id="resetToken" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

            <div style="margin-bottom:20px;">
              <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">New password</label>
              <input type="password" name="password" id="resetPassword"
                     style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                     placeholder="Min. 8 characters" required>
            </div>

            <div style="margin-bottom:24px;">
              <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Confirm new password</label>
              <input type="password" name="confirm_password" id="resetConfirm"
                     style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                     placeholder="Repeat password" required>
            </div>

            <div id="resetError"
                 style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 14px;border-radius:7px;margin-bottom:16px;font-size:14px;">
            </div>

            <button type="submit" id="resetBtn"
                    style="width:100%;padding:14px;background:#072708;color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer;">
              Reset Password
            </button>

          </form>
        </div>

        <div id="resetSuccess" style="display:none;text-align:center;padding:12px 0;">
          <div style="width:54px;height:54px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
          <h3 class="heading-06" style="margin-bottom:8px;color:#072708;">Password Reset Successfully</h3>
          <p class="p-01 color-gray" style="margin-bottom:24px;">You can now sign in with your new password.</p>
          <a href="<?= $baseUrl ?>/customer-login"
             style="display:inline-block;padding:12px 32px;background:#072708;color:#fff;text-decoration:none;border-radius:7px;font-weight:600;font-size:15px;">
            Sign In Now
          </a>
        </div>

      </div>

    <?php endif; ?>

  </div>
</div>

<script>
(function () {
  var base = window.VENORA_BASE_URL || '';

  window.handleReset = function (e) {
    e.preventDefault();
    var errBox  = document.getElementById('resetError');
    var btn     = document.getElementById('resetBtn');
    var form    = document.getElementById('resetForm');
    var wrap    = document.getElementById('resetFormWrap');
    var success = document.getElementById('resetSuccess');
    
    errBox.style.display = 'none';

    var pass    = document.getElementById('resetPassword').value;
    var confirm = document.getElementById('resetConfirm').value;

    if (pass.length < 8) {
      errBox.textContent = 'Password must be at least 8 characters.';
      errBox.style.display = 'block';
      return;
    }
    if (pass !== confirm) {
      errBox.textContent = 'Passwords do not match.';
      errBox.style.display = 'block';
      return;
    }

    btn.textContent = 'Processing…';
    btn.disabled = true;

    var payload = {
      token:    document.getElementById('resetToken').value,
      password: pass,
      confirm_password: confirm
    };

    fetch(base + '/customer-reset-submit', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.success) {
        wrap.style.display    = 'none';
        success.style.display = 'block';
      } else {
        errBox.textContent   = res.message || 'Something went wrong. Please try again.';
        errBox.style.display = 'block';
        btn.textContent = 'Reset Password';
        btn.disabled = false;
      }
    })
    .catch(function () {
      errBox.textContent   = 'Connection error. Please try again.';
      errBox.style.display = 'block';
      btn.textContent = 'Reset Password';
      btn.disabled = false;
    });
  };
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
