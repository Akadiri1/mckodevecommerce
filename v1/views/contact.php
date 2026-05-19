<?php
$page_title = "Contact";
$bodyClass  = "page-light-navbar";

function safeContactFetch($conn, $table, $where = []) {
    try {
        $conn->query("SELECT 1 FROM `$table` LIMIT 1");
        return selectContent($conn, $table, $where);
    } catch (Exception $e) { return []; }
}

$contactSettingsArr = safeContactFetch($conn, "settings_shop_contact", ["visibility" => "show"]);
$contactSettings    = !empty($contactSettingsArr) ? $contactSettingsArr[0] : [];

$igIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/6919161913a76fb895b4d2c8_ph_instagram-logo-fill.svg";
$faqIcon = "https://cdn.prod.website-files.com/6918bd445678e83950693c7b/691a355a71a4c2e99364e031_Group 1171274886.svg";
$galleryImgs = [
    ["https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3a62dfb2e1c46b3d2e_Rectangle 1105.avif","Close-up of a young woman with glowing, dewy skin"],
    ["https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b7f0a63c03c952cf3_Rectangle 1101.avif","Close-up of a woman applying skincare serum"],
    ["https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b8256954751ac9026_Rectangle 1102.avif","Close-up of a woman with wavy brown hair smiling softly"],
    ["https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b0e2c8d3d0bd0253c_Rectangle 1103.avif","Close-up of a woman with long wavy brown hair"],
    ["https://cdn.prod.website-files.com/6918bd445678e83950693c7b/69193c3b26fef69889644991_Rectangle 1104.avif","Smiling woman with long dark hair and pearl earrings"],
];

include APP_PATH . "/views/includes/header.php";
?>

<!-- [cbcode_50001o] -->

<!-- CONTACT PAGE -->
<div data-cbsection="cb1">
<!-- [cbcode_50001Contacto] -->
<section class="contact section-0-120">
  <div class="container">
    <div class="venora-contact-grid">

      <!-- Left: contact info -->
      <div class="venora-contact-info">
        <h2 class="heading-02"
            data-admc-manage="settings_shop_contact"
            data-admc-id="<?= $contactSettings['id'] ?? 1 ?>">
          <?= htmlspecialchars($contactSettings['input_heading'] ?? 'Get in touch', ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="p-01 color-gray" style="margin-top:12px;margin-bottom:36px;"
           data-admc-manage="settings_shop_contact"
           data-admc-id="<?= $contactSettings['id'] ?? 1 ?>">
          <?= htmlspecialchars($contactSettings['text_description'] ?? "We'd love to hear from you. Send us a message and we'll get back to you as soon as possible.", ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div class="contact-box-wrap">
          <div class="contact-box">
            <div class="color-gray"><div class="p-02">Email</div></div>
            <a class="contact-link w-inline-block"
               href="mailto:<?= htmlspecialchars($shop_email ?? 'info@venora.com', ENT_QUOTES, 'UTF-8') ?>"
               data-admc-manage="settings_shop_config"
               data-admc-id="<?= $shopConfig[0]['id'] ?? 1 ?>">
              <div class="heading-05"><?= htmlspecialchars($shop_email ?? 'info@venora.com', ENT_QUOTES, 'UTF-8') ?></div>
            </a>
          </div>
          <div class="contact-box">
            <div class="color-gray"><div class="p-02">Phone</div></div>
            <a class="contact-link w-inline-block"
               href="tel:<?= htmlspecialchars($shop_phone ?? '+1(555)123-4567', ENT_QUOTES, 'UTF-8') ?>"
               data-admc-manage="settings_shop_config"
               data-admc-id="<?= $shopConfig[0]['id'] ?? 1 ?>">
              <div class="heading-05"><?= htmlspecialchars($shop_phone ?? '+1 (555) 123-4567', ENT_QUOTES, 'UTF-8') ?></div>
            </a>
          </div>
          <div class="contact-box">
            <div class="color-gray"><div class="p-02">Address</div></div>
            <div class="heading-05"
                 data-admc-manage="settings_shop_config"
                 data-admc-id="<?= $shopConfig[0]['id'] ?? 1 ?>">
              <?= htmlspecialchars($shop_address ?: 'Lagos, Nigeria', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: contact form -->
      <div class="venora-contact-form-wrap">
        <form class="venora-contact-form" id="contactForm" onsubmit="handleContactSubmit(event)">
          <div class="vcf-row">
            <div class="vcf-field">
              <label class="vcf-label">First name</label>
              <input type="text" name="first_name" class="vcf-input" placeholder="Jane" required>
            </div>
            <div class="vcf-field">
              <label class="vcf-label">Last name</label>
              <input type="text" name="last_name" class="vcf-input" placeholder="Doe">
            </div>
          </div>
          <div class="vcf-field">
            <label class="vcf-label">Email address</label>
            <input type="email" name="email" class="vcf-input" placeholder="jane@example.com" required>
          </div>
          <div class="vcf-field">
            <label class="vcf-label">Subject</label>
            <input type="text" name="subject" class="vcf-input" placeholder="How can we help?">
          </div>
          <div class="vcf-field">
            <label class="vcf-label">Message</label>
            <textarea name="message" class="vcf-input vcf-textarea" rows="5"
                      placeholder="Tell us more…" required></textarea>
          </div>
          <button type="submit" class="vcf-submit">Send Message</button>
          <div id="contactSuccess" class="vcf-success" style="display:none;">
            Thank you! We'll be in touch shortly.
          </div>
          <div id="contactError" class="vcf-error" style="display:none;">
            Something went wrong. Please try again.
          </div>
        </form>
      </div>

    </div>
  </div>
</section>

<script>
(function() {
  var form = document.getElementById('contactForm');
  if (!form) return;

  var rules = {
    first_name: { required: true, label: 'First name' },
    email:      { required: true, label: 'Email', email: true },
    subject:    { required: false },
    message:    { required: true, label: 'Message', minLen: 10 }
  };

  function clearError(input) {
    input.style.borderColor = '';
    var err = input.parentNode.querySelector('.vcf-field-error');
    if (err) err.remove();
    var wrap = input.closest('.vcf-field');
    if (wrap) wrap.classList.remove('vcf-has-error');
  }

  function setError(input, msg) {
    input.style.borderColor = '#c1121f';
    clearError(input);
    input.style.borderColor = '#c1121f';
    var err = document.createElement('span');
    err.className = 'vcf-field-error';
    err.textContent = msg;
    input.parentNode.appendChild(err);
    var wrap = input.closest('.vcf-field');
    if (wrap) wrap.classList.add('vcf-has-error');
    return false;
  }

  function setValid(input) {
    clearError(input);
    input.style.borderColor = '#2d6a4f';
    var wrap = input.closest('.vcf-field');
    if (wrap) wrap.classList.remove('vcf-has-error');
  }

  function validateField(input) {
    var name = input.name;
    var rule = rules[name];
    if (!rule) return true;
    var val = input.value.trim();

    if (rule.required && !val) return setError(input, (rule.label || name) + ' is required.');
    if (rule.email && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val))
      return setError(input, 'Please enter a valid email address.');
    if (rule.minLen && val && val.length < rule.minLen)
      return setError(input, (rule.label || name) + ' must be at least ' + rule.minLen + ' characters.');

    if (val) setValid(input);
    else clearError(input);
    return true;
  }

  // Real-time: validate on blur, clear on input
  form.querySelectorAll('.vcf-input').forEach(function(inp) {
    inp.addEventListener('blur', function() { validateField(inp); });
    inp.addEventListener('input', function() {
      clearError(inp);
      inp.style.borderColor = '';
    });
  });

  window.handleContactSubmit = function(e) {
    e.preventDefault();

    // Validate all fields
    var valid = true;
    form.querySelectorAll('.vcf-input').forEach(function(inp) {
      if (!validateField(inp)) valid = false;
    });
    if (!valid) {
      // Scroll to first error
      var first = form.querySelector('.vcf-has-error .vcf-input');
      if (first) first.focus();
      return;
    }

    var btn = form.querySelector('.vcf-submit');
    btn.textContent = 'Sending…';
    btn.disabled = true;
    document.getElementById('contactSuccess').style.display = 'none';
    document.getElementById('contactError').style.display = 'none';

    var data = {};
    new FormData(form).forEach(function(v, k) { data[k] = v; });

    fetch((window.VENORA_BASE_URL || '') + '/contact-submit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        form.reset();
        form.querySelectorAll('.vcf-input').forEach(function(i) { i.style.borderColor = ''; });
        document.getElementById('contactSuccess').style.display = 'block';
      } else {
        document.getElementById('contactError').style.display = 'block';
      }
    })
    .catch(function() {
      document.getElementById('contactError').style.display = 'block';
    })
    .finally(function() {
      btn.textContent = 'Send Message';
      btn.disabled = false;
    });
  };
})();
</script>
</section>


<!-- [cbcode_50001Contactc] -->
</div>
<!-- [cbcode_50001c] -->

<?php include APP_PATH . "/views/includes/footer.php"; ?>
