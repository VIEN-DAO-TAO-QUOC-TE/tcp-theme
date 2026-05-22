(function () {
  var slider = document.querySelector('.home-courses__grid');
  if (!slider) return;

  var swiperInstance = null;
  var mobileMaxWidth = 849;

  var setupSwiper = function () {
    var isMobile = window.matchMedia('(max-width: ' + mobileMaxWidth + 'px)').matches;

    if (!isMobile) {
      if (swiperInstance) {
        swiperInstance.destroy(true, true);
        swiperInstance = null;
      }
      slider.classList.remove('is-swiper-active');
      slider.classList.remove('swiper-wrapper');
      if (slider.parentElement) slider.parentElement.classList.remove('swiper');
      return;
    }

    // Đảm bảo các item là swiper-slide
    slider.querySelectorAll('.col, .product-small, .product').forEach(function (el) {
      el.classList.add('swiper-slide');
        // Xóa các class chia cột của Flatsome để Swiper tự xử lý
        el.classList.remove('large-columns-4', 'medium-columns-3', 'small-columns-1', 'row', 'row-small');
        el.style.removeProperty('width');
        el.style.removeProperty('float');
        el.style.removeProperty('max-width');
        el.style.removeProperty('min-width');
        el.style.removeProperty('flex');
      });
      slider.classList.add('swiper-wrapper');
      if (slider.parentElement) slider.parentElement.classList.add('swiper');

      slider.classList.add('is-swiper-active');
    if (swiperInstance || typeof window.Swiper === 'undefined') return;

      swiperInstance = new window.Swiper(slider.parentElement, {
        slidesPerView: 1.1,
        slidesPerGroup: 1,
        spaceBetween: 20,
        speed: 450,
        watchOverflow: true,
        freeMode: false,
        breakpoints: {
          850: {
            slidesPerView: 4,
            slidesPerGroup: 4,
            spaceBetween: 30,
          }
        }
      });
  };

  var ensureSwiperAssets = function (cb) {
    if (typeof window.Swiper !== 'undefined') {
      cb();
      return;
    }
    if (!document.querySelector('link[data-tcp-swiper-css]')) {
      var cssLink = document.createElement('link');
      cssLink.rel = 'stylesheet';
      cssLink.href = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
      cssLink.setAttribute('data-tcp-swiper-css', '1');
      document.head.appendChild(cssLink);
    }
    var existingScript = document.querySelector('script[data-tcp-swiper-js]');
    if (existingScript) {
      existingScript.addEventListener('load', cb, { once: true });
      return;
    }
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';
    script.async = true;
    script.setAttribute('data-tcp-swiper-js', '1');
    script.addEventListener('load', cb, { once: true });
    document.head.appendChild(script);
  };

  ensureSwiperAssets(function () {
    setupSwiper();
    window.addEventListener('resize', setupSwiper);
  });
})();
