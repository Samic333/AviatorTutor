<?php
declare(strict_types=1);
/**
 * AI Q&A placeholder panel.
 * Include from any study view with:
 *   <?= $this->view->render('partials/ai-panel'); ?>
 * or directly:
 *   include BASE_PATH . '/views/partials/ai-panel.php';
 */
?>
<section class="plt-glass-card" style="padding:24px;border:1px solid rgba(56,189,248,0.18);background:linear-gradient(135deg, rgba(56,189,248,0.05), rgba(255,255,255,0.02));">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
    <div style="width:36px;height:36px;border-radius:10px;background:rgba(56,189,248,0.14);color:var(--plt-sky);display:flex;align-items:center;justify-content:center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </div>
    <div>
      <h3 style="margin:0;font-family:var(--plt-font-head);font-size:15px;font-weight:700;color:var(--plt-text);">Ask AI</h3>
      <p style="margin:0;font-size:11.5px;color:var(--plt-text-muted);">Type-aware aviation Q&amp;A — coming soon.</p>
    </div>
    <span style="margin-left:auto;padding:3px 8px;background:rgba(201,168,76,0.12);color:var(--plt-gold);font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;border-radius:6px;">Beta</span>
  </div>

  <div style="position:relative;">
    <textarea disabled placeholder="Ask anything about this system — limitations, abnormals, components…"
              style="width:100%;min-height:80px;padding:12px;background:rgba(0,0,0,0.18);border:1px solid var(--plt-glass-border);border-radius:8px;color:var(--plt-text);font-family:inherit;font-size:13px;resize:vertical;opacity:0.55;"></textarea>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
      <span style="font-size:11.5px;color:var(--plt-text-muted);">Powered by AviatorTutor AI</span>
      <button disabled class="plt-btn plt-btn--primary plt-btn--sm" style="opacity:0.55;cursor:not-allowed;">Ask</button>
    </div>
  </div>
</section>
