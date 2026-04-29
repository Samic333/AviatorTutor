<?php
declare(strict_types=1);
/** @var string $csrf_token */
?>
<div class="adm-page-header">
  <div>
    <h1 class="adm-page-header__title">Import Tool</h1>
    <p class="adm-page-header__sub">Upload PDFs and images to create flashcards and lessons. (Stub — feature in progress)</p>
  </div>
</div>

<div class="adm-panel" style="max-width:640px;">
  <div class="adm-panel__header">
    <h2 class="adm-panel__title">Import Content</h2>
  </div>
  <div class="adm-panel__body">
    <form method="POST" enctype="multipart/form-data" action="/admin/import/process">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

      <div class="adm-form-group">
        <label class="adm-label">File (PDF, PNG, JPG — max 50 MB)</label>
        <input type="file" name="import_file" class="adm-input" accept=".pdf,.png,.jpg,.jpeg">
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Content Type</label>
        <select name="content_type" class="adm-select">
          <option value="flashcards">Create Flashcards</option>
          <option value="lesson">Create Lesson</option>
        </select>
      </div>

      <div class="adm-form-group">
        <label class="adm-label">Title (optional)</label>
        <input type="text" name="title" class="adm-input" placeholder="Leave blank for auto-generated">
      </div>

      <button type="submit" class="adm-btn adm-btn--primary">Import File</button>
    </form>

    <div style="margin-top:24px;padding:16px;background:var(--adm-gold-soft);border:1px solid var(--adm-gold-dim);border-radius:var(--adm-radius);">
      <p style="margin:0;color:var(--adm-gold);font-size:13px;">
        <strong>Note:</strong> This import tool is a stub. Full AI-powered content extraction will be available in a future update.
      </p>
    </div>
  </div>
</div>
