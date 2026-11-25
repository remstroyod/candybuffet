/**
 * Телефон з кодом країни (lib/phone.php → CB_Phone::field()).
 *
 * Україна: маска «99 999 99 99» (jQuery Inputmask теми). Інші країни: без маски.
 * Повний номер («+48 601…», «0044…», «380…»), вставлений чи відновлений у поле, сам обирає країну.
 *
 * API: window.CBPhone.value( 'billing[phone]' ) → {full: '48601234567', valid: true, ua: false}
 *      window.CBPhone.normalize( cc, raw ) — те саме, що CB_Phone::normalize() у PHP.
 */
(function () {
  'use strict';

  var UA = '380';
  var doc = document;

  function digits(v) { return String(v || '').replace(/\D+/g, ''); }

  function isValid(full) {
    return full.indexOf(UA) === 0 ? /^380\d{9}$/.test(full) : /^[1-9]\d{7,14}$/.test(full);
  }

  // mirror of CB_Phone::normalize()
  function normalize(cc, raw) {
    raw = String(raw || '').trim();
    var d = digits(raw);
    var full;
    cc = cc === 'other' ? 'other' : (digits(cc) || UA);
    if (raw && (raw.charAt(0) === '+' || d.indexOf('00') === 0)) {
      full = raw.charAt(0) !== '+' && d.indexOf('00') === 0 ? d.slice(2) : d;
    } else if (cc === 'other') {
      full = d;
    } else {
      if (cc === UA) {
        if (d.length === 12 && d.indexOf(UA) === 0) { d = d.slice(3); } else if (d.length === 11 && d.indexOf('80') === 0) { d = d.slice(2); }
      }
      if (cc !== '39' && d.length > 1 && d.charAt(0) === '0') { d = d.slice(1); }
      full = cc + d;
    }
    return {full: full, ua: full.indexOf(UA) === 0, valid: isValid(full)};
  }

  function parts(box) {
    return {
      sel: box.querySelector('[data-cb-phone-cc]'),
      input: box.querySelector('[data-cb-phone-input]'),
      view: box.querySelector('[data-cb-phone-view]')
    };
  }

  function unmask(input) {
    if (input.inputmask && window.jQuery) { window.jQuery(input).inputmask('remove'); }
  }

  function setMask(box) {
    var p = parts(box);
    var cc = p.sel.value;
    var opt = p.sel.options[p.sel.selectedIndex];
    if (p.view) { p.view.textContent = (opt && opt.getAttribute('data-flag') ? opt.getAttribute('data-flag') + ' ' : '') + (cc === 'other' ? '+…' : '+' + cc); }
    p.input.placeholder = cc === UA ? '67 123 45 67' : (cc === 'other' ? '+код і номер, напр. +48 601 234 567' : 'номер без коду країни');
    p.input.setAttribute('autocomplete', cc === 'other' ? 'tel' : 'tel-national');
    var $ = window.jQuery;
    if ($ && $.fn && $.fn.inputmask) {
      if (cc === UA) {
        $(p.input).inputmask({mask: '99 999 99 99', clearIncomplete: false, showMaskOnHover: false, jitMasking: true});
      } else {
        unmask(p.input);
      }
    }
  }

  // a full number («+48 601…», «00 44…», «380…») selects the country itself
  function takeFullNumber(box, text) {
    var p = parts(box);
    var raw = String(text || '').trim();
    var d = digits(raw);
    var intl = raw.charAt(0) === '+' || d.indexOf('00') === 0 || (d.length === 12 && d.indexOf(UA) === 0);
    if (!intl || d.length < 8) { return false; }
    if (raw.charAt(0) !== '+' && d.indexOf('00') === 0) { d = d.slice(2); }
    var best = '';
    Array.prototype.forEach.call(p.sel.options, function (o) {
      if (/^\d+$/.test(o.value) && d.indexOf(o.value) === 0 && o.value.length > best.length) { best = o.value; }
    });
    unmask(p.input);
    if (best) {
      p.sel.value = best;
      p.input.value = d.slice(best.length);
    } else {
      p.sel.value = 'other';
      p.input.value = '+' + d;
    }
    setMask(box);
    box.classList.add('has-value');
    p.input.dispatchEvent(new Event('input', {bubbles: true}));
    return true;
  }

  function initBox(box) {
    if (box.getAttribute('data-cb-phone-ready')) { return; }
    box.setAttribute('data-cb-phone-ready', '1');
    var p = parts(box);
    if (!p.sel || !p.input) { return; }
    // number saved earlier as «+380 67 …» (form restore, autofill)
    if (!takeFullNumber(box, p.input.value)) { setMask(box); }

    p.sel.addEventListener('change', function () {
      unmask(p.input);
      setMask(box);
      p.input.focus();
    });
    // capture phase: before the input mask sees the pasted text
    p.input.addEventListener('paste', function (e) {
      var cd = e.clipboardData || window.clipboardData;
      if (cd && takeFullNumber(box, cd.getData('text'))) { e.preventDefault(); }
    }, true);
    // «+» in the masked Ukrainian field → free input for another country
    p.input.addEventListener('keydown', function (e) {
      if (e.key === '+' && p.sel.value === UA) {
        e.preventDefault();
        p.sel.value = 'other';
        unmask(p.input);
        p.input.value = '+';
        setMask(box);
      }
    });
    p.input.addEventListener('change', function () {
      if (p.sel.value === 'other' || p.input.value.trim().charAt(0) === '+') { takeFullNumber(box, p.input.value); }
    });
  }

  function boxFor(name) {
    var input = doc.querySelector('[data-cb-phone-input][name="' + name + '"]');
    return input ? input.closest('[data-cb-phone]') : null;
  }

  function value(name) {
    var box = boxFor(name || 'billing[phone]');
    if (!box) { return null; }
    var p = parts(box);
    return normalize(p.sel.value, p.input.value);
  }

  function init() { Array.prototype.forEach.call(doc.querySelectorAll('[data-cb-phone]'), initBox); }

  window.CBPhone = {normalize: normalize, value: value, init: init};

  if (doc.readyState === 'loading') {
    doc.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
