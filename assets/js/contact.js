/**
 * Trinetix — contact form AJAX
 * Expects window.trinetixContact = { ajaxUrl, nonce, action?, successMessage?, errorMessage? }
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

  function cfg() {
    return window.trinetixContact || {};
  }

  function findForm() {
    return (
      document.getElementById('trinetixContactForm') ||
      document.getElementById('trinetix-contact-form') ||
      document.querySelector('form.trinetix-contact-form') ||
      document.querySelector('.contact form') ||
      document.querySelector('section.contact form') ||
      document.querySelector('#contact form')
    );
  }

  function ensureStatus(form) {
    var el = form.querySelector('.form-status') || document.getElementById('contactFormStatus');
    if (!el) {
      el = document.createElement('div');
      el.className = 'form-status';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      form.appendChild(el);
    }
    return el;
  }

  function setStatus(el, message, type) {
    if (!el) return;
    el.textContent = message || '';
    el.classList.remove('is-success', 'is-error', 'is-pending');
    if (type) el.classList.add('is-' + type);
  }

  function serializeForm(form) {
    var fd = new FormData(form);
    var c = cfg();
    if (c.nonce && !fd.has('_ajax_nonce') && !fd.has('nonce')) {
      fd.append('nonce', c.nonce);
    }
    if (c.action) {
      fd.set('action', c.action);
    } else if (!fd.has('action')) {
      fd.append('action', 'trinetix_contact');
    }
    return fd;
  }

  onReady(function () {
    var form = findForm();
    if (!form) return;

    // Demo inline handlers from static HTML should not fire
    form.removeAttribute('onsubmit');

    var status = ensureStatus(form);
    var submitBtn = form.querySelector('[type="submit"]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var c = cfg();
      if (!c.ajaxUrl) {
        setStatus(status, c.errorMessage || 'Contact endpoint is not configured.', 'error');
        return;
      }

      if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
        form.reportValidity();
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-busy', 'true');
      }
      setStatus(status, c.pendingMessage || 'Sending…', 'pending');

      fetch(c.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: serializeForm(form)
      })
        .then(function (res) {
          return res.json().catch(function () {
            return { success: res.ok };
          });
        })
        .then(function (payload) {
          var ok = payload && (payload.success === true || payload.success === 'true');
          if (ok) {
            setStatus(
              status,
              (payload.data && payload.data.message) || c.successMessage || 'Thank you — we will be in touch shortly.',
              'success'
            );
            form.reset();
          } else {
            var msg =
              (payload && payload.data && payload.data.message) ||
              (payload && payload.message) ||
              c.errorMessage ||
              'Something went wrong. Please try again.';
            setStatus(status, msg, 'error');
          }
        })
        .catch(function () {
          setStatus(status, c.errorMessage || 'Unable to send right now. Please try again later.', 'error');
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.removeAttribute('aria-busy');
          }
        });
    });
  });
})();
