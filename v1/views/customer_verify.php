<?php
$page_title = "Verify Email";
$bodyClass  = "page-light-navbar";

$token   = htmlspecialchars(trim($_GET['token'] ?? ''), ENT_QUOTES, 'UTF-8');
$success = false;
$error   = '';

if (empty($token)) {
    $error = 'No verification token provided.';
} else {
    // Look up the token in the verify table
    $verifyRows = selectContent($conn, 'verify', ['verify_token' => $token, 'visibility' => 'show']);

    if (empty($verifyRows)) {
        $error = 'This verification link is invalid or has already been used.';
    } else {
        $verifyRow = $verifyRows[0];
        $userHash  = $verifyRow['tb_link'];

        // Look up the user
        $users = selectContent($conn, 'read_users', ['hash_id' => $userHash, 'visibility' => 'show']);

        if (empty($users)) {
            $error = 'User account not found.';
        } else {
            // Mark user as verified
            updateContent($conn, 'read_users',
                ['input_verify' => '1'],
                ['hash_id' => $userHash]
            );

            // Delete the verify row
            deleteContent($conn, 'verify', ['hash_id' => $verifyRow['hash_id']]);

            $success = true;
        }
    }
}

include APP_PATH . "/views/includes/header.php";
?>

<section style="min-height:80vh;background:#f9f9f7;padding-top:120px;padding-bottom:80px;display:flex;align-items:center;">
  <div class="container" style="max-width:480px;margin:0 auto;padding:0 20px;text-align:center;">

    <?php if ($success): ?>
      <!-- Success state -->
      <div style="background:#fff;border-radius:14px;padding:48px 36px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">
        <div style="width:64px;height:64px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 class="heading-02" style="margin-bottom:12px;color:#072708;">Email Verified!</h1>
        <p class="p-01 color-gray" style="margin-bottom:32px;">
          Your account has been successfully verified. You can now sign in and start shopping.
        </p>
        <a href="<?= $baseUrl ?>/customer-login"
           style="display:inline-block;padding:14px 32px;background:#072708;color:#fff;text-decoration:none;border-radius:7px;font-weight:600;font-size:15px;font-family:inherit;">
          Sign In to Your Account
        </a>
      </div>

    <?php else: ?>
      <!-- Error state -->
      <div style="background:#fff;border-radius:14px;padding:48px 36px;box-shadow:0 1px 12px rgba(7,39,8,0.07);">
        <div style="width:64px;height:64px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <h1 class="heading-02" style="margin-bottom:12px;color:#072708;">Verification Failed</h1>
        <p class="p-01 color-gray" style="margin-bottom:32px;">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
        <a href="<?= $baseUrl ?>/customer-login"
           style="display:inline-block;padding:14px 32px;background:#072708;color:#fff;text-decoration:none;border-radius:7px;font-weight:600;font-size:15px;font-family:inherit;">
          Back to Sign In
        </a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php include APP_PATH . "/views/includes/footer.php"; ?>
