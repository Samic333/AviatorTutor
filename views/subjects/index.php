<?php
declare(strict_types=1);
/** @var array $enrolled */
/** @var array $pendingRequests */
/** @var array $availableToAdd */
/** @var string $csrf_token */
/** @var ?string $flashOk */
/** @var ?string $flashError */

$h = static fn(?string $s): string => htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');

// Map subject slug → study URL. Prefers the per-subject continue URL
// computed by SubjectsController (most recent lesson), falling back to
// /systems for Q400 and /pricing for unowned packs.
$studyUrl = static function(array $row): string {
    if (!empty($row['continue_url'])) return (string) $row['continue_url'];
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
        $pct   = (int) ($s['progress_pct'] ?? 0);
        $due   = (int) ($s['flashcards_due'] ?? 0);
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
            <p style="margin:0 0 12px;font-size:12.5px;color:#94A3B8;line-height:1.5;"><?= $blurb ?></p>
          <?php endif; ?>

          <!-- Progress bar -->
          <div style="margin-top:6px;">
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11.5px;color:#94A3B8;margin-bottom:4px;">
              <span><?= $pct ?>% studied</span>
              <?php if ($due > 0): ?><span><?= $due ?> due</span><?php endif; ?>
            </div>
            <div style="height:4px;border-radius:999px;background:rgba(255,255,255,0.08);overflow:hidden;">
              <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;transition:width .25s ease;"></div>
            </div>
          </div>

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

<!-- Add Subject — visible CTA tile that opens a modal listing every subject -->
<section style="margin-top:8px;">
  <button type="button" id="add-subject-trigger"
          style="width:100%;padding:18px;background:linear-gradient(135deg,rgba(56,189,248,0.10),rgba(168,85,247,0.10));
                 border:1px dashed rgba(56,189,248,0.45);border-radius:14px;color:#F1F5F9;
                 font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;">
    <span style="font-size:20px;line-height:1;">+</span> Add a new subject
  </button>
</section>

<!-- Modal: subject catalog -->
<div id="add-subject-modal" class="add-modal" hidden role="dialog" aria-labelledby="add-modal-title" aria-modal="true">
  <div class="add-modal__backdrop" data-add-close></div>
  <div class="add-modal__panel">
    <header class="add-modal__head">
      <h2 id="add-modal-title">Add a new subject</h2>
      <button type="button" class="add-modal__x" data-add-close aria-label="Close">×</button>
    </header>
    <p class="add-modal__intro">Pick from the catalog or describe your own. Captain Samic gets your request and sends you a quote.</p>

    <form method="post" action="/my-subjects/request" class="add-modal__form">
      <input type="hidden" name="csrf_token" value="<?= $h($csrf_token) ?>">

      <?php if (!empty($availableToAdd)): ?>
        <div class="add-modal__grid">
          <?php foreach ($availableToAdd as $s):
            $color  = $h($s['color_hex'] ?? '#38BDF8');
            $name   = $h($s['name'] ?? $s['slug']);
            $price  = (float) ($s['price_usd'] ?? 0);
            $coming = !empty($s['is_coming_soon']);
            $cat    = (string) ($s['category'] ?? '');
            $catLabel = $cat === 'aircraft_pack' ? 'Aircraft' : ($cat === 'airline_interview' ? 'Interview' : 'Subject');
          ?>
            <label class="add-tile" style="--add-accent: <?= $color ?>;">
              <input type="radio" name="subject_slug" value="<?= $h($s['slug']) ?>">
              <span class="add-tile__cat"><?= $h($catLabel) ?></span>
              <h3 class="add-tile__name"><?= $name ?></h3>
              <div class="add-tile__row">
                <span class="add-tile__price">$<?= number_format($price, 0) ?></span>
                <?php if ($coming): ?>
                  <span class="add-tile__soon">Coming soon</span>
                <?php endif; ?>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="color:#94A3B8;font-size:13.5px;">You're enrolled in everything in the catalog already. Describe your own request below.</p>
      <?php endif; ?>

      <details style="margin-top:14px;">
        <summary>Or describe your own subject</summary>
        <div class="add-modal__custom">
          <input type="text" name="subject_name" placeholder="e.g. Q400 line maintenance overview">
          <textarea name="notes" rows="3" placeholder="Anything we should know? (target exam, deadline, depth)…"></textarea>
        </div>
      </details>

      <footer class="add-modal__foot">
        <button type="button" class="add-btn-cancel" data-add-close>Cancel</button>
        <button type="submit" class="add-btn-submit">Request access</button>
      </footer>
    </form>
  </div>
</div>

<style>
.add-modal { position: fixed; inset: 0; z-index: 80; display: flex; align-items: center; justify-content: center; padding: 24px; }
.add-modal[hidden] { display: none; }
.add-modal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.55); }
.add-modal__panel { position: relative; width: min(720px, 100%); max-height: 86vh; overflow: hidden; display: flex; flex-direction: column;
                    background: var(--thm-bg-2, #1E293B); border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
                    border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
.add-modal__head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;
                   border-bottom: 1px solid var(--thm-border, rgba(255,255,255,0.08)); }
.add-modal__head h2 { margin: 0; font-size: 17px; font-weight: 700; }
.add-modal__x { background: transparent; border: 0; color: #94A3B8; font-size: 24px; cursor: pointer; padding: 4px 10px; line-height: 1; min-width: 36px; min-height: 36px; }
.add-modal__intro { margin: 0; padding: 14px 20px 6px; color: #94A3B8; font-size: 13.5px; }
.add-modal__form { padding: 8px 20px 20px; overflow-y: auto; }
.add-modal__grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; margin-top: 10px; }

.add-tile { position: relative; display: flex; flex-direction: column; gap: 4px;
            padding: 14px; border-radius: 12px; cursor: pointer; min-height: 96px;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
            border-left: 4px solid var(--add-accent, #38BDF8); }
.add-tile:has(input:checked) { background: rgba(56,189,248,0.14); border-color: var(--add-accent, #38BDF8); }
.add-tile input { position: absolute; opacity: 0; pointer-events: none; }
.add-tile__cat { font-size: 10.5px; letter-spacing: 0.06em; text-transform: uppercase; color: var(--add-accent, #38BDF8); font-weight: 700; }
.add-tile__name { margin: 0; font-size: 14px; font-weight: 700; }
.add-tile__row { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; margin-top: auto; }
.add-tile__price { color: var(--add-accent, #38BDF8); font-weight: 700; }
.add-tile__soon  { color: #FBBF24; font-weight: 600; }

.add-modal__form details summary { cursor: pointer; font-size: 13px; color: var(--thm-accent, #38BDF8); font-weight: 600; padding: 6px 0; }
.add-modal__custom { display: grid; gap: 8px; margin-top: 8px; }
.add-modal__custom input, .add-modal__custom textarea {
    padding: 10px 12px; border-radius: 10px; font-size: 14px;
    background: rgba(255,255,255,0.03); border: 1px solid var(--thm-border, rgba(255,255,255,0.08));
    color: var(--thm-fg, #F1F5F9); font-family: inherit;
}
.add-modal__foot { display: flex; justify-content: flex-end; gap: 10px; padding-top: 14px; }
.add-btn-cancel, .add-btn-submit { padding: 10px 18px; border-radius: 10px; border: 0; font-weight: 700; cursor: pointer; font-size: 14px; min-height: 44px; }
.add-btn-cancel { background: rgba(255,255,255,0.06); color: var(--thm-fg, #F1F5F9); }
.add-btn-submit { background: linear-gradient(135deg,#38BDF8,#0EA5E9); color: #0F172A; }
</style>

<script>
(function () {
  var modal = document.getElementById('add-subject-modal');
  var trigger = document.getElementById('add-subject-trigger');
  if (!modal || !trigger) return;

  function open() { modal.hidden = false; document.body.style.overflow = 'hidden'; }
  function close() { modal.hidden = true; document.body.style.overflow = ''; }

  trigger.addEventListener('click', open);
  modal.addEventListener('click', function (e) {
    if (e.target.closest('[data-add-close]')) { e.preventDefault(); close(); }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.hidden) close();
  });

  // Auto-open when ?add=<slug> is in the URL (Phase 4 promo CTA target).
  var params = new URLSearchParams(window.location.search);
  if (params.get('add') || window.location.hash === '#add-subject') {
    open();
    var slug = params.get('add');
    if (slug) {
      var radio = modal.querySelector('input[name="subject_slug"][value="' + CSS.escape(slug) + '"]');
      if (radio) radio.checked = true;
    }
  }
})();
</script>
