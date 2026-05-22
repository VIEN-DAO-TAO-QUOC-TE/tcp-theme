jQuery(function ($) {
  const $sticky = $("[data-course-sticky-header]");
  const $hero = $("#hero-cover");
  const $sidebar = $(".c-course__sidebar");
  const $sidebarTop = $(".c-course-sidebar__card--top");

  if (!$sticky.length || !$hero.length) return;

  // init lock: disable animation/transition to prevent flash
  // $sidebar.addClass("is-init");
  // $sticky.addClass("is-init");

  const DELAY_SHOW_STICKY = 180;
  const DELAY_SHOW_SIDEBAR = 120;

  let last = null;
  let t1 = null;
  let t2 = null;
  let tExpandDone = null;
  let tResize = null;

  const clearTimers = () => {
    if (t1) window.clearTimeout(t1);
    if (t2) window.clearTimeout(t2);
    if (tExpandDone) window.clearTimeout(tExpandDone);
    t1 = null;
    t2 = null;
    tExpandDone = null;
  };

  const measureNaturalHeight = (el) => {
    const clone = el.cloneNode(true);

    clone.classList.remove("is-hidden", "is-showing");
    clone.style.cssText = `
      position: absolute !important;
      left: -99999px !important;
      top: 0 !important;
      width: ${el.offsetWidth}px !important;
      max-height: none !important;
      height: auto !important;
      overflow: visible !important;
      opacity: 1 !important;
      transform: none !important;
      animation: none !important;
      transition: none !important;
      pointer-events: none !important;
      visibility: hidden !important;
    `;

    document.body.appendChild(clone);
    const h = clone.scrollHeight || clone.offsetHeight || 1000;
    clone.remove();
    return h;
  };

  const setTopMax = () => {
    if (!$sidebarTop.length) return;
    const el = $sidebarTop.get(0);
    const h = measureNaturalHeight(el);
    el.style.setProperty("--tcp-top-max", `${h}px`);
  };

  const showSticky = () => {
    $sticky.addClass("is-visible").attr("aria-hidden", "false");
  };

  const hideSticky = () => {
    $sticky.removeClass("is-visible").attr("aria-hidden", "true");
  };

  const hideSidebarTop = () => {
    if (!$sidebarTop.length) return;

    // đo trước khi collapse
    setTopMax();

    $sidebarTop.removeClass("is-showing").addClass("is-hidden");
    $sidebar.addClass("active");
  };

  const showSidebarTop = () => {
    if (!$sidebarTop.length) return;

    // đo trước khi expand
    setTopMax();

    $sidebarTop.removeClass("is-hidden").addClass("is-showing");
    $sidebar.removeClass("active");

    if (tExpandDone) window.clearTimeout(tExpandDone);
    tExpandDone = window.setTimeout(() => {
      $sidebarTop.removeClass("is-showing");
    }, 420); // >= duration animation
  };

  const applyState = (shouldShowSticky) => {
    if (last === shouldShowSticky) return;
    last = shouldShowSticky;

    clearTimers();

    if (shouldShowSticky) {
      hideSidebarTop();
      t1 = window.setTimeout(showSticky, DELAY_SHOW_STICKY);
    } else {
      hideSticky();
      t2 = window.setTimeout(showSidebarTop, DELAY_SHOW_SIDEBAR);
    }
  };

  const heroEl = $hero.get(0);
  const io = new IntersectionObserver(
    (entries) => {
      const entry = entries[0];
      applyState(!entry.isIntersecting);
    },
    { threshold: 0, rootMargin: "-80px 0px 0px 0px" },
  );

  io.observe(heroEl);

  // init
  setTopMax();
  hideSticky();
  showSidebarTop();

  // update var on resize (debounced)
  $(window).on("resize", () => {
    if (tResize) window.clearTimeout(tResize);
    tResize = window.setTimeout(setTopMax, 150);
  });

  // // Init (no animation)
  // setTopMax();
  // hideSticky();
  // showSidebarTop();

  // enable animation after first paint
  // requestAnimationFrame(() => {
  //   requestAnimationFrame(() => {
  //     $sidebar.removeClass("is-init");
  //     $sticky.removeClass("is-init");
  //   });
  // });

  // $(window).on("load", () => {
  //   setTopMax();
  // });
});
