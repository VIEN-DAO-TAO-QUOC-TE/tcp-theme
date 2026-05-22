import "./flickity-custom";

(function ($) {
  $(document).on("flatsome-flickity-ready", function () {
    // code here
  });

  $(document).ready(function () {
    $(".row-slider-4").lazyFlickity({
      cellAlign: "left",
      contain: true,
      pageDots: true,
      groupCells: 4,
      wrapAround: false,
      // freeScroll: true
      prevNextButtons: false,
      autoPlay: true,
      percentPosition: true,
      lazyLoad: 1,
    });

    $(".row-slider-3").lazyFlickity({
      cellAlign: "left",
      contain: true,
      pageDots: false,
      groupCells: 3,
      wrapAround: false,
      // freeScroll: true
    });

    $(".row-slider-2").lazyFlickity({
      cellAlign: "left",
      contain: true,
      pageDots: false,
      groupCells: 2,
      wrapAround: false,
      // freeScroll: true
    });

  });
})(jQuery);
