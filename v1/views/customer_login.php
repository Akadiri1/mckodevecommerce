<?php
$page_title = "My Account";
$bodyClass  = "page-light-navbar";

// If already logged in, go to dashboard
if (!empty($_SESSION['customer_id'])) {
    header("Location: " . $baseUrl . "/customer-dashboard");
    exit;
}

include APP_PATH . "/views/includes/header.php";
?>

<div style="height:100px;background:#f9f9f7;"></div>
<div style="min-height:calc(100vh - 100px);background:#f9f9f7;padding-bottom:80px;">
  <div class="container" style="max-width:520px;margin:0 auto;padding:0 20px;">

    <!-- Heading -->
    <div style="text-align:center;margin-bottom:36px;">
      <h1 class="heading-02" style="margin-bottom:10px;">My Account</h1>
      <p class="p-01 color-gray">Sign in or create a new account.</p>
    </div>

    <!-- Tabs -->
    <div style="display:flex;border-bottom:2px solid #e8e8e3;margin-bottom:32px;">
      <button id="tabLoginBtn"
              onclick="switchTab('login')"
              style="flex:1;padding:12px;background:none;border:none;cursor:pointer;font-family:inherit;font-size:15px;font-weight:600;color:#072708;border-bottom:2px solid #072708;margin-bottom:-2px;transition:all 0.2s;">
        Sign In
      </button>
      <button id="tabRegisterBtn"
              onclick="switchTab('register')"
              style="flex:1;padding:12px;background:none;border:none;cursor:pointer;font-family:inherit;font-size:15px;font-weight:600;color:#888;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all 0.2s;">
        Create Account
      </button>
    </div>

    <!-- Login Form -->
    <div id="tabLogin">
      <form id="loginForm" onsubmit="handleLogin(event)"
            style="background:#fff;border-radius:12px;padding:32px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">

        <div style="margin-bottom:20px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Email address</label>
          <input type="email" name="email" id="loginEmail"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;transition:border-color 0.2s;"
                 placeholder="you@example.com" required>
        </div>

        <div style="margin-bottom:8px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Password</label>
          <input type="password" name="password" id="loginPassword"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;transition:border-color 0.2s;"
                 placeholder="••••••••" required>
        </div>

        <div style="text-align:right;margin-bottom:24px;">
          <a href="<?= $baseUrl ?>/customer-forgot-password" class="p-01"
             style="color:#072708;font-size:13px;text-decoration:underline;">Forgot password?</a>
        </div>

        <div id="loginError"
             style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 14px;border-radius:7px;margin-bottom:18px;font-size:14px;">
        </div>

        <button type="submit" id="loginBtn"
                style="width:100%;padding:14px;background:#072708;color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer;transition:opacity 0.2s;">
          Sign In
        </button>

      </form>
    </div>

    <!-- Register Form -->
    <div id="tabRegister" style="display:none;">
      <form id="registerForm" onsubmit="handleRegister(event)"
            style="background:#fff;border-radius:12px;padding:32px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">First name</label>
            <input type="text" name="firstname" id="regFirstname"
                   style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                   placeholder="Jane" required>
          </div>
          <div>
            <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Last name</label>
            <input type="text" name="lastname" id="regLastname"
                   style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                   placeholder="Doe" required>
          </div>
        </div>

        <div style="margin-bottom:20px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Email address</label>
          <input type="email" name="email" id="regEmail"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                 placeholder="you@example.com" required>
        </div>

        <div style="margin-bottom:20px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Password</label>
          <input type="password" name="password" id="regPassword"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                 placeholder="Min. 8 characters" required>
        </div>

        <div style="margin-bottom:24px;">
          <label class="p-01" style="display:block;margin-bottom:6px;font-weight:500;">Confirm password</label>
          <input type="password" name="confirm_password" id="regConfirm"
                 style="width:100%;padding:12px 14px;border:1.5px solid #ddd;border-radius:7px;font-family:inherit;font-size:15px;box-sizing:border-box;"
                 placeholder="Repeat password" required>
        </div>

        <div id="registerError"
             style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 14px;border-radius:7px;margin-bottom:18px;font-size:14px;">
        </div>

        <button type="submit" id="registerBtn"
                style="width:100%;padding:14px;background:#072708;color:#fff;border:none;border-radius:7px;font-family:inherit;font-size:15px;font-weight:600;cursor:pointer;transition:opacity 0.2s;">
          Create Account
        </button>

      </form>

      <!-- Success box is OUTSIDE the form so it stays visible when form is hidden -->
      <div id="registerSuccess"
           style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:20px 24px;border-radius:12px;margin-top:16px;font-size:15px;text-align:center;">
        <div style="font-size:32px;margin-bottom:10px;">✉️</div>
        <div id="registerSuccessText">Please check your email to verify your account.</div>
        <a href="<?= $baseUrl ?>/customer-login"
           style="display:inline-block;margin-top:16px;color:#072708;font-weight:600;font-size:14px;text-decoration:underline;">
          Back to Sign In
        </a>
      </div>
    </div>

  </div>
</div>

<script>
(function () {
  var base = window.VENORA_BASE_URL || '';

  // ── Tab switching ────────────────────────────────────────────
  window.switchTab = function (tab) {
    var isLogin = tab === 'login';
    document.getElementById('tabLogin').style.display    = isLogin ? 'block' : 'none';
    document.getElementById('tabRegister').style.display = isLogin ? 'none'  : 'block';

    var loginBtn    = document.getElementById('tabLoginBtn');
    var registerBtn = document.getElementById('tabRegisterBtn');
    loginBtn.style.color        = isLogin ? '#072708' : '#888';
    loginBtn.style.borderBottom = isLogin ? '2px solid #072708' : '2px solid transparent';
    registerBtn.style.color        = isLogin ? '#888'  : '#072708';
    registerBtn.style.borderBottom = isLogin ? '2px solid transparent' : '2px solid #072708';
  };

  // ── Login handler ────────────────────────────────────────────
  window.handleLogin = function (e) {
    e.preventDefault();
    var errBox = document.getElementById('loginError');
    var btn    = document.getElementById('loginBtn');
    errBox.style.display = 'none';
    btn.textContent = 'Signing in…';
    btn.disabled = true;

    var payload = {
      email:    document.getElementById('loginEmail').value.trim(),
      password: document.getElementById('loginPassword').value
    };

    fetch(base + '/customer-login-submit', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.success) {
        window.location.href = base + '/customer-dashboard';
      } else {
        errBox.textContent   = res.message || 'Invalid email or password.';
        errBox.style.display = 'block';
        btn.textContent = 'Sign In';
        btn.disabled = false;
      }
    })
    .catch(function () {
      errBox.textContent   = 'Something went wrong. Please try again.';
      errBox.style.display = 'block';
      btn.textContent = 'Sign In';
      btn.disabled = false;
    });
  };

  // ── Register handler ─────────────────────────────────────────
  window.handleRegister = function (e) {
    e.preventDefault();
    var errBox  = document.getElementById('registerError');
    var succBox = document.getElementById('registerSuccess');
    var btn     = document.getElementById('registerBtn');
    errBox.style.display  = 'none';
    succBox.style.display = 'none';

    var pass    = document.getElementById('regPassword').value;
    var confirm = document.getElementById('regConfirm').value;

    if (pass !== confirm) {
      errBox.textContent   = 'Passwords do not match.';
      errBox.style.display = 'block';
      return;
    }
    if (pass.length < 8) {
      errBox.textContent   = 'Password must be at least 8 characters.';
      errBox.style.display = 'block';
      return;
    }

    btn.textContent = 'Creating account…';
    btn.disabled = true;

    var payload = {
      firstname:        document.getElementById('regFirstname').value.trim(),
      lastname:         document.getElementById('regLastname').value.trim(),
      email:            document.getElementById('regEmail').value.trim(),
      password:         pass,
      confirm_password: confirm
    };

    fetch(base + '/customer-register-submit', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res.success) {
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('registerSuccessText').textContent = res.message || 'Please check your email to verify your account.';
        succBox.style.display = 'block';
      } else {
        errBox.textContent   = res.message || 'Registration failed. Please try again.';
        errBox.style.display = 'block';
        btn.textContent = 'Create Account';
        btn.disabled = false;
      }
    })
    .catch(function () {
      errBox.textContent   = 'Something went wrong. Please try again.';
      errBox.style.display = 'block';
      btn.textContent = 'Create Account';
      btn.disabled = false;
    });
  };
})();
</script>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
