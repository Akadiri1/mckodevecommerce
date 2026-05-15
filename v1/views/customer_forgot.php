<?php
$page_title = "Forgot Password";
$bodyClass  = "page-light-navbar";

include APP_PATH . "/views/includes/header.php";
?>

<div style="height:100px;background:#f9f9f7;"></div>
<div style="min-height:calc(80vh - 100px);background:#f9f9f7;padding-bottom:80px;display:flex;align-items:center;">
  <div class="container" style="max-width:480px;margin:0 auto;padding:0 20px;">

    <div style="text-align:center;margin-bottom:36px;">
      <h1 class="heading-02" style="margin-bottom:10px;">Forgot Password?</h1>
      <p class="p-01 color-gray">Enter your email and we'll send you a reset link.</p>
    </div>

    <div style="background:#fff;border-radius:12px;padding:36px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">

      <form id="forgotForm" onsubmit="handleForgot(event)">

        <div style="margin-bottom:24px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Email address</label>
          <input type="email" name="email" id="forgotEmail"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                 placeholder="you@example.com" required>
        </div>

        <div id="forgotError"
             style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 14px;border-radius:7px;margin-bottom:16px;font-size:14px;">
        </div>
        <div id="forgotSuccess"
             style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 14px;border-radius:7px;margin-bottom:16px;font-size:14px;">
        </div>

        <button type="submit" id="forgotBtn"
                style="width:100%;padding:14px;background:#072708;color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer;">
          Send Reset Link
        </button>

      </form>

      <div style="text-align:center;margin-top:20px;">
        <a href="<?= $baseUrl ?>/customer-login" class="p-01"
           style="color:#072708;text-decoration:underline;font-size:14px;">
          Back to Sign In
        </a>
      </div>

    </div>
  </div>
</div>

<script>
(function () {
  var base = window.VENORA_BASE_URL || '';

  window.handleForgot = function (e) {
    e.preventDefault();
    var errBox  = document.getElementById('forgotError');
    var succBox = document.getElementById('forgotSuccess');
    var btn     = document.getElementById('forgotBtn');
    var form    = document.getElementById('forgotForm');
    errBox.style.display  = 'none';
    succBox.style.display = 'none';

    btn.textContent = 'Sending…';
    btn.disabled = true;

    var payload = { email: document.getElementById('forgotEmail').value.trim() };

    fetch(base + '/customer-forgot-submit', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      // Always show success message for security
      form.style.display    = 'none';
      succBox.textContent   = "If this email is registered, you'll receive a password reset link shortly. Please also check your spam folder.";
      succBox.style.display = 'block';
    })
    .catch(function () {
      errBox.textContent   = 'Something went wrong. Please try again.';
      errBox.style.display = 'block';
      btn.textContent = 'Send Reset Link';
      btn.disabled = false;
    });
  };
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
