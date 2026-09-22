/**
 * Trinetix — contact form AJAX
 * Expects window.trinetixContact = { ajaxUrl, nonce, action?, i18n?, successMessage?, errorMessage? }
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

  function i18n(key, fallback) {
    var c = cfg();
    if (c.i18n && c.i18n[key]) return c.i18n[key];
    if (key === 'success' && c.successMessage) return c.successMessage;
    if (key === 'error' && c.errorMessage) return c.errorMessage;
    if (key === 'pending' && c.pendingMessage) return c.pendingMessage;
    return fallback;
  }

  function findForm() {
    return (
      document.getElementById('trinetixContactForm') ||
      document.getElementById('trinetix-contact-form') ||
      document.querySelector('form.trinetix-contact-form') ||
      document.querySelector('form.form') ||
      document.querySelector('.contact form') ||
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
    if (message) {
      el.hidden = false;
      el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
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
    if (!form || form.tagName !== 'FORM') return;

    form.removeAttribute('onsubmit');

    var status = ensureStatus(form);
    var submitBtn = form.querySelector('[type="submit"]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var c = cfg();
      if (!c.ajaxUrl) {
        setStatus(status, i18n('error', 'Contact endpoint is not configured.'), 'error');
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
      setStatus(status, i18n('pending', 'Sending…'), 'pending');

      fetch(c.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: serializeForm(form),
        headers: {
          Accept: 'application/json'
        }
      })
        .then(function (res) {
          return res.json().then(
            function (json) {
              return { ok: res.ok, payload: json };
            },
            function () {
              return { ok: res.ok, payload: null };
            }
          );
        })
        .then(function (result) {
          var payload = result.payload;
          var ok = payload && (payload.success === true || payload.success === 'true');

          if (ok) {
            setStatus(
              status,
              (payload.data && payload.data.message) ||
                i18n('success', 'Thank you. Your message has been sent.'),
              'success'
            );
            form.reset();

            // Restore default interest from first active pill (or first pill).
            var interest = document.getElementById('trinetixInterest');
            if (interest) {
              var active =
                document.querySelector('.pill.active') || document.querySelector('.pill');
              interest.value = active
                ? active.getAttribute('data-interest') || active.textContent.trim()
                : '';
            }
          } else {
            var msg =
              (payload && payload.data && payload.data.message) ||
              (payload && payload.message) ||
              i18n('error', 'Something went wrong. Please try again.');
            setStatus(status, msg, 'error');
          }
        })
        .catch(function () {
          setStatus(
            status,
            i18n('error', 'Unable to send right now. Please try again later.'),
            'error'
          );
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
