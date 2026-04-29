<?php
declare(strict_types=1);
/** @var array $subjects */
/** @var string $csrf_token */

$categoryLabels = [
    'aircraft_pack'     => 'Aircraft Packs',
    'airline_interview' => 'Airline Interview Packs',
    'aviation_subject'  => 'Aviation Subject Packs',
];

$grouped = [];
foreach ($subjects as $s) {
    $cat = (string) ($s['category'] ?? 'other');
    $grouped[$cat][] = $s;
}
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Pricing</h1>
    <p class="adm-page-header__sub">Manage subject prices, publish state, and coming-soon flags.</p>
  </div>
</div>

<?php if (empty($subjects)): ?>
  <div class="adm-panel" style="max-width:680px;">
    <div class="adm-panel__body" style="padding:32px;text-align:center;">
      <p class="adm-muted" style="margin:0 0 16px;">No subjects found. Run the Phase&nbsp;4 database migration to create the subjects table and seed pricing.</p>
      <div style="padding:16px;background:var(--adm-gold-soft);border:1px solid var(--adm-gold-dim);border-radius:var(--adm-radius);text-align:left;">
        <p style="margin:0 0 8px;color:var(--adm-gold);font-weight:600;font-size:13px;">Migration file</p>
        <code style="font-size:12px;color:var(--adm-text);">database/migrations/2026_04_28_subjects_and_purchases.sql</code>
      </div>
    </div>
  </div>
<?php else: ?>
  <form method="post" action="/admin/pricing/update">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <?php foreach ($categoryLabels as $key => $label): ?>
      <?php if (empty($grouped[$key])) continue; ?>
      <div class="adm-panel" style="margin-bottom:20px;">
        <div class="adm-panel__head">
          <h2 class="adm-panel__title"><?= htmlspecialchars($label) ?></h2>
          <span class="adm-muted adm-mono" style="font-size:12px;"><?= count($grouped[$key]) ?> subjects</span>
        </div>
        <div class="adm-panel__body" style="padding:0;">
          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr>
                  <th style="width:40%;">Subject</th>
                  <th>Slug</th>
                  <th style="width:120px;">Price&nbsp;(USD)</th>
                  <th style="width:90px;text-align:center;">Published</th>
                  <th style="width:110px;text-align:center;">Coming&nbsp;Soon</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($grouped[$key] as $s): ?>
                  <tr>
                    <td><strong style="color:var(--adm-text);"><?= htmlspecialchars((string)$s['name']) ?></strong></td>
                    <td class="adm-muted adm-mono" style="font-size:12px;"><?= htmlspecialchars((string)$s['slug']) ?></td>
                    <td>
                      <input type="number" name="price[<?= (int)$s['id'] ?>]"
                             value="<?= number_format((float)$s['price_usd'], 2, '.', '') ?>"
                             step="0.01" min="0"
                             class="adm-input" style="width:90px;padding:5px 8px;font-size:12px;">
                    </td>
                    <td style="text-align:center;">
                      <input type="checkbox" name="published[<?= (int)$s['id'] ?>]" value="1"
                             <?= $s['is_published'] ? 'checked' : '' ?>>
                    </td>
                    <td style="text-align:center;">
                      <input type="checkbox" name="coming_soon[<?= (int)$s['id'] ?>]" value="1"
                             <?= !empty($s['is_coming_soon']) ? 'checked' : '' ?>>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <div style="position:sticky;bottom:0;padding:16px;background:var(--adm-bg);border-top:1px solid var(--adm-border);">
      <button type="submit" class="adm-btn adm-btn--primary">Save All Changes</button>
    </div>
  </form>
<?php endif; ?>
