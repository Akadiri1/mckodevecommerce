# ADMC Framework — Complete Developer Guide
**Admin Data Management Console | Build, Deploy, and Manage Content-Driven Websites**

---

## Table of Contents
1. [What Is ADMC?](#1-what-is-admc)
2. [Project Folder Structure](#2-project-folder-structure)
3. [Getting Started — Step by Step](#3-getting-started--step-by-step)
4. [Database Naming Conventions](#4-database-naming-conventions)
5. [Column Naming Prefixes](#5-column-naming-prefixes)
6. [PHP Functions Whitelist](#6-php-functions-whitelist)
7. [Relational Data — Pre-Indexing Pattern](#7-relational-data--pre-indexing-pattern)
8. [Dynamic Navigation](#8-dynamic-navigation)
9. [Live Edit System — data-admc Attributes](#9-live-edit-system--data-admc-attributes)
10. [CB Code Block System (AI Edit Markers)](#10-cb-code-block-system-ai-edit-markers)
11. [Theme & Colour System](#11-theme--colour-system)
12. [Section Visibility — Show/Hide from ADMC](#12-section-visibility--showhide-from-admc)
13. [Card Link Pattern (Image + Title Links)](#13-card-link-pattern-image--title-links)
14. [Contact & Email System (PHPMailer)](#14-contact--email-system-phpmailer)
15. [Page Views — How to Build a Page](#15-page-views--how-to-build-a-page)
16. [Router — How to Add a New Route](#16-router--how-to-add-a-new-route)
17. [Admin Panel — Login & Live Edit](#17-admin-panel--login--live-edit)
18. [SQL Setup — Running Your Database](#18-sql-setup--running-your-database)
19. [Quick Reference — Common Gotchas](#19-quick-reference--common-gotchas)

---

## 1. What Is ADMC?

The **Admin Data Management Console (ADMC)** is a PHP/MySQL framework for building and managing content-driven websites. It has two layers:

**Layer 1 — The Backend Framework**
A set of PHP conventions, database naming rules, and whitelisted functions that keep every project consistent, secure, and maintainable.

**Layer 2 — The Live Edit System**
HTML attributes (`data-admc-*`) placed on page elements that allow admins to edit content directly on the live site without touching code. Powered by `admc.min.js`.

**Who this is for:** Developers building websites that need a non-technical client to manage content (text, images, colours, navigation) without a traditional CMS.

---

## 2. Project Folder Structure

Every ADMC project follows this exact structure:

```
project-root/
├── .env/
│   └── config.php              ← DB credentials (never commit this)
├── project_setup.sql           ← Full database schema + seed data
├── www/                        ← DocumentRoot (point your server here)
│   ├── .htaccess               ← URL rewriting (RewriteBase /)
│   ├── index.php               ← Single entry point for all requests
│   ├── uploads/                ← User-uploaded images
│   └── assets/                 ← CSS, JS, fonts
│       ├── css/
│       ├── js/
│       └── img/
└── v1/                         ← Application core (not publicly accessible)
    ├── models/
    │   └── model.php           ← PDO database connection
    ├── controllers/
    │   └── controller.php      ← All ADMC whitelisted functions
    ├── views/
    │   ├── includes/
    │   │   ├── header.php      ← Navbar (DB-driven, ADMC attributed)
    │   │   └── footer.php      ← Footer + chat widget + ADMC script
    │   ├── home.php
    │   ├── about.php
    │   └── [all other pages]
    ├── routes/
    │   ├── router.php          ← Main URL routing
    │   ├── ajax_router.php     ← ADMC AJAX save/update endpoints
    │   └── admin_router.php    ← Admin panel routes
    ├── auth/                   ← Login, logout, signup
    ├── admin/                  ← Admin panel pages
    └── phpm/                   ← PHPMailer library
```

---

## 3. Getting Started — Step by Step

### New project checklist

**Step 1 — Configure the database**
Edit `.env/config.php`:
```php
<?php
putenv('DB_USER=root');
putenv('DB_PASSWORD=');
putenv('DB_NAME=my_project_db');
putenv('PRODUCTION_MODE=false');
```

**Step 2 — Run the SQL setup file**
In phpMyAdmin → Import, run your `project_setup.sql`. This creates all tables and inserts seed data. The site should render with placeholder content immediately after.

**Step 3 — Set the ADMC cookie**
In `www/index.php`, set the cookie to your project identifier:
```php
setcookie("admc", "my-project", time()+31536000, "/", "", false, false);
```
This value identifies your project to the ADMC live edit system.

**Step 4 — Add your virtual host**
In WAMP/MAMP, point a virtual host (e.g. `myproject.local`) to `www/`. With a virtual host, all URLs use `RewriteBase /`. Without one (subdirectory), use `RewriteBase /project-name/www/`.

**Step 5 — Log into the admin panel**
Go to `http://myproject.local/login` and log in with your admin credentials. After login, `$_SESSION['admin_id']` is set and the live edit icons appear on every page.

**Step 6 — Start editing**
Browse the site while logged in. Click any pencil (✏) icon to edit that piece of content inline. Changes save directly to the database.

---

## 4. Database Naming Conventions

### 4.1 Table Prefixes

The prefix of a table defines what the ADMC admin panel can do with it:

| Prefix | Purpose | Admin Can |
|---|---|---|
| `panel_` | Dynamic multi-row content (blog posts, services, team members) | Add, Edit, Delete, Reorder |
| `settings_` | Single-row page settings (hero section, CTA block) | Edit only |
| `read_` | Collected data — form submissions, newsletter signups | View, Edit (no delete from panel) |
| `addition_` | Child records linked to a parent row (social links per team member) | Add, Edit, Delete |
| `selection_` | Dropdown option lists (blog categories, service types) | Add, Edit, Delete |

**Examples:**
```
panel_services          → list of services (3 rows = 3 service cards)
panel_blog              → blog posts
settings_home_hero      → homepage hero (always 1 row)
settings_home_cta       → homepage CTA block (always 1 row)
read_contact_messages   → contact form submissions
addition_team_socials   → social links linked to panel_team row
selection_blog_category → dropdown: "Leadership", "NetSuite", "AI"
```

**Naming pattern:** `{prefix}_{project_namespace}_{descriptor}`

For a project called "Mike Mahony" with namespace `mm`:
```
panel_mm_services
settings_mm_hero
read_mm_messages
selection_mm_blog_cat
```
The namespace prevents collisions when multiple projects share one database.

---

### 4.2 Mandatory Columns (Every Table, No Exceptions)

Every table in ADMC **must** have these six columns:

```sql
id           INT PRIMARY KEY AUTO_INCREMENT
hash_id      VARCHAR(255) NOT NULL        -- public-facing ID, never expose raw `id`
visibility   VARCHAR(50) DEFAULT 'show'   -- 'show' or 'hide' only
date_created DATE NOT NULL
time_created TIME NOT NULL
created_by   VARCHAR(255) NOT NULL        -- admin hash_id who created the row
```

**Why `hash_id` instead of `id`?**  
Raw numeric IDs (`id=1`, `id=2`) expose your database structure and record counts to the public. `hash_id` is a random alphanumeric string used in URLs and cross-table references:
```
/services/mmns001/netsuite-sprint    ← hash_id in URL
/blog/mmbl003/fractional-cto        ← hash_id in URL
```

**Why `visibility`?**  
The ADMC admin can toggle any row to `'hide'` to remove it from the site without deleting it. Views filter with `["visibility" => "show"]` so hidden rows never appear.

---

## 5. Column Naming Prefixes

The column prefix tells the ADMC panel which input control to render for that field.

| Prefix | Input Rendered | SQL Type | Example |
|---|---|---|---|
| `input_` | Single-line text | `VARCHAR(255)` | `input_title`, `input_name`, `input_slug` |
| `text_` | Multi-line textarea | `TEXT` or `LONGTEXT` | `text_description`, `text_body` |
| `icon_` | Icon picker (Phosphor, Font Awesome, MDI) | `VARCHAR(100)` | `icon_service_icon`, `icon_step_icon` |
| `image_1` | Single image uploader | `TEXT` | One image per record |
| `image_2` | Primary image + gallery | `TEXT` | Linked to `images` system table |
| `dated_` | Date picker | `DATE` | `dated_published`, `dated_event_start` |
| `timed_` | Time picker | `TIME` | `timed_event_time` |
| `bgcolor_` | Background colour picker | `VARCHAR(100)` | `bgcolor_primary`, `bgcolor_card` |
| `textcolor_` | Text colour picker | `VARCHAR(100)` | `textcolor_heading`, `textcolor_body` |
| `select_` | Dropdown (links to `selection_` table) | `VARCHAR(255)` | `select_category` |
| `add_` | Multi-record child (links to `addition_` table) | — | `add_team_socials` |
| `input_order` | Sort order (used by `selectContentAsc`) | `INT` | Always `input_order` |

**Rules:**
- A table may have `image_1` **OR** `image_2` — never both
- `image_1` is the standard single image. Use it for hero backgrounds, card images, avatars
- `image_2` is for galleries (e.g. a property listing with many photos)
- `icon_` stores the icon class string: `"ph ph-chart-bar"`, `"fa-solid fa-star"`, etc.
- `bgcolor_` and `textcolor_` are rendered as colour pickers in the admin panel

---

## 6. PHP Functions Whitelist

You are **strictly prohibited** from writing raw SQL. Use only these approved functions:

---

### `selectContent($conn, $table, $where)`
Standard SELECT. Returns an array of rows.

```php
// All visible services
$services = selectContent($conn, "panel_services", ["visibility" => "show"]);

// Multiple conditions
$featured = selectContent($conn, "panel_blog", ["visibility" => "show", "select_category" => "Leadership"]);

// All records (no filter)
$all = selectContent($conn, "panel_blog", []);

// settings_ tables are always single row — append [0]
$hero = selectContent($conn, "settings_home_hero", ["visibility" => "show"])[0];
$cta  = selectContent($conn, "settings_mm_cta",    ["visibility" => "show"])[0];
```

---

### `selectContentDesc($conn, $table, $where, $orderColumn, $limit)`
Fetches records sorted **descending** (newest first).

```php
// 6 most recent blog posts
$posts = selectContentDesc($conn, "panel_blog", ["visibility" => "show"], "id", 6);

// 10 most recent session bookings
$bookings = selectContentDesc($conn, "read_mm_sessions", [], "id", 10);
```

---

### `selectContentAsc($conn, $table, $where, $orderColumn, $limit)`
Fetches records sorted **ascending** (lowest number first — used for ordered lists).

```php
// Nav items in menu order
$navItems = selectContentAsc($conn, "panel_mm_pages", ["visibility" => "show"], "input_order", 15);

// Service cards in display order
$services = selectContentAsc($conn, "panel_mm_services", ["visibility" => "show"], "input_order", 10);
```

---

### `insertContent($conn, $table, $dataArray)`
Inserts a new record.

```php
$new = [
    'hash_id'          => uniqid('svc_', true),
    'input_title'      => 'New Service',
    'text_description' => 'Service description here.',
    'input_order'      => 4,
    'visibility'       => 'show',
    'date_created'     => date('Y-m-d'),
    'time_created'     => date('H:i:s'),
    'created_by'       => $_SESSION['admin_id'],
];
insertContent($conn, "panel_mm_services", $new);
```

---

### `insertSafe($conn, $table, $dataArray)`
Safe insert for **user-submitted data** (contact forms, newsletter signups). Includes try/catch.

```php
insertSafe($conn, 'read_mm_messages', [
    'hash_id'      => uniqid('msg_', true),
    'input_name'   => $name,
    'input_email'  => $email,
    'text_message' => $message,
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => 'visitor',
]);
```

---

### `updateContent($conn, $table, $setArray, $whereArray)`
Updates an existing record. `$whereArray` must **not** be empty.

```php
// Hide a service
updateContent($conn, "panel_mm_services",
    ['visibility' => 'hide'],
    ['id' => 3]
);

// Change a hero heading
updateContent($conn, "settings_mm_hero",
    ['input_heading' => 'New Heading'],
    ['hash_id' => 'mmhero001']
);
```

---

### String / Utility Helpers

| Function | What it does |
|---|---|
| `shortContent($string)` | Truncates to ~50 characters |
| `previewBody($string, $wordCount)` | Truncates to N words (strips HTML) |
| `decodeDate($date)` | `"2025-01-15"` → `"January 15, 2025"` |
| `cleans($string)` | Converts any string to a URL-safe slug: `"My Title!"` → `"my-title"` |
| `hexToRgb($hex)` | `"#FFBF00"` → `"255, 191, 0"` (for CSS `rgb()`) |
| `currentPage($urlSegment)` | Echoes `"active"` if current URL matches |
| `previewBodyWithElipsces($string, $count, $strip, $url_param)` | Truncates with an optional "read more" link |

---

### Strictly Prohibited

> ❌ Never use: `eval()`, `shell_exec()`, `system()`, `passthru()`, `exec()`, `file_put_contents()`, `unlink()`, `mysqli_query()`, raw PDO prepare/execute, or `function` keyword to define new functions.

---

## 7. Relational Data — Pre-Indexing Pattern

When displaying child records (`addition_` table) alongside their parent records, **never query inside a loop**. Always pre-index.

```php
// ✅ CORRECT — two queries total, no matter how many records

// Step 1: Fetch all child records in ONE query
$socialsRaw = selectContent($conn, "addition_team_socials", ["visibility" => "show"]);

// Step 2: Pre-index by parent's hash_id
$socialsByHash = [];
foreach ($socialsRaw as $social) {
    $socialsByHash[$social['tb_link']][] = $social;
}

// Step 3: Render — look up children with zero extra DB calls
$members = selectContentAsc($conn, "panel_team_members", ["visibility" => "show"], "input_order", 20);
foreach ($members as $member) {
    echo '<h3>' . $member['input_name'] . '</h3>';
    if (isset($socialsByHash[$member['hash_id']])) {
        foreach ($socialsByHash[$member['hash_id']] as $social) {
            echo '<a href="' . $social['input_link'] . '">' . $social['input_label'] . '</a>';
        }
    }
}
```

```php
// ❌ WRONG — runs a new query for every member (N+1 problem)
foreach ($members as $member) {
    $socials = selectContent($conn, "addition_team_socials", ["tb_link" => $member['hash_id']]);
    // ...
}
```

**The `tb_link` column** in an `addition_` table always stores the `hash_id` of the parent record:

| Column | Stores |
|---|---|
| `tb` | Parent table name (e.g. `"panel_team_members"`) |
| `tb_link` | `hash_id` of the specific parent row |

---

## 8. Dynamic Navigation

The navbar is **database-driven** — never hard-code nav links in the HTML.

### Tables used

| Table | Purpose |
|---|---|
| `panel_pages` (or `panel_mm_pages`) | Top-level nav items |
| `addition_pages` (or `addition_mm_pages`) | Dropdown children linked to a parent via `tb_link` |

### Key columns in `panel_pages`

| Column | Value example | Purpose |
|---|---|---|
| `input_name` | `"About"` | Label shown in menu |
| `input_link` | `"/about"` | URL slug |
| `input_order` | `2` | Sort order |
| `visibility` | `"show"` | Toggle to hide from nav |

### Nav render pattern (in `header.php`)

```php
<?php
// Fetch parents in order
$navParents = selectContentAsc($conn, 'panel_mm_pages', ['visibility' => 'show'], 'input_order', 15);

// Fetch dropdown children and pre-index by parent hash_id
$navDropRaw  = selectContentAsc($conn, 'addition_mm_pages', ['visibility' => 'show'], 'input_order', 30);
$navDropHash = [];
foreach ($navDropRaw as $drop) {
    $navDropHash[$drop['tb_link']][] = $drop;
}
?>

<ul class="nav-links" data-admc-tb="panel_mm_pages">
<?php foreach ($navParents as $nav):
    $hasDropdown = isset($navDropHash[$nav['hash_id']]);
?>
    <li class="<?= $hasDropdown ? 'has-dropdown' : '' ?>">
        <a href="<?= htmlspecialchars($nav['input_link'], ENT_QUOTES, 'UTF-8') ?>"
           data-admc-manage="panel_mm_pages"
           data-admc-id="<?= $nav['id'] ?>">
            <?= htmlspecialchars($nav['input_name'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php if ($hasDropdown): ?>
        <ul data-admc-tb="addition_mm_pages"
            data-admc-tbadd="panel_mm_pages"
            data-admc-tblink="<?= $nav['hash_id'] ?>">
            <?php foreach ($navDropHash[$nav['hash_id']] as $child): ?>
            <li>
                <a href="<?= htmlspecialchars($child['input_link'], ENT_QUOTES, 'UTF-8') ?>"
                   data-admc-manage="addition_mm_pages"
                   data-admc-id="<?= $child['id'] ?>">
                    <?= htmlspecialchars($child['input_name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>
```

---

## 9. Live Edit System — data-admc Attributes

The live edit system lets logged-in admins click any element on the site and edit it directly. Every editable element **must** have the correct `data-admc-*` attributes.

---

### 9.1 `data-admc-manage` — Make Text/Content Editable

Place on the HTML tag that renders the text. Always paired with `data-admc-id`.

```php
<!-- Settings table (single row) — use [0]['id'] -->
<h1 data-admc-manage="settings_mm_hero"
    data-admc-id="<?= $hero['id'] ?>">
    <?= htmlspecialchars($hero['input_heading'], ENT_QUOTES, 'UTF-8') ?>
</h1>

<!-- Panel table (loop) — use $row['id'] -->
<h3 data-admc-manage="panel_mm_services"
    data-admc-id="<?= $svc['id'] ?>">
    <?= htmlspecialchars($svc['input_title'], ENT_QUOTES, 'UTF-8') ?>
</h3>
```

Multiple fields from the same row can all point to the same `data-admc-id` — ADMC opens one edit panel covering all `input_` and `text_` columns for that row:

```php
<div class="hero-content">
    <p class="badge" data-admc-manage="settings_mm_hero" data-admc-id="<?= $hero['id'] ?>">
        <?= htmlspecialchars($hero['input_badge'], ENT_QUOTES, 'UTF-8') ?>
    </p>
    <h1 data-admc-manage="settings_mm_hero" data-admc-id="<?= $hero['id'] ?>">
        <?= htmlspecialchars($hero['input_heading'], ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <p data-admc-manage="settings_mm_hero" data-admc-id="<?= $hero['id'] ?>">
        <?= htmlspecialchars($hero['text_description'], ENT_QUOTES, 'UTF-8') ?>
    </p>
</div>
```

---

### 9.2 `data-admc-image` — Make an Image Editable

Place on the **wrapper `<div>` around the `<img>`** — NOT on the `<img>` itself.

```php
<!-- ✅ CORRECT — on the wrapper div -->
<div data-admc-image="panel_mm_services"
     data-admc-id="<?= $svc['id'] ?>">
    <img src="<?= htmlspecialchars($svc['image_1'], ENT_QUOTES, 'UTF-8') ?>"
         alt="<?= htmlspecialchars($svc['input_title'], ENT_QUOTES, 'UTF-8') ?>">
</div>

<!-- ❌ WRONG — never on the img tag -->
<img data-admc-image="panel_mm_services" data-admc-id="<?= $svc['id'] ?>"
     src="<?= $svc['image_1'] ?>">
```

When the admin clicks the image wrapper, ADMC opens an image uploader. The new image path is stored in the `image_1` column.

---

### 9.3 `data-admc-tb` — Enable "Add New" for a List

Place on the **container that wraps the entire `foreach` loop**. This adds an "Add New" button to the list.

```php
<!-- The wrapper div gets data-admc-tb -->
<div class="services-grid" data-admc-tb="panel_mm_services">
    <?php foreach ($services as $svc): ?>
        <div class="service-card">
            <!-- card content -->
        </div>
    <?php endforeach; ?>
</div>
```

---

### 9.4 `data-admc-tb` + `data-admc-tbadd` + `data-admc-tblink` — Manage Child Records

Used for `addition_` tables. Three attributes are required together:

```php
<div class="social-links"
     data-admc-tb="addition_team_socials"
     data-admc-tbadd="panel_team_members"
     data-admc-tblink="<?= $member['hash_id'] ?>">
    <?php foreach ($socialsByHash[$member['hash_id']] ?? [] as $social): ?>
        <a href="<?= $social['input_link'] ?>"
           data-admc-manage="addition_team_socials"
           data-admc-id="<?= $social['id'] ?>">
            <i class="<?= $social['icon_social'] ?>"></i>
        </a>
    <?php endforeach; ?>
</div>
```

| Attribute | Value |
|---|---|
| `data-admc-tb` | The `addition_` table name |
| `data-admc-tbadd` | The parent `panel_` table name |
| `data-admc-tblink` | The `hash_id` of the parent record |

---

### 9.5 Loading the ADMC Script

The ADMC live edit script must **only load for logged-in admins**. Place this at the bottom of your `footer.php`:

```php
<?php if (isset($_SESSION['admin_id'])): ?>
    <script src="https://admc.dev/admc.min.js" charset="utf-8"></script>
<?php endif; ?>
```

> ⚠️ Do NOT comment out the `if` check. Without it, edit icons appear for all visitors but saves fail with "Network Error" because there is no valid admin session.

---

## 10. CB Code Block System (AI Edit Markers)

This is how the AI reads and edits view files. Every editable section of a page **must** use this structure. Without it, AI assistance cannot locate or modify content blocks.

### Structure

```php
<div data-cbsection="cb1">
<?php/*##cb1o##*/>

    <?php/*##cbcode_56761o##*/>
    <div data-cbcodesection="cbcode_56761">
        <section class="hero">
            <h1>Editable Content Here</h1>
        </section>
    </div>
    <?php/*##cbcode_56761c##*/>

    <?php/*##cbcode_56762o##*?>
    <div data-cbcodesection="cbcode_56762">
        <section class="about">
            <h2>Second Section</h2>
        </section>
    </div>
    <?php/*##cbcode_56762c##*/>

<?php/*##cb1c##*/>
</div>
```

### Rules

| Element | Purpose |
|---|---|
| `<div data-cbsection="cb1">` | Declares the main editable zone |
| `<?php/*##cb1o##*/>` | GPS start marker — required |
| `<?php/*##cbcode_XXXXXo##*/>` | Opens a unique editable block |
| `<div data-cbcodesection="cbcode_XXXXX">` | Container holding the HTML |
| `<?php/*##cbcode_XXXXXc##*/>` | Closes the editable block |
| `<?php/*##cb1c##*/>` | GPS end marker — required |

**ID numbering convention by page:**

| Page | ID range |
|---|---|
| `home.php` | 10001 – 10099 |
| `about.php` | 20001 – 20099 |
| `services.php` | 30001 – 30099 |
| `podcast.php` | 40001 – 40099 |
| `blog.php` | 50001 – 50099 |
| `contact.php` | 60001 – 60099 |
| `book_session.php` | 70001 – 70099 |
| `service_detail.php` | 80001 – 80099 |
| `podcast_episode.php` | 90001 – 90099 |
| `blog_post.php` | 11001 – 11099 |

> ⚠️ IDs must be **unique per file**. Duplicate IDs cause AI edits to overwrite the wrong block. Never delete the `<?php/*##...*/?>` comments.

---

## 11. Theme & Colour System

### How it works

Colours are stored in `website_status` and injected as CSS variables in `header.php`. Changing a colour in the ADMC admin panel updates the variable instantly across the whole site.

### `website_status` colour columns

| Column | CSS Variable | Controls |
|---|---|---|
| `color` | `--primary` | Buttons, links, accents, gradient text |
| `secondary_color` | `--bg-primary` (dark) | Main dark background |
| `bgcolor_background` | `--bg-primary` | Dark page background |
| `bgcolor_surface` | `--bg-secondary`, `--bg-card` | Card / nav surfaces |
| `textcolor_heading` | `--text-primary` | H1 H2 H3 |
| `textcolor_body` | `--text-secondary` | Paragraph text |
| `textcolor_muted` | `--text-muted` | Labels, meta, captions |

### Injecting colours in `header.php`

```php
<?php
// Fetch from website_status
$style = selectContent($conn, "website_status", ["visibility" => "show"])[0];
$primary     = $style['color']            ?? '#FFBF00';
$primaryDark = '#cc9900';                              // derived shade
$bgDark      = $style['bgcolor_background'] ?? '#050a14';
$bgSurface   = $style['bgcolor_surface']    ?? '#0a1128';
$textHead    = $style['textcolor_heading']  ?? '#f9fafb';
$textBody    = $style['textcolor_body']     ?? '#d1d5db';
$textMuted   = $style['textcolor_muted']    ?? '#9ca3af';
?>

<style data-admc-manage="website_status" data-admc-id="<?= $style['id'] ?>">
  /* Apply primary to both modes */
  :root, [data-theme="dark"], [data-theme="light"] {
    --primary:       <?= htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') ?>;
    --primary-dark:  <?= htmlspecialchars($primaryDark, ENT_QUOTES, 'UTF-8') ?>;
    --accent:        <?= htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') ?>;
    --primary-glow:  rgba(255, 191, 0, 0.3);
    --border-hover:  rgba(255, 191, 0, 0.35);
  }
  /* Dark mode backgrounds */
  [data-theme="dark"] {
    --bg-primary:     <?= htmlspecialchars($bgDark,   ENT_QUOTES, 'UTF-8') ?>;
    --bg-secondary:   <?= htmlspecialchars($bgSurface, ENT_QUOTES, 'UTF-8') ?>;
    --bg-card:        <?= htmlspecialchars($bgSurface, ENT_QUOTES, 'UTF-8') ?>;
    --text-primary:   <?= htmlspecialchars($textHead,  ENT_QUOTES, 'UTF-8') ?>;
    --text-secondary: <?= htmlspecialchars($textBody,  ENT_QUOTES, 'UTF-8') ?>;
    --text-muted:     <?= htmlspecialchars($textMuted, ENT_QUOTES, 'UTF-8') ?>;
  }
  /* Light mode keeps its own palette from the stylesheet */
</style>
```

> ⚠️ Do NOT inject `--bg-primary` and `--text-primary` at `:root` level — they would override the light mode CSS. Use `[data-theme="dark"]` so light mode keeps its own stylesheet values.

> ⚠️ Never hardcode `rgba(0, 166, 81, ...)` (a previous project's green) in your CSS. Always use `var(--primary-glow)` or `rgba(255, 191, 0, ...)` based on your primary colour.

---

## 12. Section Visibility — Show/Hide from ADMC

### Hiding a whole section (`settings_` table)

Every `settings_` table has a `visibility` column. Set it to `'hide'` in the ADMC panel → the section disappears.

In the view, wrap each section in a null check:

```php
<?php
// Fetch with visibility filter — returns empty array if 'hide'
$heroArr = selectContent($conn, "settings_mm_hero", ["visibility" => "show"]);
$hero    = !empty($heroArr) ? $heroArr[0] : null;
?>

<?php if ($hero): ?>
<section class="hero">
    <!-- section content using $hero data -->
</section>
<?php endif; ?>
```

### Hiding individual cards (`panel_` table)

Cards in a loop are filtered automatically — just set `visibility = 'hide'` on the row in ADMC:

```php
// Only 'show' rows are returned — hidden cards never appear
$services = selectContentAsc($conn, "panel_mm_services", ["visibility" => "show"], "input_order", 10);

<?php foreach ($services as $svc): ?>
    <!-- ADMC will never pass hidden rows here -->
<?php endforeach; ?>
```

No extra `if` check needed in the loop — the `["visibility" => "show"]` WHERE clause handles it.

---

## 13. Card Link Pattern (Image + Title Links)

On listing pages, the **image AND the title text** should both link to the detail page. The description/body text stays plain (not linked).

### Pattern

```php
<div class="card">
    <!-- IMAGE → links to detail page -->
    <a href="/services/<?= $svc['hash_id'] ?>/<?= cleans($svc['input_title']) ?>">
        <div data-admc-image="panel_mm_services" data-admc-id="<?= $svc['id'] ?>">
            <img src="<?= htmlspecialchars($svc['image_1'], ENT_QUOTES, 'UTF-8') ?>"
                 alt="<?= htmlspecialchars($svc['input_title'], ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </a>

    <div class="card-body">
        <!-- TITLE → also links, styled with .card-title-link (no underline) -->
        <h3 data-admc-manage="panel_mm_services" data-admc-id="<?= $svc['id'] ?>">
            <a href="/services/<?= $svc['hash_id'] ?>/<?= cleans($svc['input_title']) ?>"
               class="card-title-link">
                <?= htmlspecialchars($svc['input_title'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>

        <!-- DESCRIPTION → plain text, no link -->
        <p data-admc-manage="panel_mm_services" data-admc-id="<?= $svc['id'] ?>">
            <?= previewBody($svc['text_description'], 30) ?>
        </p>
    </div>
</div>
```

### CSS for `.card-title-link`

```css
.card-title-link {
    text-decoration: none;
    color: inherit;
    transition: color 0.3s ease;
    display: block;
}
.card-title-link:hover {
    color: var(--primary);
}
```

### URL structure for detail pages

```
/services/{hash_id}/{slug}
/podcast/{hash_id}
/blog/{hash_id}/{slug}
```

`{hash_id}` is the `hash_id` column value. `{slug}` is the `input_title` run through `cleans()`.

In `router.php`, the detail route fetches the record by `hash_id`:

```php
case 'services/' . $uri[2]:
    include APP_PATH . "/views/service_detail.php";
    $is404 = false;
    die;
```

In the detail view:
```php
$hash = $uri[2] ?? '';
$svcArr = selectContent($conn, "panel_mm_services", ["hash_id" => $hash, "visibility" => "show"]);
if (empty($svcArr)) { include APP_PATH . '/views/404.php'; die; }
$svc = $svcArr[0];
```

---

## 14. Contact & Email System (PHPMailer)

### Setup

SMTP credentials are stored in `settings_website_info` (editable from ADMC — no code changes needed):

| Column | Value |
|---|---|
| `input_email` | Admin receive email |
| `input_email_from` | Your Gmail address (the "send from" address) |
| `input_email_password` | **Gmail App Password** (16 chars — NOT your login password) |
| `input_email_smtp_host` | `smtp.gmail.com` |
| `input_email_smtp_secure_type` | `tls` |
| `input_email_smtp_port` | `587` |

**How to generate a Gmail App Password:**
1. Go to `myaccount.google.com/security`
2. Enable 2-Step Verification if not already on
3. Go to **App Passwords**
4. Select App: "Mail", Device: "Other" → name it "Website"
5. Copy the 16-character password and paste it into ADMC

### Backend pattern (contact form handler)

```php
<?php
header('Content-Type: application/json');

// SMTP vars are already set globally by index.php from settings_website_info
// $site_email_from, $site_email_smtp_host, $site_email_password, etc.

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$name    = htmlspecialchars(trim($data['name']    ?? ''), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($data['message'] ?? ''), ENT_QUOTES, 'UTF-8');

if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Required fields missing.']);
    die;
}

// Always save to DB first
insertSafe($conn, 'read_contact_messages', [
    'hash_id'      => uniqid('msg_', true),
    'input_name'   => $name,
    'input_email'  => $email,
    'text_message' => $message,
    'visibility'   => 'show',
    'date_created' => date('Y-m-d'),
    'time_created' => date('H:i:s'),
    'created_by'   => 'visitor',
]);

// Send email only if SMTP is configured
if (empty($site_email_from) || empty($site_email_password)) {
    echo json_encode(['success' => true, 'note' => 'Saved. Configure SMTP in ADMC.']);
    die;
}

try {
    require APP_PATH . '/phpm/PHPMailerAutoload.php';

    // Email to visitor (auto-reply)
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host       = $site_email_smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $site_email_from;
    $mail->Password   = $site_email_password;
    $mail->SMTPSecure = $site_email_smtp_secure_type;
    $mail->Port       = (int)$site_email_smtp_port;
    $mail->setFrom($site_email_from, $site_name);
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = "Message received — {$site_name}";
    $mail->Body    = "<p>Thanks {$name}, we received your message and will reply within 1 business day.</p>";

    // Email to admin (notification)
    $mail2 = new PHPMailer;
    // ... same SMTP setup ...
    $mail2->addAddress($site_email, $site_name);
    $mail2->addReplyTo($email, $name);
    $mail2->Subject = "New contact from {$name}";
    $mail2->Body    = "<p><strong>{$name}</strong> ({$email}): {$message}</p>";

    if ($mail->send() && $mail2->send()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Mail send failed.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Mail error. Message saved.']);
}
```

---

## 15. Page Views — How to Build a Page

Every page view follows this template:

```php
<?php
// 1. Set page meta
$page_title = "About";
$bodyClass  = "has-dark-hero";  // or "home-page" for homepage

// 2. Fetch all data needed by this page
$hero    = selectContent($conn, "settings_mm_about_hero",  ["visibility" => "show"])[0];
$content = selectContent($conn, "settings_mm_about_block", ["visibility" => "show"])[0];
$items   = selectContentAsc($conn, "panel_mm_about_features", ["visibility" => "show"], "input_order", 10);

// 3. Include header (outputs <html>, <head>, navbar, particles, etc.)
include 'includes/mm_header.php';
?>

<!-- 4. Wrap all content in CB zone -->
<div data-cbsection="cb1">
<?php/*##cb1o##*/>

<!-- 5. Each section is its own cbcode block -->
<?php/*##cbcode_20001o##*/>
<div data-cbcodesection="cbcode_20001">

<?php if ($hero): ?>
<section class="page-hero">
  <div class="section-shine"></div>
  <div class="page-hero-bg">
    <div data-admc-image="settings_mm_about_hero" data-admc-id="<?= $hero['id'] ?>">
      <img src="<?= htmlspecialchars($hero['image_1'], ENT_QUOTES, 'UTF-8') ?>" alt="About">
    </div>
  </div>
  <div class="container">
    <div class="page-hero-content">
      <div class="breadcrumb">
        <a href="/">Home</a><i class="ph ph-caret-right"></i><span>About</span>
      </div>
      <h1 data-admc-manage="settings_mm_about_hero" data-admc-id="<?= $hero['id'] ?>">
        <?= htmlspecialchars($hero['input_heading'], ENT_QUOTES, 'UTF-8') ?>
        <span class="gradient-text"><?= htmlspecialchars($hero['input_heading_highlight'], ENT_QUOTES, 'UTF-8') ?></span>
      </h1>
    </div>
  </div>
</section>
<?php endif; ?>

</div>
<?php/*##cbcode_20001c##*/>

<!-- Add more cbcode blocks for each section ... -->

<?php/*##cb1c##*/>
</div>

<?php include 'includes/mm_footer.php'; ?>
```

### Inner page hero (`page-hero` class)

All inner pages (not homepage) use a standard page hero:

```php
<section class="page-hero">
  <div class="section-shine"></div>
  <div class="page-hero-bg">
    <div data-admc-image="settings_mm_contact" data-admc-id="<?= $cs['id'] ?>">
      <img src="<?= htmlspecialchars($cs['image_1'], ENT_QUOTES, 'UTF-8') ?>" alt="Contact">
    </div>
  </div>
  <div class="container">
    <div class="page-hero-content">
      <!-- breadcrumb, section-label, h1, p -->
    </div>
  </div>
</section>
```

The image is dimmed with CSS (`.page-hero-bg img { filter: brightness(0.32); }`) and a dark gradient overlay is applied automatically. Text stays white in both dark and light mode.

---

## 16. Router — How to Add a New Route

All routing is in `v1/routes/router.php`. URL segments are available in the `$uri` array.

### Single-segment routes (`/about`, `/contact`)

```php
switch ($uri[1]) {
    case 'about':
        include APP_PATH . "/views/about.php";
        $is404 = false;
        die;

    case 'contact':
        include APP_PATH . "/views/contact.php";
        $is404 = false;
        die;
}
```

### Detail routes (`/services/{hash_id}`, `/blog/{hash_id}/{slug}`)

```php
if (count($uri) > 2) {
    switch ($uri[1] . "/" . $uri[2]) {
        case 'services/' . $uri[2]:
            include APP_PATH . "/views/service_detail.php";
            $is404 = false;
            die;

        case 'blog/' . $uri[2]:
            include APP_PATH . "/views/blog_post.php";
            $is404 = false;
            die;
    }
}
```

In the detail view, `$uri[2]` is the `hash_id`:

```php
$hash   = $uri[2] ?? '';
$record = selectContent($conn, "panel_mm_services", ["hash_id" => $hash, "visibility" => "show"]);
if (empty($record)) { include APP_PATH . '/views/404.php'; die; }
$item = $record[0];
```

### Form backend routes (POST handlers)

```php
case 'contact-submit':
    include APP_PATH . "/views/contact_backend.php";
    $is404 = false;
    die;
```

---

## 17. Admin Panel — Login & Live Edit

### Logging in

```
http://yourdomain.local/login
```

Use your admin credentials. After login, `$_SESSION['admin_id']` is set.

### What login unlocks

1. **Live edit icons** appear on every element that has `data-admc-manage`
2. **Gear icon (⚙)** in top-left opens the ADMC control panel
3. **Image upload** works on `data-admc-image` wrappers
4. **Add New** button appears on `data-admc-tb` containers
5. **Colour picker** opens for `bgcolor_` and `textcolor_` columns in `website_status`

### Logging out

```
http://yourdomain.local/logout
```

### Admin panel

```
http://yourdomain.local/admin
```

Manage all database tables, add/edit/delete records, configure tables.

---

## 18. SQL Setup — Running Your Database

### Recommended file structure

```
mikemahony_setup.sql         ← Full schema + seed data (run this first time)
mikemahony_fix_colors.sql    ← Quick UPDATE if colours need resetting
```

### Rules for writing your SQL file

**Always use `CREATE TABLE IF NOT EXISTS`** — safe to re-run:
```sql
CREATE TABLE IF NOT EXISTS `panel_mm_services` ( ... );
```

**Always use `INSERT IGNORE`** — skips duplicate rows without error:
```sql
INSERT IGNORE INTO `panel_mm_services` VALUES (1, 'mmns001', ...);
```

**For `ALTER TABLE` on MySQL 5.7** — `ADD COLUMN IF NOT EXISTS` does NOT exist in MySQL 5.7 (only MySQL 8.0+). Use the safe pattern instead:

```sql
-- MySQL 5.7 safe column addition
SET @c = (SELECT COUNT(*) FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'settings_website_info'
            AND COLUMN_NAME  = 'input_tagline');
SET @s = IF(@c = 0,
    CONCAT('ALTER TABLE settings_website_info ADD COLUMN input_tagline VARCHAR(255) DEFAULT ', CHAR(39), CHAR(39)),
    'SELECT 1');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
```

**Order matters** — put `CREATE TABLE` statements FIRST, then `ALTER TABLE` LAST:
```sql
-- All CREATE TABLE + INSERT IGNORE first
CREATE TABLE IF NOT EXISTS `panel_mm_services` (...);
INSERT IGNORE INTO `panel_mm_services` VALUES (...);

-- ALTER TABLE at the very end
-- (so even if ALTER fails, all content tables are created)
ALTER TABLE `settings_website_info` ADD COLUMN `input_tagline` VARCHAR(255) DEFAULT '';
UPDATE `settings_website_info` SET `input_name` = 'My Site' WHERE id = 1;
```

**Do not use `WHERE id = 1` in UPDATEs.** The row might not have `id = 1`. Use `hash_id` or `LIMIT 1`:
```sql
-- ✅ Use hash_id
UPDATE `website_status` SET `color` = '#FFBF00' WHERE `hash_id` = 'ws001';

-- ✅ Or LIMIT 1 if only one row
UPDATE `website_status` SET `color` = '#FFBF00' LIMIT 1;

-- ❌ Dangerous — row might have a different id
UPDATE `website_status` SET `color` = '#FFBF00' WHERE id = 1;
```

---

## 19. Quick Reference — Common Gotchas

| Mistake | Correct Approach |
|---|---|
| Raw SQL (`SELECT * FROM ...`) | Use `selectContent()` only |
| `query inside a foreach loop` | Pre-index with a single query before the loop |
| `data-admc-image` on `<img>` | Place on the **wrapper div**, not the img tag |
| Duplicate `cbcode_` IDs in one file | Every block needs a unique numeric ID |
| Deleting `<?php/*##...*/?>` comments | These are required navigation markers — never remove |
| Both `image_1` and `image_2` in one table | Use one or the other, never both |
| Hard-coding navigation links | Navigation is DB-driven via `panel_pages` |
| `settings_` table without `[0]` | Always append `[0]` — settings tables are single-row |
| `//if (isset($_SESSION['admin_id']))` | The `//` comments out the check — ADMC loads for everyone but saves fail with Network Error |
| `WHERE id = 1` in UPDATE | Use `WHERE hash_id = 'your_hash'` or `LIMIT 1` |
| `ADD COLUMN IF NOT EXISTS` | Only works in MySQL 8.0+. Use SET/PREPARE/EXECUTE for MySQL 5.7 |
| `:root { --bg-primary: #dark; }` in header | Breaks light mode. Use `[data-theme="dark"]` for dark backgrounds |
| `rgba(0, 166, 81, ...)` hardcoded in CSS | This is a specific project's green. Use `var(--primary-glow)` |
| Text link wrapping entire card | Only wrap image and title in `<a>`. Description stays plain. Use `.card-title-link` class |
| `website_status.status = 'demo'` | Set to `'live'` or primary colour won't inject regardless of DB value |
| Forgetting to log in before editing | Go to `/login` first. Without a session, ADMC icons appear but saves fail with "Network Error" |
| `input_email_password` = Gmail login password | Must be an **App Password** (16 chars). Generate at myaccount.google.com/security → App Passwords |

---

---

## 20. E-Commerce — Building a Shop with ADMC

ADMC supports full e-commerce websites. This section covers the table structure, product patterns, cart/order flow, and payment routing.

---

### 20.1 E-Commerce Table Map

| Table | Type | Purpose |
|---|---|---|
| `panel_products` | `panel_` | Product listings |
| `addition_product_images` | `addition_` | Extra gallery images per product |
| `addition_product_variants` | `addition_` | Size, colour, or option variants |
| `selection_product_category` | `selection_` | Category dropdown options |
| `selection_product_tag` | `selection_` | Tag dropdown options |
| `panel_collections` | `panel_` | Featured collections (homepage sections) |
| `read_orders` | `read_` | Customer orders |
| `read_order_items` | `read_` | Line items inside each order |
| `read_cart` | `read_` | Shopping cart rows |
| `read_reviews` | `read_` | Customer product reviews |
| `read_customers` | `read_` | Registered customers |
| `settings_shop_hero` | `settings_` | Shop homepage hero |
| `settings_shop_config` | `settings_` | Tax rate, shipping, currency, store name |
| `read_newsletter` | `read_` | Email subscribers |

---

### 20.2 Product Table (`panel_products`)

```sql
CREATE TABLE IF NOT EXISTS `panel_products` (
  `id`                   INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`              VARCHAR(255) NOT NULL,
  `input_title`          VARCHAR(255),          -- product name
  `input_slug`           VARCHAR(255),          -- url slug
  `select_category`      VARCHAR(255),          -- links to selection_product_category
  `text_description`     LONGTEXT,              -- full product description
  `text_short_desc`      TEXT,                  -- used on listing cards
  `input_price`          VARCHAR(50),           -- e.g. "29.99"
  `input_compare_price`  VARCHAR(50),           -- original/crossed-out price
  `input_sku`            VARCHAR(100),          -- stock keeping unit
  `input_stock`          VARCHAR(20),           -- quantity in stock
  `input_weight`         VARCHAR(50),           -- for shipping calc
  `input_badge`          VARCHAR(50),           -- "New", "Sale", "Best Seller"
  `icon_product_icon`    VARCHAR(100),          -- optional icon
  `image_2`              TEXT,                  -- primary image (+ gallery via addition_product_images)
  `add_product_variants` VARCHAR(255),          -- signals variants exist
  `input_order`          INT DEFAULT 0,
  `visibility`           VARCHAR(50) DEFAULT 'show',
  `date_created`         DATE NOT NULL,
  `time_created`         TIME NOT NULL,
  `created_by`           VARCHAR(255) NOT NULL
);
```

> Use `image_2` (not `image_1`) for products because they typically have a gallery. The primary image path goes in `image_2`. Extra images go in `addition_product_images` linked by `tb_link = hash_id`.

---

### 20.3 Product Variants (`addition_product_variants`)

```sql
CREATE TABLE IF NOT EXISTS `addition_product_variants` (
  `id`             INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`        VARCHAR(255) NOT NULL,
  `tb`             VARCHAR(255) DEFAULT 'panel_products',
  `tb_link`        VARCHAR(255),       -- hash_id of parent product
  `input_name`     VARCHAR(255),       -- e.g. "Size", "Colour"
  `input_value`    VARCHAR(255),       -- e.g. "XL", "Red"
  `input_price`    VARCHAR(50),        -- override price for this variant
  `input_stock`    VARCHAR(20),        -- stock for this specific variant
  `input_sku`      VARCHAR(100),
  `input_order`    INT DEFAULT 0,
  `visibility`     VARCHAR(50) DEFAULT 'show',
  `date_created`   DATE NOT NULL,
  `time_created`   TIME NOT NULL,
  `created_by`     VARCHAR(255) NOT NULL
);
```

---

### 20.4 Orders (`read_orders` + `read_order_items`)

```sql
CREATE TABLE IF NOT EXISTS `read_orders` (
  `id`                   INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`              VARCHAR(255) NOT NULL,  -- order number shown to customer
  `input_customer_name`  VARCHAR(255),
  `input_customer_email` VARCHAR(255),
  `input_phone`          VARCHAR(50),
  `text_address`         TEXT,                   -- shipping address (JSON or plain)
  `input_status`         VARCHAR(50) DEFAULT 'pending',
                         -- pending | paid | processing | shipped | delivered | cancelled
  `input_total`          VARCHAR(50),            -- order total
  `input_subtotal`       VARCHAR(50),
  `input_tax`            VARCHAR(50),
  `input_shipping`       VARCHAR(50),
  `input_payment_method` VARCHAR(100),           -- stripe | paypal | transfer
  `input_payment_ref`    VARCHAR(255),           -- payment gateway transaction ID
  `text_notes`           TEXT,                   -- customer notes
  `visibility`           VARCHAR(50) DEFAULT 'show',
  `date_created`         DATE NOT NULL,
  `time_created`         TIME NOT NULL,
  `created_by`           VARCHAR(255) NOT NULL   -- customer hash_id or 'guest'
);

CREATE TABLE IF NOT EXISTS `read_order_items` (
  `id`                INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`           VARCHAR(255) NOT NULL,
  `tb`                VARCHAR(255) DEFAULT 'read_orders',
  `tb_link`           VARCHAR(255),     -- hash_id of the order
  `input_product_id`  VARCHAR(255),     -- hash_id of the product
  `input_title`       VARCHAR(255),     -- product name at time of purchase
  `input_variant`     VARCHAR(255),     -- variant selected (e.g. "XL / Red")
  `input_quantity`    VARCHAR(20),
  `input_price`       VARCHAR(50),      -- unit price at time of purchase
  `input_total`       VARCHAR(50),      -- quantity × price
  `image_1`           TEXT,             -- product image snapshot
  `visibility`        VARCHAR(50) DEFAULT 'show',
  `date_created`      DATE NOT NULL,
  `time_created`      TIME NOT NULL,
  `created_by`        VARCHAR(255) NOT NULL
);
```

---

### 20.5 Cart (`read_cart`)

```sql
CREATE TABLE IF NOT EXISTS `read_cart` (
  `id`               INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`          VARCHAR(255) NOT NULL,
  `input_session_id` VARCHAR(255),     -- PHP session ID or customer hash_id
  `input_product_id` VARCHAR(255),     -- product hash_id
  `input_variant_id` VARCHAR(255),     -- variant hash_id (if applicable)
  `input_quantity`   INT DEFAULT 1,
  `input_price`      VARCHAR(50),      -- price at time of add
  `visibility`       VARCHAR(50) DEFAULT 'show',
  `date_created`     DATE NOT NULL,
  `time_created`     TIME NOT NULL,
  `created_by`       VARCHAR(255) NOT NULL
);
```

---

### 20.6 Shop Config (`settings_shop_config`)

```sql
CREATE TABLE IF NOT EXISTS `settings_shop_config` (
  `id`                    INT PRIMARY KEY AUTO_INCREMENT,
  `hash_id`               VARCHAR(255) NOT NULL,
  `input_currency`        VARCHAR(10)  DEFAULT 'USD',   -- USD, GBP, NGN
  `input_currency_symbol` VARCHAR(10)  DEFAULT '$',
  `input_tax_rate`        VARCHAR(20)  DEFAULT '0',     -- percentage e.g. "7.5"
  `input_free_shipping`   VARCHAR(50),                  -- min order for free shipping
  `input_shipping_rate`   VARCHAR(50),                  -- flat rate shipping cost
  `input_stripe_key`      VARCHAR(255),                 -- Stripe publishable key
  `input_paypal_client`   VARCHAR(255),                 -- PayPal client ID
  `text_return_policy`    TEXT,
  `text_shipping_info`    TEXT,
  `visibility`            VARCHAR(50) DEFAULT 'show',
  `date_created`          DATE NOT NULL,
  `time_created`          TIME NOT NULL,
  `created_by`            VARCHAR(255) NOT NULL
);
```

---

### 20.7 Product Listing Page Pattern

```php
<?php
$page_title = "Shop";
$bodyClass  = "has-dark-hero";

// Category filter from URL
$activeCategory = $uri[2] ?? '';

// Get all categories for filter bar
$categories = selectContent($conn, "selection_product_category", ["visibility" => "show"]);

// Get products (filtered or all)
$where = ["visibility" => "show"];
if (!empty($activeCategory)) {
    $where["select_category"] = $activeCategory;
}
$products = selectContentDesc($conn, "panel_products", $where, "id", 24);

// Pre-index variants
$variantsRaw = selectContent($conn, "addition_product_variants", ["visibility" => "show"]);
$variantsByHash = [];
foreach ($variantsRaw as $v) {
    $variantsByHash[$v['tb_link']][] = $v;
}

include 'includes/header.php';
?>

<section class="shop-grid">
  <div class="container">

    <!-- Category filter bar -->
    <div class="category-filter">
      <a href="/shop" class="filter-btn <?= empty($activeCategory) ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="/shop/<?= htmlspecialchars($cat['input_name'], ENT_QUOTES, 'UTF-8') ?>"
           class="filter-btn <?= ($activeCategory === $cat['input_name']) ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['input_name'], ENT_QUOTES, 'UTF-8') ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Product grid -->
    <div class="products-grid" data-admc-tb="panel_products">
      <?php foreach ($products as $product): ?>
        <div class="product-card">

          <!-- Image → links to product detail -->
          <a href="/product/<?= $product['hash_id'] ?>/<?= cleans($product['input_title']) ?>">
            <div class="product-img" data-admc-image="panel_products" data-admc-id="<?= $product['id'] ?>">
              <img src="<?= htmlspecialchars($product['image_2'], ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <?php if (!empty($product['input_badge'])): ?>
              <span class="product-badge"><?= htmlspecialchars($product['input_badge'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
          </a>

          <div class="product-body">
            <!-- Title → also links to detail -->
            <h3 data-admc-manage="panel_products" data-admc-id="<?= $product['id'] ?>">
              <a href="/product/<?= $product['hash_id'] ?>/<?= cleans($product['input_title']) ?>"
                 class="card-title-link">
                <?= htmlspecialchars($product['input_title'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </h3>

            <!-- Short description — plain text -->
            <p data-admc-manage="panel_products" data-admc-id="<?= $product['id'] ?>">
              <?= previewBody($product['text_short_desc'], 15) ?>
            </p>

            <!-- Price -->
            <div class="product-price">
              <?php if (!empty($product['input_compare_price'])): ?>
                <span class="price-original">$<?= htmlspecialchars($product['input_compare_price'], ENT_QUOTES, 'UTF-8') ?></span>
              <?php endif; ?>
              <span class="price-current">$<?= htmlspecialchars($product['input_price'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>

            <!-- Add to cart button -->
            <button class="btn btn-primary btn-add-cart"
                    data-product-id="<?= $product['hash_id'] ?>"
                    data-price="<?= htmlspecialchars($product['input_price'], ENT_QUOTES, 'UTF-8') ?>">
              Add to Cart
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>
```

---

### 20.8 Cart & Add-to-Cart AJAX Pattern

The cart is managed via AJAX calls to the backend. Cart data is stored in `read_cart` keyed by session ID.

**JavaScript (add to cart):**
```javascript
document.querySelectorAll('.btn-add-cart').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var productId = this.dataset.productId;
        var variantId = this.dataset.variantId || '';
        var qty       = 1;

        fetch('/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: qty })
        })
        .then(r => r.json())
        .then(function(data) {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        });
    });
});
```

**Backend route (`v1/views/cart_add_backend.php`):**
```php
<?php
header('Content-Type: application/json');

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

$sessionId = session_id();
$productId = htmlspecialchars(trim($data['product_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$variantId = htmlspecialchars(trim($data['variant_id'] ?? ''), ENT_QUOTES, 'UTF-8');
$qty       = max(1, (int)($data['quantity'] ?? 1));

if (empty($productId)) {
    echo json_encode(['success' => false]); die;
}

// Verify product exists
$product = selectContent($conn, "panel_products", ["hash_id" => $productId, "visibility" => "show"]);
if (empty($product)) {
    echo json_encode(['success' => false, 'error' => 'Product not found']); die;
}

// Check if already in cart for this session
$existing = selectContent($conn, "read_cart", [
    "input_session_id" => $sessionId,
    "input_product_id" => $productId,
    "input_variant_id" => $variantId,
]);

if (!empty($existing)) {
    // Update quantity
    $newQty = (int)$existing[0]['input_quantity'] + $qty;
    updateContent($conn, "read_cart",
        ["input_quantity" => $newQty],
        ["id" => $existing[0]['id']]
    );
} else {
    // Add new cart row
    insertSafe($conn, "read_cart", [
        'hash_id'          => uniqid('cart_', true),
        'input_session_id' => $sessionId,
        'input_product_id' => $productId,
        'input_variant_id' => $variantId,
        'input_quantity'   => $qty,
        'input_price'      => $product[0]['input_price'],
        'visibility'       => 'show',
        'date_created'     => date('Y-m-d'),
        'time_created'     => date('H:i:s'),
        'created_by'       => $sessionId,
    ]);
}

// Return updated cart count
$cartItems = selectContent($conn, "read_cart", ["input_session_id" => $sessionId, "visibility" => "show"]);
$cartCount = count($cartItems);

echo json_encode(['success' => true, 'cart_count' => $cartCount]);
```

---

### 20.9 E-Commerce Routes to Add

Add these to `v1/routes/router.php`:

```php
// ── Shop pages ──────────────────────────────────────────────
case 'shop':
    include APP_PATH . "/views/shop.php";
    $is404 = false; die;

case 'cart':
    include APP_PATH . "/views/cart.php";
    $is404 = false; die;

case 'checkout':
    include APP_PATH . "/views/checkout.php";
    $is404 = false; die;

case 'order-confirm':
    include APP_PATH . "/views/order_confirm.php";
    $is404 = false; die;

// ── AJAX handlers ────────────────────────────────────────────
case 'cart/add':
    include APP_PATH . "/views/cart_add_backend.php";
    $is404 = false; die;

case 'cart/remove':
    include APP_PATH . "/views/cart_remove_backend.php";
    $is404 = false; die;

case 'cart/update':
    include APP_PATH . "/views/cart_update_backend.php";
    $is404 = false; die;

case 'checkout/process':
    include APP_PATH . "/views/checkout_process_backend.php";
    $is404 = false; die;

// ── Product detail ───────────────────────────────────────────
// (in the count($uri) > 2 block)
case 'product/' . $uri[2]:
    include APP_PATH . "/views/product_detail.php";
    $is404 = false; die;

case 'shop/' . $uri[2]:
    // Category filter: /shop/{category}
    include APP_PATH . "/views/shop.php";
    $is404 = false; die;
```

---

### 20.10 E-Commerce SQL — Mandatory Columns Summary

For every e-commerce table, include the standard 6 mandatory columns:

```sql
`id`           INT PRIMARY KEY AUTO_INCREMENT,
`hash_id`      VARCHAR(255) NOT NULL,   -- used in URLs and cross-references
`visibility`   VARCHAR(50) DEFAULT 'show',
`date_created` DATE NOT NULL,
`time_created` TIME NOT NULL,
`created_by`   VARCHAR(255) NOT NULL,
```

**E-commerce gotchas:**

| Mistake | Fix |
|---|---|
| Storing price as INT | Use `VARCHAR(50)` — prices are display values with decimals |
| Using `image_1` for products | Use `image_2` for products with galleries |
| No session check on cart routes | Always use `session_id()` to link cart rows to visitor |
| Saving order totals dynamically | Snapshot prices at order creation — don't recalculate from current product prices |
| No order status tracking | Always include `input_status` column with defined values: `pending`, `paid`, `shipped`, `delivered`, `cancelled` |
| Cart items not linked to sessions | Use `input_session_id = session_id()` to segregate carts per visitor |

---

*ADMC Framework — maintained by mckodev. For issues or contributions, contact the project maintainer.*
