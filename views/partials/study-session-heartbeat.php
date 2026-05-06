<?php
declare(strict_types=1);
/**
 * Phase 9 — emits a navigator.sendBeacon to /api/study-sessions/end on
 * page hide so the most recent study_sessions row gets ended_at +
 * duration_secs stamped. Lets the /progress total-study-time stat
 * actually accumulate hours.
 *
 * Pass:
 *   $sessionType  string  one of: detail, revision, flashcard, quiz, diagram
 *   $sessionSystemId ?int  optional system id (matches recordStudySession's call)
 */
$_st = (string) ($sessionType ?? '');
$_si = isset($sessionSystemId) ? (int) $sessionSystemId : 0;
if ($_st === '') return;
?>
<script>
(function () {
  var sent = false;
  var payload = new URLSearchParams();
  payload.set('type', <?= json_encode($_st) ?>);
  <?php if ($_si > 0): ?>payload.set('system_id', <?= json_encode((string)$_si) ?>);<?php endif; ?>

  function endSession() {
    if (sent) return;
    sent = true;
    try {
      if (navigator.sendBeacon) {
        var blob = new Blob([payload.toString()], { type: 'application/x-www-form-urlencoded' });
        navigator.sendBeacon('/api/study-sessions/end', blob);
      } else {
        fetch('/api/study-sessions/end', {
          method: 'POST', credentials: 'same-origin', keepalive: true,
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: payload.toString()
        }).catch(function(){});
      }
    } catch (e) { /* never block unload */ }
  }

  // pagehide is the most reliable across browsers (including iOS Safari).
  window.addEventListener('pagehide', endSession);
  // beforeunload as a desktop fallback for older Safari.
  window.addEventListener('beforeunload', endSession);
  // Tab hidden also closes the session on mobile when the OS suspends.
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') endSession();
  });
})();
</script>
