// header, menu mobile, toast, lightbox galeri, konfirmasi hapus
(function () {
  var header = document.getElementById('siteHeader');
  if (header) {
    var onScroll = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    });
  }

  document.querySelectorAll('.toast').forEach(function (t) {
    setTimeout(function () {
      t.classList.add('is-leaving');
      setTimeout(function () { t.remove(); }, 450);
    }, 5000);
  });

  var box = document.getElementById('lightbox');
  if (box && typeof box.showModal === 'function') {
    var boxImg = box.querySelector('img');
    document.querySelectorAll('.gallery-item').forEach(function (btn) {
      btn.addEventListener('click', function () {
        boxImg.src = btn.getAttribute('data-full');
        boxImg.alt = btn.getAttribute('data-alt') || '';
        box.showModal();
      });
    });
    box.addEventListener('click', function (e) {
      if (e.target === box) { box.close(); }
    });
  }

  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) { e.preventDefault(); }
    });
  });
})();
