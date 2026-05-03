/* AviatorTutor — toast notifications
 *
 * Tiny, dependency-free toast region. Pages opt in by including this script
 * (the pilot layout does so for every authenticated page) and an empty
 * <div id="toast-region"> is auto-inserted into the body on first call.
 *
 *   Toast.success('Saved.')
 *   Toast.error('Could not save — try again.')
 *   Toast.info('Welcome back.')
 *
 * Toasts auto-dismiss after 4s; click to dismiss early.
 */
(function () {
  'use strict';

  function ensureRegion() {
    var region = document.getElementById('toast-region');
    if (region) return region;
    region = document.createElement('div');
    region.id = 'toast-region';
    region.setAttribute('role', 'status');
    region.setAttribute('aria-live', 'polite');
    region.style.cssText = [
      'position:fixed', 'z-index:9999',
      'top:16px', 'right:16px',
      'display:flex', 'flex-direction:column', 'gap:8px',
      'pointer-events:none',
      'max-width:min(380px, calc(100vw - 32px))'
    ].join(';');
    document.body.appendChild(region);
    return region;
  }

  function show(level, message, opts) {
    if (!message) return;
    opts = opts || {};
    var region = ensureRegion();
    var palette = {
      success: { bg: 'rgba(16,185,129,0.18)',  fg: '#A7F3D0', bd: 'rgba(16,185,129,0.4)' },
      error:   { bg: 'rgba(239,68,68,0.18)',   fg: '#FCA5A5', bd: 'rgba(239,68,68,0.4)'  },
      info:    { bg: 'rgba(56,189,248,0.18)',  fg: '#BAE6FD', bd: 'rgba(56,189,248,0.4)' }
    }[level] || { bg: 'rgba(148,163,184,0.18)', fg: '#E2E8F0', bd: 'rgba(148,163,184,0.4)' };

    var el = document.createElement('div');
    el.className = 'toast toast--' + level;
    el.style.cssText = [
      'pointer-events:auto', 'cursor:pointer',
      'padding:12px 14px', 'border-radius:10px',
      'background:' + palette.bg, 'color:' + palette.fg,
      'border:1px solid ' + palette.bd,
      'font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Inter,system-ui,sans-serif',
      'font-size:13.5px', 'font-weight:600',
      'box-shadow:0 6px 24px rgba(0,0,0,0.25)',
      'opacity:0', 'transform:translateY(-6px)',
      'transition:opacity 180ms ease, transform 180ms ease'
    ].join(';');
    el.textContent = String(message);

    region.appendChild(el);
    // Force reflow before transitioning in.
    void el.offsetWidth;
    el.style.opacity = '1';
    el.style.transform = 'translateY(0)';

    var ttl = typeof opts.ttl === 'number' ? opts.ttl : 4000;
    var dismiss = function () {
      el.style.opacity = '0';
      el.style.transform = 'translateY(-6px)';
      setTimeout(function () {
        if (el.parentNode) el.parentNode.removeChild(el);
      }, 200);
    };
    el.addEventListener('click', dismiss);
    setTimeout(dismiss, ttl);
  }

  var Toast = {
    success: function (m, o) { show('success', m, o); },
    error:   function (m, o) { show('error',   m, o); },
    info:    function (m, o) { show('info',    m, o); },
    show:    show
  };
  window.Toast = Toast;

  // Surface uncaught errors as a visible toast so users notice that
  // something went wrong, instead of staring at silent dead UI.
  window.addEventListener('error', function (ev) {
    if (!ev || !ev.error) return;
    Toast.error('Something went wrong — refresh if it sticks.', { ttl: 6000 });
  });
  window.addEventListener('unhandledrejection', function (ev) {
    Toast.error('A background request failed.', { ttl: 5000 });
  });
})();
