// Swiper slider for home courses section (mobile only)
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    if (window.innerWidth > 767 || typeof Swiper === 'undefined') return;

    var grid = document.querySelector('.home-courses__slider');
    if (!grid) return;

    // Xóa toàn bộ class Flatsome layout để tránh flex-basis/max-width interference
    var removeClasses = [
      'row', 'row-small', 'large-columns-4', 'medium-columns-3', 'small-columns-1',
      'equalize-box', 'has-equal-box-heights', 'c-product-grid'
    ];
    removeClasses.forEach(function (c) { grid.classList.remove(c); });

    // Chỉ lấy direct children .col làm slides
    var slides = Array.prototype.slice.call(grid.children);
    slides.forEach(function (el) {
      if (!el.classList.contains('col')) return;
      el.classList.add('swiper-slide');
      // Xóa inline styles và class gây nhiễu từ Flatsome JS
      el.style.cssText = '';
    });

    // Bọc trong .swiper container
    if (!grid.parentElement.classList.contains('swiper')) {
      var wrap = document.createElement('div');
      wrap.className = 'swiper tcp-courses-swiper';
      grid.parentNode.insertBefore(wrap, grid);
      wrap.appendChild(grid);
    }

    grid.classList.add('swiper-wrapper');

    new Swiper(grid.parentElement, {
      slidesPerView: 1.2,
      slidesPerGroup: 1,
      spaceBetween: 12,
      grabCursor: true,
      loop: false,
    });
  });
})();
