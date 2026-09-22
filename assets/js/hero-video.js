/**
 * Trinetix — single hero video autoplay implementation
 * Muted / loop / playsInline · playbackRate · user-gesture fallback
 * Desktop / mobile source switch · preload metadata
 * Consolidates previous duplicate scripts 1–3.
 */
(function () {
  'use strict';

  var DEFAULT_RATE = 1;
  var MOBILE_MQ = '(max-width: 980px)';

  function onReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  function startHeroVideo() {
    var video = document.querySelector('.hero-video');
    if (!video) return;

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) {
      try {
        video.pause();
      } catch (e) {}
      video.removeAttribute('autoplay');
      return;
    }

    video.autoplay = true;
    video.loop = true;
    video.muted = true;
    video.defaultMuted = true;
    video.playsInline = true;
    video.setAttribute('muted', '');
    video.setAttribute('autoplay', '');
    video.setAttribute('loop', '');
    video.setAttribute('playsinline', '');
    video.setAttribute('webkit-playsinline', '');
    video.preload = 'metadata';
    video.setAttribute('preload', 'metadata');

    var rate = DEFAULT_RATE;
    var chip = document.getElementById('heroSpeedChip');
    var label = document.getElementById('heroSpeedLabel');
    var speeds = [0.9, 1, 1.25];
    var speedIdx = 1;

    if (video.dataset.playbackRate) {
      var parsed = parseFloat(video.dataset.playbackRate);
      if (!isNaN(parsed) && parsed > 0) {
        rate = parsed;
      }
    }

    function applyRate(value) {
      try {
        video.playbackRate = value;
      } catch (e) {}
      if (label) {
        label.textContent = Number(value).toFixed(2).replace(/0+$/, '').replace(/\.$/, '') + 'x';
      }
    }

    applyRate(rate);

    /* Desktop / mobile source switch via data-src-desktop / data-src-mobile */
    function pickSource() {
      var desktop = video.getAttribute('data-src-desktop');
      var mobile = video.getAttribute('data-src-mobile');
      var themeSrc = video.getAttribute('data-src') || '';
      var isMobile = window.matchMedia(MOBILE_MQ).matches;
      var next = isMobile ? mobile || desktop || themeSrc : desktop || mobile || themeSrc;

      if (!next) {
        var sourceEl = video.querySelector('source');
        if (sourceEl && sourceEl.getAttribute('src')) {
          return;
        }
        return;
      }

      var current = '';
      var sourceEl = video.querySelector('source');
      if (sourceEl) {
        current = sourceEl.getAttribute('src') || '';
      } else {
        current = video.getAttribute('src') || '';
      }

      if (current === next) return;

      if (sourceEl) {
        sourceEl.setAttribute('src', next);
      } else {
        video.setAttribute('src', next);
      }
      video.load();
    }

    pickSource();

    function playNow() {
      try {
        video.muted = true;
        applyRate(rate);
        var promise = video.play();
        if (promise && typeof promise.catch === 'function') {
          promise.catch(function () {});
        }
      } catch (e) {}
    }

    video.addEventListener('play', function () {
      applyRate(rate);
    });

    ['loadedmetadata', 'loadeddata', 'canplay'].forEach(function (evt) {
      video.addEventListener(evt, playNow);
    });

    video.addEventListener('pause', function () {
      if (!document.hidden) {
        setTimeout(playNow, 60);
      }
    });

    document.addEventListener('visibilitychange', function () {
      if (!document.hidden) playNow();
    });

    /* User-gesture fallback when autoplay is blocked */
    ['pointerdown', 'touchstart', 'keydown', 'scroll'].forEach(function (evt) {
      window.addEventListener(evt, playNow, { once: true, passive: true });
    });

    if (chip) {
      chip.addEventListener('click', function () {
        speedIdx = (speedIdx + 1) % speeds.length;
        rate = speeds[speedIdx];
        applyRate(rate);
      });
    }

    var resizeTimer;
    window.addEventListener(
      'resize',
      function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          pickSource();
          playNow();
        }, 200);
      },
      { passive: true }
    );

    playNow();
    setTimeout(playNow, 250);
    setTimeout(playNow, 1000);
  }

  onReady(startHeroVideo);
  window.addEventListener('load', startHeroVideo);
})();
