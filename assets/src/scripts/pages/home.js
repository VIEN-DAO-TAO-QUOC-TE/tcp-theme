// ── Trainers slider: ensure 2 cards per slide on all viewports ─────────────
(function ($) {
  // Add config class before flickity-custom.js initializes on window.load
  $(function () {
    var $grid = $(".home-trainers__grid");
    if ($grid.length && !$grid.hasClass("js-slider-nav_2-2-1")) {
      $grid.addClass("js-slider-nav_2-2-1");
    }
  });

  // Fallback: if flickity-custom.js already ran with wrong groupCells, re-init
  $(window).on("load.trainers", function () {
    var $grid = $(".home-trainers__grid");
    if (!$grid.length) return;

    var flkty =
      $grid.data("flickity") ||
      (typeof Flickity !== "undefined" && Flickity.data && Flickity.data($grid[0]));

    if (!flkty || !flkty.slides || flkty.slides.length > 1) return;

    // Only 1 slide group — destroy and re-init with groupCells=2
    $grid.closest(".slider-wrap").find(".slider-nav").remove();
    $grid.removeData("customNavInited flickity lazyFlickity flickityInstance");

    try {
      flkty.destroy();
    } catch (e) {}

    if (typeof Flickity === "undefined") return;

    var newFlkty = new Flickity($grid[0], {
      cellAlign: "left",
      contain: true,
      groupCells: 2,
      wrapAround: false,
      prevNextButtons: false,
      pageDots: false,
      autoPlay: true,
      percentPosition: true,
      lazyLoad: 1,
    });
    $grid.data("flickity", newFlkty);

    // Re-trigger nav build via flickity-custom.js handler
    $(document).trigger("flatsome-flickity-ready");
  });

})(jQuery);

// ── Corporate training mosaic ─────────────────────────────────────────────
jQuery(function ($) {
  var BREAKPOINT = 549;

  var $section = $(".home-corporate-training");
  if (!$section.length) return;

  // Tạo wrapper mobile (chỉ tạo 1 lần)
  var mobileWrapClass = "home-corporate-training__mosaic-mobile";
  var $mobileWrap = $section.find("." + mobileWrapClass);

  if (!$mobileWrap.length) {
    // Đặt wrapper ở đầu cột center (trên tiêu đề/nội dung) để giống ảnh 2
    // Bạn có thể đổi vị trí prependTo('.section-content') nếu muốn.
    $mobileWrap = $("<div/>", { class: mobileWrapClass }).prependTo(
      $section.find(".c-mosaic--center .col-inner").first(),
    );
  }

  // Inject CSS tối thiểu để ra dạng grid như ảnh 2 (bạn có thể chuyển sang file CSS riêng)
  if (!$("#tcp-mobile-mosaic-style").length) {
    $(
      '<style id="tcp-mobile-mosaic-style">\
      @media (max-width: 548px){\
        .home-corporate-training__mosaic-mobile .img{\
          margin:0 !important;\
        }\
        .home-corporate-training__mosaic-mobile img{\
          width:100%;\
          height:100%;\
          object-fit:cover;\
          border-radius:8px;\
          display:block;\
        }\
        /* Ẩn 2 cột mosaic trái/phải trên mobile để tránh trống/rối */\
        .home-corporate-training .c-mosaic--left,\
        .home-corporate-training .c-mosaic--right{\
          display:none !important;\
        }\
      }\
    </style>',
    ).appendTo("head");
  }

  // Lưu vị trí gốc để restore
  function cacheOriginalPosition($el) {
    if ($el.data("tcpCached")) return;

    var $parent = $el.parent();
    var $next = $el.next(); // có thể rỗng

    $el.data("tcpCached", true);
    $el.data("tcpParent", $parent);
    $el.data("tcpNext", $next.length ? $next : null);
  }

  function moveToMobile() {
    // Lấy toàn bộ .img trong cả 3 cột mosaic (kể cả trong cột center)
    var $imgs = $section.find(".c-mosaic .img");

    // Lưu vị trí gốc rồi detach để chuyển DOM thật (giữ lightbox event tốt hơn clone)
    $imgs.each(function () {
      cacheOriginalPosition($(this));
    });

    // Dọn wrapper rồi append theo thứ tự DOM hiện tại
    $mobileWrap.empty();
    $imgs.detach().appendTo($mobileWrap);
  }

  function restoreDesktop() {
    // Lấy các ảnh đang nằm trong wrapper mobile
    var $imgs = $mobileWrap.find(".img");
    if (!$imgs.length) return;

    $imgs.each(function () {
      var $img = $(this);
      var $parent = $img.data("tcpParent");
      var $next = $img.data("tcpNext");

      // Trả về đúng chỗ cũ
      if ($parent && $parent.length) {
        if ($next && $next.length) {
          $img.insertBefore($next);
        } else {
          $img.appendTo($parent);
        }
      }
    });

    $mobileWrap.empty();
  }

  function handle() {
    var w = window.innerWidth || $(window).width();
    if (w < BREAKPOINT) {
      moveToMobile();
    } else {
      restoreDesktop();
      // hiện lại 2 cột mosaic trái/phải nếu bị style khác can thiệp
      // (CSS media query ở trên tự lo phần này)
    }
  }

  // Run + debounce resize
  var t = null;
  handle();
  $(window).on("resize orientationchange", function () {
    clearTimeout(t);
    t = setTimeout(handle, 120);
  });
});
