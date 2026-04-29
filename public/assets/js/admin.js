/* AviatorTutor Admin — Sidebar + UI interactions */
(function () {
  'use strict';

  /* ---- Sidebar mobile toggle ---- */
  const sidebar  = document.querySelector('.adm-sidebar');
  const overlay  = document.querySelector('.adm-overlay');
  const hamburger = document.querySelector('.adm-topbar__hamburger');

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
  }

  hamburger?.addEventListener('click', function () {
    if (sidebar?.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  overlay?.addEventListener('click', closeSidebar);

  /* ---- Active nav link highlighting ---- */
  const path = window.location.pathname;
  document.querySelectorAll('.adm-nav-link').forEach(function (link) {
    const href = link.getAttribute('href');
    if (!href) return;
    if (href === '/admin' && path === '/admin') {
      link.classList.add('active');
    } else if (href !== '/admin' && path.startsWith(href)) {
      link.classList.add('active');
    }
  });

  /* ---- Auto-dismiss flash messages ---- */
  document.querySelectorAll('.adm-flash').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 5000);
  });

  /* ---- Confirm destructive actions ---- */
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      const msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  });

  /* ---- Inline select-then-submit ---- */
  document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
    el.addEventListener('change', function () {
      el.closest('form')?.submit();
    });
  });

})();
