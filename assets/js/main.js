/**
 * Trinetix — core interactions
 * Header scroll state, reading progress, reveal/stagger, pills.
 * Null-safe · no jQuery · respects prefers-reduced-motion.
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function onReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  onReady(function () {
    var header = document.getElementById('header') || document.querySelector('.site-header');
    var progress = document.getElementById('progress');
    var heroBg = document.getElementById('heroBg');

    function onScroll() {
      var y = window.scrollY || window.pageYOffset || 0;

      if (header) {
        var scrolled = y > 24;
        header.classList.toggle('fixed', scrolled);
        header.classList.toggle('is-scrolled', scrolled);
      }

      if (progress) {
        var max = document.documentElement.scrollHeight - window.innerHeight;
        var pct = max > 0 ? (y / max) * 100 : 0;
        progress.style.width = pct + '%';
      }

      if (heroBg && !reduceMotion && y < 900) {
        heroBg.style.transform = 'scale(1.02) translateY(' + y * 0.035 + 'px)';
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* Reveal / stagger */
    var targets = document.querySelectorAll('.reveal, .stagger');
    if (reduceMotion) {
      targets.forEach(function (el) {
        el.classList.add('show');
      });
    } else if (targets.length && 'IntersectionObserver' in window) {
      var io = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('show');
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.12, rootMargin: '0px 0px -20px' }
      );
      targets.forEach(function (el) {
        io.observe(el);
      });
    } else {
      targets.forEach(function (el) {
        el.classList.add('show');
      });
    }

    /* Pills (filter / tab chips) */
    document.querySelectorAll('.pill').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.pill').forEach(function (x) {
          x.classList.remove('active');
        });
        btn.classList.add('active');
        var interest = document.getElementById('trinetixInterest');
        if (interest) {
          interest.value = btn.getAttribute('data-interest') || btn.textContent.trim();
        }
      });
    });
  });
})();
