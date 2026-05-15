<?php
$contactHeader = selectContent($conn, "settings_home_contact", ["visibility" => "show"])[0];

// Dynamic form selections (admin-editable)
$formServiceOptions = selectContentAsc($conn, "selection_form_services", ["visibility" => "show"], "input_order", 50);
$formPropertyOptions = selectContentAsc($conn, "selection_form_property_sizes", ["visibility" => "show"], "input_order", 50);
?>
<section id="contact" class="section">
  <div class="container">
    <div class="contact-grid">

      <div>
        <span class="section-label" style="color:var(--teal-light)" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_label'] ?></span>
        <h2 class="section-title" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_title'] ?></h2>
        <p class="section-subtitle" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['text_subtitle'] ?></p>

        <div class="contact-info">
          <div class="contact-item">
            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="contact-item-text">
              <span class="contact-item-label" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_phone_label'] ?></span>
              <a href="tel:<?= $site_phone ?? '' ?>" class="contact-item-value"><?= $site_phone ?? '' ?></a>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="contact-item-text">
              <span class="contact-item-label" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_email_label'] ?></span>
              <a href="mailto:<?= $site_email ?? '' ?>" class="contact-item-value"><?= $site_email ?? '' ?></a>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="contact-item-text">
              <span class="contact-item-label" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_response_label'] ?></span>
              <span class="contact-item-value" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_response_value'] ?></span>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="contact-item-text">
              <span class="contact-item-label" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_coverage_label'] ?></span>
              <span class="contact-item-value" data-admc-manage="settings_home_contact" data-admc-id="<?= $contactHeader['id'] ?>"><?= $contactHeader['input_coverage_value'] ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="contact-form-wrap">
        <p class="contact-form-title"><i class="fa-solid fa-clipboard-list"></i> Request a Free Quote</p>
        <form class="contact-form" onsubmit="submitForm(event)">
          <div class="form-row">
            <div class="form-group">
              <label>First Name *</label>
              <input type="text" name="first_name" placeholder="Your first name" required />
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" name="last_name" placeholder="Your last name" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Phone Number *</label>
              <input type="tel" name="phone" placeholder="07xxx xxx xxx" required />
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" placeholder="your@email.com" />
            </div>
          </div>
          <div class="form-group">
            <label>Service Required *</label>
            <select name="service" required>
              <option value="">Select a service...</option>
              <?php foreach ($formServiceOptions as $opt) { ?>
                <option value="<?= $opt['input_name'] ?>"><?= $opt['input_name'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Property Size</label>
              <select name="property_size">
                <option value="">Select...</option>
                <?php foreach ($formPropertyOptions as $opt) { ?>
                  <option value="<?= $opt['input_name'] ?>"><?= $opt['input_name'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label>Postcode *</label>
              <input type="text" name="postcode" placeholder="e.g. EH1 1AA" required />
            </div>
          </div>
          <div class="form-group">
            <label>Additional Notes</label>
            <textarea name="notes" rows="3" placeholder="Anything else we should know? (e.g. furnished, oven clean needed, access details...)"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-lg form-submit">
            Send My Quote Request →
          </button>
        </form>
      </div>

    </div>
  </div>
</section>
