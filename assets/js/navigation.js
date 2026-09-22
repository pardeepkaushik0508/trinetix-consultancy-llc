/**
 * Trinetix — accessible mobile navigation
 * aria-expanded / aria-controls · Escape · outside click · link close · body scroll lock
 */
(function () {
  'use strict';

  function onReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  onReady(function () {
    var header = document.getElementById('header') || document.querySelector('.site-header');
    var toggle = document.getElementById('menuToggle') || document.querySelector('.menu-toggle');
    var nav =
      document.getElementById('primaryNav') ||
      document.getElementById('site-navigation') ||
      (header && header.querySelector('.nav'));

    if (!header || !toggle) return;

    if (nav && !nav.id) {
      nav.id = 'primaryNav';
    }

    var navId = nav ? nav.id : '';
    if (navId) {
      toggle.setAttribute('aria-controls', navId);
    }
    toggle.setAttribute('aria-expanded', 'false');
    if (!toggle.getAttribute('aria-label')) {
      toggle.setAttribute('aria-label', 'Open navigation');
    }

    function isOpen() {
      return header.classList.contains('open');
    }

    function setOpen(open) {
      header.classList.toggle('open', open);
      document.body.classList.toggle('menu-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    }

    function closeMenu() {
      setOpen(false);
    }

    function toggleMenu() {
      setOpen(!isOpen());
    }

    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      toggleMenu();
    });

    if (nav) {
      nav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen()) {
        closeMenu();
        toggle.focus();
      }
    });

    document.addEventListener('click', function (e) {
      if (!isOpen()) return;
      var target = e.target;
      if (header.contains(target)) return;
      closeMenu();
    });

    window.addEventListener(
      'resize',
      function () {
        if (window.matchMedia('(min-width: 981px)').matches && isOpen()) {
          closeMenu();
        }
      },
      { passive: true }
    );
  });
})();
