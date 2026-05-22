(function ($) {
  // =========================
  // Config
  // =========================
  const BREAKPOINT_MOBILE = 549;
  const BREAKPOINT_TABLET = 849;

  // Default items per view: Desktop - Tablet - Mobile
  const DEFAULT_ITEMS = [4, 2, 1];

  // =========================
  // Helpers
  // =========================
  function getFlickityInstance($slider) {
    const $el = $slider.eq(0);
    return (
      $el.data("flickity") ||
      $el.data("lazyFlickity") ||
      $el.data("flickityInstance")
    );
  }

  // Ensure each slider has its own wrap (avoid sharing nav between sliders)
  function ensureUniqueWrap($slider) {
    const $el = $slider.eq(0);
    let $wrap = $el.closest(".slider-wrap");

    if (!$wrap.length) {
      $el.wrap('<div class="slider-wrap"></div>');
      return $el.closest(".slider-wrap");
    }

    const $slidersInWrap = $wrap.find(".js-slider-nav");
    if ($slidersInWrap.length > 1) {
      $el.wrap('<div class="slider-wrap"></div>');
      return $el.closest(".slider-wrap");
    }

    return $wrap;
  }

  // Read items config from class:
  // - js-slider-nav_4-2-1
  // - js-slider-nav_3-2-1
  // Fallback: DEFAULT_ITEMS
  function parseItemsConfig($el) {
    // Cache once per slider for performance
    const cached = $el.data("sliderItemsConfig");
    if (cached && cached.length === 3) return cached;

    const classStr = ($el.attr("class") || "").trim();
    const classes = classStr ? classStr.split(/\s+/) : [];

    let result = DEFAULT_ITEMS;

    for (let i = 0; i < classes.length; i++) {
      const cls = classes[i];
      const match = cls.match(/^js-slider-nav_(\d+)-(\d+)-(\d+)$/);
      if (match) {
        result = [
          Math.max(1, parseInt(match[1], 10)), // desktop
          Math.max(1, parseInt(match[2], 10)), // tablet
          Math.max(1, parseInt(match[3], 10)), // mobile
        ];
        break;
      }
    }

    $el.data("sliderItemsConfig", result);
    return result;
  }

  function getGroupCellsByWidth($el) {
    const w = window.innerWidth || document.documentElement.clientWidth;
    const [desktop, tablet, mobile] = parseItemsConfig($el);

    if (w < BREAKPOINT_MOBILE) return mobile;
    if (w < BREAKPOINT_TABLET) return tablet;
    return desktop;
  }

  function debounce(fn, wait) {
    let t = null;
    return function () {
      const ctx = this;
      const args = arguments;
      clearTimeout(t);
      t = setTimeout(function () {
        fn.apply(ctx, args);
      }, wait);
    };
  }

  function buildFlickityOptions($el, groupCells) {
    const isLoop = $el.hasClass("js-slider-loop");

    return {
      cellAlign: "left",
      contain: !isLoop,
      groupCells: groupCells,
      wrapAround: isLoop,
      prevNextButtons: false,
      pageDots: false,
      autoPlay: true,
      percentPosition: true,
      lazyLoad: 1,
    };
  }

  // Use direct Flickity constructor (synchronous) so desktop grids init
  // immediately without waiting for IntersectionObserver (lazyFlickity).
  function flickityInit($el, options) {
    if (typeof Flickity !== "undefined") {
      const flkty = new Flickity($el[0], options);
      $el.data("flickity", flkty);
      return flkty;
    }
    if (typeof $el.lazyFlickity === "function") {
      $el.lazyFlickity(options);
    }
    return getFlickityInstance($el);
  }

  // =========================
  // Custom Nav
  // =========================
  function buildCustomNav($slider) {
    const $el = $slider.eq(0);

    // Prevent double init per slider
    if ($el.data("customNavInited")) return;

    const flkty = getFlickityInstance($el);
    if (!flkty) return;

    const $wrap = ensureUniqueWrap($el);

    // If wrap already has nav, mark inited and stop (avoid duplicates)
    if ($wrap.children(".slider-nav").length) {
      $el.data("customNavInited", true);
      return;
    }

    const $nav = $(
      `
      <div class="slider-nav">
        <button class="slider-nav__btn slider-nav__btn--prev" type="button" aria-label="Previous">
          <div class="icon-chevron-left"></div>
        </button>

        <div class="slider-dots" aria-label="Slider pagination"></div>

        <button class="slider-nav__btn slider-nav__btn--next" type="button" aria-label="Next">
          <div class="icon-chevron-right"></div>
        </button>
      </div>
      `,
    );

    $wrap.append($nav);

    const $dots = $nav.find(".slider-dots");
    const $btnPrev = $nav.find(".slider-nav__btn--prev");
    const $btnNext = $nav.find(".slider-nav__btn--next");

    function buildDots() {
      $dots.empty();
      const slideCount = flkty.slides ? flkty.slides.length : 0;

      if (slideCount <= 1) {
        $nav.hide();
        return;
      } else {
        $nav.show();
      }

      const $first = $(
        `<button class="slider-dots__dot" type="button" aria-label="Go to first slide">1</button>`,
      );
      $first.on("click", function () {
        flkty.select(0);
      });
      $dots.append($first);

      $dots.append('<span class="slider-dots__sep" aria-hidden="true"></span>');

      const $last = $(
        `<button class="slider-dots__dot" type="button" aria-label="Go to last slide">${slideCount}</button>`,
      );
      $last.on("click", function () {
        flkty.select(slideCount - 1);
      });
      $dots.append($last);
    }

    function updateUI() {
      const slideCount = flkty.slides ? flkty.slides.length : 0;
      const idx = flkty.selectedIndex || 0;

      const $allDots = $dots.find(".slider-dots__dot");
      $allDots.removeClass("is-active");
      if (idx === 0) {
        $allDots.eq(0).addClass("is-active");
      } else if (idx === slideCount - 1) {
        $allDots.eq(1).addClass("is-active");
      }

      $btnPrev.prop("disabled", idx <= 0);
      $btnNext.prop("disabled", idx >= slideCount - 1);
    }

    // Bind click once for this nav
    $btnPrev.off("click.customNav").on("click.customNav", function () {
      flkty.previous();
    });
    $btnNext.off("click.customNav").on("click.customNav", function () {
      flkty.next();
    });

    buildDots();
    updateUI();

    // Keep UI synced
    flkty.on("select", updateUI);
    flkty.on("resize", function () {
      buildDots();
      updateUI();
    });

    $el.data("customNavInited", true);
  }

  // =========================
  // Flickity init / re-init
  // =========================
  function initOrUpdateSlider($slider) {
    const $el = $slider.eq(0);

    let flkty = getFlickityInstance($el);

    if (!flkty) {
      flkty = flickityInit($el, buildFlickityOptions($el, getGroupCellsByWidth($el)));
    }

    buildCustomNav($el);

    // Bind resize watcher per slider (only once)
    if (!$el.data("customResizeBound")) {
      let lastGroup = getGroupCellsByWidth($el);

      $(window).on(
        "resize.customNavGroupCells",
        debounce(function () {
          const nextGroup = getGroupCellsByWidth($el);
          if (nextGroup === lastGroup) return;
          lastGroup = nextGroup;

          const inst = getFlickityInstance($el);
          if (!inst) return;

          // Destroy & re-init so groupCells applies reliably
          try {
            inst.destroy();
          } catch (e) {}

          $el.removeData("customNavInited");
          $el.removeData("lazyFlickity");
          $el.removeData("flickity");
          $el.removeData("flickityInstance");

          flickityInit($el, buildFlickityOptions($el, nextGroup));

          // Wrap already has nav, buildCustomNav will just mark inited
          buildCustomNav($el);
        }, 150),
      );

      $el.data("customResizeBound", true);
    }
  }

  function initAllSliderNavs() {
    $(".js-slider-nav").each(function () {
      initOrUpdateSlider($(this));
    });
  }

  // =========================
  // Events
  // =========================

  // 1) Preferred: Flatsome triggers when any Flickity slider is ready.
  //    Always check ALL js-slider-nav elements so product grids (courses)
  //    that Flatsome doesn't auto-init on desktop still get initialized.
  $(document).on("flatsome-flickity-ready", function () {
    initAllSliderNavs();
  });

  // 2) Fallback: window load — runs after all scripts (including Flatsome
  //    plugins like lazyFlickity and equalize-box) have fully loaded.
  $(window).on("load", function () {
    initAllSliderNavs();
  });
})(jQuery);
