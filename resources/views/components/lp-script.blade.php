{{--
  All landing-page behaviour, in one plain <script>. No Alpine, no bundle: this
  has to be interactive before anything else parses, and it is the only
  JavaScript the page ships.

  tools/build-proto.mjs inlines this same file into the review prototype, so the
  behaviour you test there is the behaviour that ships.
--}}
<script>
(function () {
  'use strict';

  /* ── Header menu ──────────────────────────────────────────────────── */
  var burger = document.getElementById('lp-burger');
  var menu = document.getElementById('lp-menu');
  if (burger && menu) {
    var setMenu = function (open) {
      burger.setAttribute('aria-expanded', String(open));
      menu.setAttribute('data-open', String(open));
    };
    burger.addEventListener('click', function () {
      setMenu(burger.getAttribute('aria-expanded') !== 'true');
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && burger.getAttribute('aria-expanded') === 'true') {
        setMenu(false);
        burger.focus();
      }
    });
  }

  /* ── Platform carousel ────────────────────────────────────────────── */
  var rail = document.getElementById('lp-rail');
  if (rail) {
    var railBtns = [].slice.call(document.querySelectorAll('[data-rail]'));
    var step = function () {
      var card = rail.querySelector('.lp-card');
      return card ? card.getBoundingClientRect().width + 16 : rail.clientWidth * 0.8;
    };
    var syncRail = function () {
      var max = rail.scrollWidth - rail.clientWidth - 2;
      railBtns.forEach(function (b) {
        b.disabled = b.dataset.rail === 'prev' ? rail.scrollLeft <= 2 : rail.scrollLeft >= max;
      });
    };
    railBtns.forEach(function (b) {
      b.addEventListener('click', function () {
        rail.scrollBy({ left: b.dataset.rail === 'next' ? step() : -step(), behavior: 'smooth' });
      });
    });
    rail.addEventListener('scroll', syncRail, { passive: true });
    window.addEventListener('resize', syncRail);
    syncRail();
  }

  /* ── Get-started dialog ───────────────────────────────────────────────
     Every CTA opens this rather than jumping into WhatsApp. The package the
     visitor clicked from is pre-selected, and a website address typed into the
     closing field is carried across so nothing has to be retyped. */
  var modal = document.getElementById('lp-quote');
  var form = document.getElementById('lp-quote-form');

  var openQuote = function (opts) {
    if (!modal) return;
    opts = opts || {};

    if (opts.package) {
      var sel = form && form.querySelector('[name="package"]');
      if (sel) {
        // Only take the value if the dialog actually offers it.
        for (var i = 0; i < sel.options.length; i++) {
          if (sel.options[i].value === opts.package) { sel.value = opts.package; break; }
        }
      }
    }
    if (opts.website) {
      var site = form && form.querySelector('[name="website"]');
      if (site && !site.value) site.value = opts.website;
    }

    if (typeof modal.showModal === 'function') {
      if (!modal.open) modal.showModal();
    } else {
      // No <dialog> support: fall back to the section anchor rather than
      // trapping the visitor with a dead button.
      modal.setAttribute('open', 'open');
    }

    var first = form && form.querySelector('input, select, textarea');
    if (first) {
      // Wait for the dialog to paint, or focus lands before it is visible.
      requestAnimationFrame(function () { first.focus({ preventScroll: true }); });
    }
  };

  document.addEventListener('click', function (e) {
    var open = e.target.closest && e.target.closest('[data-quote]');
    if (open) {
      e.preventDefault();
      openQuote({ package: open.dataset.package });
      return;
    }
    var close = e.target.closest && e.target.closest('[data-quote-close]');
    if (close && modal) {
      e.preventDefault();
      if (typeof modal.close === 'function') modal.close(); else modal.removeAttribute('open');
    }
  });

  // Click the backdrop to dismiss: the card stops the event, the dialog itself
  // only receives it when the click landed outside.
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal && typeof modal.close === 'function') modal.close();
    });
  }

  /* Compose everything already typed into a WhatsApp message. */
  if (form) {
    var waBtn = form.querySelector('[data-quote-wa]');
    if (waBtn) {
      waBtn.addEventListener('click', function () {
        var val = function (n) {
          var el = form.querySelector('[name="' + n + '"]');
          return el && el.value ? el.value.trim() : '';
        };
        var lines = ['Hi Fignoc, I would like a website.'];
        if (val('business')) lines.push('Business: ' + val('business'));
        if (val('name')) lines.push('Name: ' + val('name'));
        if (val('website')) lines.push('Website: ' + val('website'));
        if (val('package')) lines.push('Package: ' + val('package'));
        if (val('budget')) lines.push('Budget: ' + val('budget'));
        if (val('goal')) lines.push('What I need: ' + val('goal'));
        window.open(
          'https://wa.me/' + form.dataset.wa + '?text=' + encodeURIComponent(lines.join('\n')),
          '_blank',
          'noopener'
        );
      });
    }
  }

  /* The Visibility Check form posts on its own now — it collects the name,
     number, business and address the report needs, so there is nothing left for
     the dialog to ask. Left here only to keep the prototype (which has no
     endpoint) from navigating away. */
  var check = document.getElementById('lp-check');
  if (check && check.getAttribute('action') === '#') {
    check.addEventListener('submit', function (e) {
      e.preventDefault();
      var val = function (n) {
        var el = check.querySelector('[name="' + n + '"]');
        return el && el.value ? el.value.trim() : '';
      };
      if (!check.reportValidity()) return;
      openQuote({ website: val('website') });
    });
  }

  /* Re-open the dialog on the confirmation state after a successful post. */
  if (modal && modal.dataset.autoOpen === 'true') openQuote({});
})();
</script>
