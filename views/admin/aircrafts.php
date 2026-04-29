<?php
declare(strict_types=1);
/** @var array $aircrafts */
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Aircraft Catalog</h1>
    <p class="adm-page-header__sub">Set status and ordering. Cockpit images are managed via the cockpit upload tool.</p>
  </div>
</div>

<div class="adm-panel">
  <div class="adm-table-wrap">
    <table class="adm-table">
      <thead>
        <tr>
          <th>Aircraft</th>
          <th>Slug</th>
          <th>Status / Order</th>
          <th class="num">Waitlist</th>
          <th>Cockpit</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($aircrafts as $a): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars((string)$a['name']) ?></strong>
              <span class="adm-muted"> (<?= htmlspecialchars((string)$a['short_name']) ?>)</span>
            </td>
            <td class="adm-mono"><?= htmlspecialchars((string)$a['slug']) ?></td>
            <td>
              <form method="post" action="/admin/aircrafts/update" style="display:inline-flex;gap:8px;align-items:center;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                <select name="status" class="adm-select" style="width:auto;padding:5px 8px;font-size:12px;">
                  <option value="live"        <?= $a['status'] === 'live'        ? 'selected' : '' ?>>live</option>
                  <option value="beta"        <?= $a['status'] === 'beta'        ? 'selected' : '' ?>>beta</option>
                  <option value="coming_soon" <?= $a['status'] === 'coming_soon' ? 'selected' : '' ?>>coming_soon</option>
                  <option value="archived"    <?= $a['status'] === 'archived'    ? 'selected' : '' ?>>archived</option>
                </select>
                <input type="number" name="sort_order" class="adm-input" value="<?= (int)$a['sort_order'] ?>" style="width:64px;padding:5px 8px;font-size:12px;">
                <button type="submit" class="adm-btn adm-btn--ghost adm-btn--sm">Save</button>
              </form>
            </td>
            <td class="num adm-mono"><?= (int)$a['waitlist_count'] ?></td>
            <td>
              <?php if (!empty($a['cockpit_image_path'])): ?>
                <span class="adm-badge adm-badge--success">SET</span>
              <?php else: ?>
                <span class="adm-muted">—</span>
              <?php endif; ?>
            </td>
            <td><a href="/aircraft/<?= htmlspecialchars((string)$a['slug']) ?>" class="adm-btn adm-btn--ghost adm-btn--sm">View →</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
