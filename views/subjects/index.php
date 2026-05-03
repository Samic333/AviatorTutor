<?php
declare(strict_types=1);
/** @var array $enrolled */
/** @var array $pendingRequests */
/** @var array $availableToAdd */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');

// Map subject slug → study URL. Q400-pack lands on /systems for now (the
// subject view itself is a Phase 4 build); other packs go to /pricing
// because they aren't live yet.
$studyUrl = static function(array $row): string {
    $slug = (string) ($row['slug'] ?? '');
    if ($slug === 'q400-pack') return '/systems';
    return '/pricing';
};
?>

<section class="plt-page-head" style="margin-bottom:24px;">
  <h1 style="margin:0 0 6px;font-family:'DM Sans',Inter,sans-serif;font-size:28px;font-weight:700;color:#F1F5F9;">My Subjects</h1>
  <p style="margin:0;color:#94A3B8;font-size:14px;">Everything you're enrolled in. Add a new subject and we'll send you a quote.</p>
</section>

<?php if ($flashOk): ?>
  <div class="plt-flash plt-flash--ok" role="status" style="margin-bottom:16px;padding:12px 14px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.28);border-radius:10px;color:#A7F3D0;font-size:14px;">
    <?= $h($flashOk) ?>
  </div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="plt-flash plt-flash--err" role="alert" style="margin-bottom:16px;padding:12px 14px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.28);border-radius:10px;color:#FCA5A5;font-size:14px;">
    <?= $h($flashError) ?>
  </div>
<?php endif; ?>

<!-- Enrolled subjects -->
<section class="plt-glass-card" style="padding:24px;margin-bottom:24px;">
  <div class="plt-section-header" style="margin-bottom:16px;">
    <h2 class="plt-section-header__title" style="margin:0;font-family:'DM Sans',Inter,sans-serif;font-size:18px;font-weight:700;color:#F1F5F9;">Enrolled (<?= count($enrolled) ?>)</h2>
  </div>

  <?php if (empty($enrolled)): ?>
    <p style="margin:0;color:#94A3B8;font-size:14px;">You're not enrolled in any subjects yet. Add one below or visit <a href="/pricing" style="color:#38BDF8;font-weight:600;">pricing</a> to purchase a pack.</p>
  <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));gap:14px;">
      <?php foreach ($enrolled as $s):
        $color = $h($s['color_hex'] ?? '#38BDF8');
        $name  = $h($s['name'] ?? $s['slug']);
        $blurb = $h($s['short_blurb'] ?? '');
        $src   = (string) ($s['source'] ?? 'purchase');
        $badge = $src === 'subscription' ? 'Subscription' : ($src === 'admin' ? 'Admin' : 'Owned');
      ?>
        <a href="<?= $h($studyUrl($s)) ?>" class="plt-subject-card" style="display:block;padding:18px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:14px;text-decoration:none;color:inherit;transition:all .15s ease;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <div style="width:38px;height:38px;border-radius:10px;background:<?= $color ?>22;color:<?= $color ?>;display:flex;align-items:center;justify-content:center;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
            </div>
            <span style="padding:3px 9px;background:<?= $color ?>22;color:<?= $color ?>;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;"><?= $h($badge) ?></span>
          </div>
          <h3 style="margin:0 0 4px;font-family:'DM Sans',Inter,sans-serif;font-size:15px;font-weight:700;color:#F1F5F9;"><?= $name ?></h3>
          <?php if ($blurb !== ''): ?>
            <p style="margin:0;font-size:12.5px;color:#94A3B8;line-height:1.5;"><?= $blurb ?></p>
          <?php endif; ?>
          <div style="margin-top:14px;font-size:13px;color:<?= $color ?>;font-weight:600;">Continue studying →</div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php if (!empty($pendingRequests)): ?>
<!-- Pending requests -->
<section class="plt-glass-card" style="padding:20px;margin-bottom:24px;">
  <h2 style="margin:0 0 12px;font-family:'DM Sans',Inter,sans-serif;font-size:16px;font-weight:700;color:#F1F5F9;">Pending requests</h2>
  <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px;">
    <?php foreach ($pendingRequests as $r): ?>
      <li style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;">
        <span style="color:#E2E8F0;font-size:14px;"><?= $h($r['requested_subject']) ?></span>
        <span style="padding:3px 9px;background:rgba(245,158,11,0.18);color:#FBBF24;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;"><?= $h($r['status']) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>

<!-- Add Subject -->
<section class="plt-glass-card" id="add-subject" style="padding:24px;">
  <div class="plt-section-header" style="margin-bottom:14px;">
    <h2 class="plt-section-header__title" style="margin:0;font-family:'DM Sans',Inter,sans-serif;font-size:18px;font-weight:700;color:#F1F5F9;">Add a new subject</h2>
  </div>
  <p style="margin:0 0 16px;color:#94A3B8;font-size:14px;">Pick from the catalog or describe what you want to study. Captain Samic gets your request and sends you a quote.</p>

  <form method="post" action="/my-subjects/request" style="display:grid;gap:14px;">
    <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">

    <?php if (!empty($availableToAdd)): ?>
      <label style="display:block;font-size:13px;color:#94A3B8;font-weight:600;margin-bottom:4px;">From the catalog</label>
      <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:10px;">
        <?php foreach ($availableToAdd as $s):
          $color  = $h($s['color_hex'] ?? '#38BDF8');
          $name   = $h($s['name'] ?? $s['slug']);
          $price  = (float) ($s['price_usd'] ?? 0);
          $coming = !empty($s['is_coming_soon']);
        ?>
          <label style="position:relative;display:block;padding:14px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:12px;cursor:pointer;">
            <input type="radio" name="subject_slug" value="<?= $h($s['slug']) ?>" style="position:absolute;top:12px;right:12px;accent-color:<?= $color ?>;">
            <h3 style="margin:0 0 4px;font-family:'DM Sans',Inter,sans-serif;font-size:14px;font-weight:700;color:#F1F5F9;"><?= $name ?></h3>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;">
              <span style="color:<?= $color ?>;font-weight:700;">$<?= number_format($price, 0) ?></span>
              <?php if ($coming): ?>
                <span style="color:#FBBF24;font-weight:600;">Coming soon</span>
              <?php endif; ?>
            </div>
          </label>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <details style="margin-top:8px;">
      <summary style="cursor:pointer;color:#38BDF8;font-size:13px;font-weight:600;">Or describe your own subject</summary>
      <div style="display:grid;gap:10px;margin-top:10px;">
        <input type="text" name="subject_name" placeholder="e.g. Q400 line maintenance overview"
               style="padding:10px 12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;color:#F1F5F9;font-size:14px;">
        <textarea name="notes" rows="3" placeholder="Anything we should know? (target exam, deadline, depth)…"
                  style="padding:10px 12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;color:#F1F5F9;font-size:14px;font-family:inherit;resize:vertical;"></textarea>
      </div>
    </details>

    <div style="display:flex;justify-content:flex-end;">
      <button type="submit" class="plt-btn plt-btn--primary" style="padding:10px 18px;background:linear-gradient(135deg,#38BDF8,#0EA5E9);color:#0F172A;border:0;border-radius:10px;font-weight:700;cursor:pointer;font-size:14px;">
        Request access
      </button>
    </div>
  </form>
</section>
