<?php
declare(strict_types=1);
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
    <h1 class="plt-page-header__title">Change Password</h1>
    <p class="plt-page-header__sub">Update your account password. You'll need your current password to make this change.</p>
  </div>
  <div>
    <a href="/profile" class="plt-btn plt-btn--ghost plt-btn--sm">← Back to Profile</a>
  </div>
</div>

<div class="plt-glass-card" style="padding:28px;max-width:480px;">
  <form method="post" action="/profile/password" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="plt-form-group">
      <label class="plt-label" for="current_password">Current Password</label>
      <input class="plt-input" type="password" name="current_password" id="current_password"
             required autocomplete="current-password" placeholder="Enter your current password">
    </div>

    <hr class="plt-divider">

    <div class="plt-form-group">
      <label class="plt-label" for="new_password">New Password</label>
      <input class="plt-input" type="password" name="new_password" id="new_password"
             required minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
    </div>

    <div class="plt-form-group">
      <label class="plt-label" for="confirm_password">Confirm New Password</label>
      <input class="plt-input" type="password" name="confirm_password" id="confirm_password"
             required autocomplete="new-password" placeholder="Repeat new password">
      <span id="match-hint" style="font-size:12px;margin-top:4px;display:none;"></span>
    </div>

    <div style="margin-top:8px;">
      <button type="submit" class="plt-btn plt-btn--primary">Update Password</button>
    </div>
  </form>
</div>

<script>
(function() {
  var np = document.getElementById('new_password');
  var cp = document.getElementById('confirm_password');
  var hint = document.getElementById('match-hint');

  function checkMatch() {
    if (!cp.value) { hint.style.display = 'none'; return; }
    if (np.value === cp.value) {
      hint.style.display = 'block';
      hint.style.color   = 'var(--plt-success)';
      hint.textContent   = '✓ Passwords match';
    } else {
      hint.style.display = 'block';
      hint.style.color   = 'var(--plt-danger)';
      hint.textContent   = 'Passwords do not match';
    }
  }

  np.addEventListener('input', checkMatch);
  cp.addEventListener('input', checkMatch);
})();
</script>
