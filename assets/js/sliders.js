/**
 * Trinetix — industries + testimonials sliders
 * Data from window.trinetixData (with sensible fallbacks).
 * Keyboard support · null-safe · no jQuery.
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

  function getData() {
    return window.trinetixData || {};
  }

  var FALLBACK_INDUSTRIES = [
    {
      title: 'Healthcare',
      text: 'Modernize care platforms, operations and data experiences with secure technology designed around patients, practitioners and performance.',
      img: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=2200&q=92'
    },
    {
      title: 'Life Sciences',
      text: 'Accelerate research, clinical operations and connected data with scalable platforms, analytics and intelligent automation.',
      img: 'https://images.unsplash.com/photo-1576086213369-97a306d36557?auto=format&fit=crop&w=2200&q=92'
    },
    {
      title: 'Financial Services',
      text: 'Create resilient digital experiences, automate complex workflows and use trusted data to support faster business decisions.',
      img: 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=2200&q=92'
    },
    {
      title: 'High Tech',
      text: 'Accelerate product delivery and platform modernization while improving reliability, scalability and time to market.',
      img: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=2200&q=92'
    },
    {
      title: 'Consumer',
      text: 'Connect digital commerce, customer data and experience design to create more consistent and relevant journeys.',
      img: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=2200&q=92'
    },
    {
      title: 'Manufacturing',
      text: 'Modernize operations, connect systems and improve visibility across production, supply chains and enterprise workflows.',
      img: 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=2200&q=92'
    }
  ];

  var FALLBACK_TESTIMONIALS = [
    {
      img: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=800&q=92',
      quote:
        'Trinetix combines strategic thinking with practical delivery. The team keeps the work focused, transparent and aligned to the business outcome we are trying to achieve.',
      name: 'Enterprise Technology Leader',
      role: 'Placeholder testimonial — replace with approved client quote'
    },
    {
      img: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=92',
      quote:
        'The engagement was structured and collaborative from the beginning. We could make decisions quickly because priorities and progress were always clear.',
      name: 'Operations Executive',
      role: 'Placeholder testimonial — replace with approved client quote'
    },
    {
      img: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=92',
      quote:
        'The team helped simplify a complex technology problem and move from planning into delivery without losing sight of the business objective.',
      name: 'Digital Product Leader',
      role: 'Placeholder testimonial — replace with approved client quote'
    },
    {
      img: 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=800&q=92',
      quote:
        'What stood out was the balance between strategic thinking and hands-on execution. Recommendations were practical and built around our environment.',
      name: 'Strategy Leader',
      role: 'Placeholder testimonial — replace with approved client quote'
    }
  ];

  function normalizeIndustry(item) {
    if (!item || typeof item !== 'object') return null;
    return {
      title: item.title || '',
      text: item.text || '',
      img: item.img || item.image || '',
      url: item.url || ''
    };
  }

  function normalizeTestimonial(item) {
    if (!item || typeof item !== 'object') return null;
    return {
      img: item.img || item.image || '',
      quote: item.quote || '',
      name: item.name || '',
      role: item.role || '',
      chip: item.chip || item.name || '',
      rating: item.rating || 5
    };
  }

  onReady(function () {
    var data = getData();
    var industriesRaw =
      (Array.isArray(window.trinetixIndustries) && window.trinetixIndustries.length
        ? window.trinetixIndustries
        : null) ||
      (Array.isArray(data.industries) && data.industries.length ? data.industries : null) ||
      FALLBACK_INDUSTRIES;
    var testimonialsRaw =
      (Array.isArray(window.trinetixTestimonials) && window.trinetixTestimonials.length
        ? window.trinetixTestimonials
        : null) ||
      (Array.isArray(data.testimonials) && data.testimonials.length ? data.testimonials : null) ||
      FALLBACK_TESTIMONIALS;

    initIndustries(
      industriesRaw.map(normalizeIndustry).filter(Boolean)
    );
    initTestimonials(
      testimonialsRaw.map(normalizeTestimonial).filter(Boolean)
    );
  });

  function initIndustries(industries) {
    var iBg = document.getElementById('industryBg');
    var iTitle = document.getElementById('industryTitle');
    var iText = document.getElementById('industryText');
    var prev = document.getElementById('industryPrev');
    var next = document.getElementById('industryNext');
    var tabs = document.querySelectorAll('.industry-tab');
    var stage = document.querySelector('.industries') || document.getElementById('industries');

    if (!industries.length || (!iBg && !iTitle && !iText && !tabs.length)) return;

    var index = 0;
    var timer = null;

    function setIndustry(i) {
      index = ((i % industries.length) + industries.length) % industries.length;
      var d = industries[index];

      if (iBg) {
        iBg.style.opacity = 0.15;
        clearTimeout(timer);
        timer = setTimeout(function () {
          if (d.img) iBg.style.backgroundImage = "url('" + d.img + "')";
          iBg.style.opacity = 1;
        }, 160);
      }

      if (iTitle && d.title) iTitle.textContent = d.title;
      if (iText && d.text) iText.textContent = d.text;
      var iCta = document.getElementById('industryCta');
      if (iCta && d.url) iCta.setAttribute('href', d.url);

      tabs.forEach(function (b, n) {
        var active = n === index;
        b.classList.toggle('active', active);
        b.setAttribute('aria-selected', active ? 'true' : 'false');
        b.setAttribute('tabindex', active ? '0' : '-1');
      });
    }

    setIndustry(0);

    tabs.forEach(function (b) {
      b.setAttribute('role', 'tab');
      b.addEventListener('click', function () {
        var i = b.dataset.i !== undefined ? +b.dataset.i : Array.prototype.indexOf.call(tabs, b);
        setIndustry(i);
      });
      b.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
          e.preventDefault();
          setIndustry(index + 1);
          tabs[index] && tabs[index].focus();
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
          e.preventDefault();
          setIndustry(index - 1);
          tabs[index] && tabs[index].focus();
        } else if (e.key === 'Home') {
          e.preventDefault();
          setIndustry(0);
          tabs[0] && tabs[0].focus();
        } else if (e.key === 'End') {
          e.preventDefault();
          setIndustry(industries.length - 1);
          tabs[index] && tabs[index].focus();
        }
      });
    });

    if (prev) {
      prev.addEventListener('click', function () {
        setIndustry(index - 1);
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        setIndustry(index + 1);
      });
    }

    if (stage) {
      stage.addEventListener('keydown', function (e) {
        if (e.target.closest && e.target.closest('.industry-tab')) return;
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          setIndustry(index - 1);
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          setIndustry(index + 1);
        }
      });
    }
  }

  function initTestimonials(testimonials) {
    var main = document.getElementById('testimonialMain');
    var tImg = document.getElementById('tImg');
    var tQuote = document.getElementById('tQuote');
    var tName = document.getElementById('tName');
    var tRole = document.getElementById('tRole');
    var prev = document.getElementById('testimonialPrev');
    var next = document.getElementById('testimonialNext');
    var chips = document.querySelectorAll('.person-chip');
    var stage = document.querySelector('.testimonials') || document.getElementById('testimonials');

    if (!testimonials.length || (!tImg && !tQuote && !chips.length)) return;

    var index = 0;
    var timer = null;

    function setTestimonial(i) {
      index = ((i % testimonials.length) + testimonials.length) % testimonials.length;
      var d = testimonials[index];

      if (main) main.classList.add('is-changing');

      clearTimeout(timer);
      timer = setTimeout(function () {
        if (tImg && d.img) {
          tImg.src = d.img;
          if (d.name) tImg.alt = d.name;
        }
        if (tQuote && d.quote) tQuote.textContent = d.quote;
        if (tName && d.name) tName.textContent = d.name;
        if (tRole && d.role) tRole.textContent = d.role;

        chips.forEach(function (x, n) {
          var active = n === index;
          x.classList.toggle('active', active);
          x.setAttribute('aria-selected', active ? 'true' : 'false');
          x.setAttribute('tabindex', active ? '0' : '-1');
        });

        if (main) main.classList.remove('is-changing');
      }, 180);
    }

    setTestimonial(0);

    chips.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var i = btn.dataset.t !== undefined ? +btn.dataset.t : Array.prototype.indexOf.call(chips, btn);
        setTestimonial(i);
      });
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
          e.preventDefault();
          setTestimonial(index + 1);
          chips[index] && chips[index].focus();
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
          e.preventDefault();
          setTestimonial(index - 1);
          chips[index] && chips[index].focus();
        }
      });
    });

    if (prev) {
      prev.addEventListener('click', function () {
        setTestimonial(index - 1);
      });
    }
    if (next) {
      next.addEventListener('click', function () {
        setTestimonial(index + 1);
      });
    }

    if (stage) {
      stage.addEventListener('keydown', function (e) {
        if (e.target.closest && (e.target.closest('.person-chip') || e.target.closest('.industry-tab'))) return;
        if (e.key === 'ArrowLeft') {
          e.preventDefault();
          setTestimonial(index - 1);
        } else if (e.key === 'ArrowRight') {
          e.preventDefault();
          setTestimonial(index + 1);
        }
      });
    }
  }
})();
