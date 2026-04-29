<?php
declare(strict_types=1);
/** @var array $user */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */
?>

<?php if ($flashOk): ?>
  <div class="plt-flash plt-flash--ok" style="margin:0 0 20px;"><?= htmlspecialchars($flashOk) ?></div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="plt-flash plt-flash--error" style="margin:0 0 20px;"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="plt-page-header">
  <div>
    <h1 class="plt-page-header__title">Profile Settings</h1>
    <p class="plt-page-header__sub">Manage your personal information and avatar.</p>
  </div>
  <div>
    <a href="/profile/password" class="plt-btn plt-btn--ghost plt-btn--sm">Change Password →</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:900px;">

  <!-- Avatar card -->
  <div class="plt-glass-card" style="padding:24px;">
    <h2 style="font-family:var(--plt-font-head);font-size:15px;font-weight:700;margin:0 0 20px;color:var(--plt-text);">Profile Photo</h2>

    <div class="plt-avatar-upload">
      <div class="plt-avatar-upload__preview" id="avatar-preview">
        <?php if (!empty($user['avatar'])): ?>
          <img src="/assets/uploads/avatars/<?= htmlspecialchars((string)$user['avatar']) ?>" alt="Avatar">
        <?php else: ?>
          <?= strtoupper(mb_substr((string)($user['name'] ?? 'P'), 0, 1)) ?>
        <?php endif; ?>
      </div>
      <div>
        <p style="margin:0 0 8px;font-size:13.5px;color:var(--plt-text);">Upload a profile photo</p>
        <p style="margin:0;font-size:12px;color:var(--plt-text-muted);">JPEG, PNG or WebP &middot; Max 5 MB</p>
      </div>
    </div>

    <form method="post" action="/profile/avatar" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <input type="file" name="avatar" id="avatar-file" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" onchange="previewAvatar(this)">
      <div style="display:flex;gap:8px;">
        <button type="button" class="plt-btn plt-btn--ghost plt-btn--sm" onclick="document.getElementById('avatar-file').click();">Choose Photo</button>
        <button type="submit" class="plt-btn plt-btn--primary plt-btn--sm" id="avatar-upload-btn" style="display:none;">Upload</button>
      </div>
    </form>
  </div>

  <!-- Info card -->
  <div class="plt-glass-card" style="padding:24px;">
    <h2 style="font-family:var(--plt-font-head);font-size:15px;font-weight:700;margin:0 0 20px;color:var(--plt-text);">Personal Information</h2>

    <form method="post" action="/profile/update">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="plt-form-group">
        <label class="plt-label" for="name">Full Name</label>
        <input class="plt-input" type="text" name="name" id="name"
               value="<?= htmlspecialchars((string)($user['name'] ?? '')) ?>"
               required maxlength="120" autocomplete="name">
      </div>

      <div class="plt-form-group">
        <label class="plt-label" for="email">Email Address</label>
        <input class="plt-input" type="email" name="email" id="email"
               value="<?= htmlspecialchars((string)($user['email'] ?? '')) ?>"
               required maxlength="255" autocomplete="email">
      </div>

      <div class="plt-form-group" style="margin-bottom:0;">
        <label class="plt-label">Member since</label>
        <div style="padding:10px 14px;background:rgba(255,255,255,0.03);border:1px solid var(--plt-glass-border);border-radius:var(--plt-radius);color:var(--plt-text-muted);font-size:14px;">
          <?= !empty($user['created_at']) ? htmlspecialchars(date('F j, Y', strtotime((string)$user['created_at']))) : '—' ?>
        </div>
      </div>

      <div style="margin-top:20px;">
        <button type="submit" class="plt-btn plt-btn--primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var preview = document.getElementById('avatar-preview');
    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
    document.getElementById('avatar-upload-btn').style.display = 'inline-flex';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
