(function ($) {
  $(function () {
    $(".c-cta-banner__layout").each(function () {
      const $layout = $(this);
      const $img = $layout.find(".c-cta-banner__media img").first();
      if (!$img.length) return;

      const url = $img.attr("src");
      if (!url) return;

      const $target = $layout.find(".c-cta-banner__layout-col > .col-inner").first();
      if (!$target.length) return;

      // tránh tạo lặp
      if ($target.find(".c-cta-banner__bg-float").length) {
        $img.closest(".c-cta-banner__media").hide();
        return;
      }

      // đảm bảo target làm mốc positioning
      $target.css("position", "relative");

      // tạo ảnh overlay
      const $float = $(`
        <img
          class="c-cta-banner__bg-float"
          src="${url}"
          alt=""
          aria-hidden="true"
          loading="lazy"
          decoding="async"
        />
      `);

      $target.append($float);

      // Ẩn cột media gốc
      $img.closest(".c-cta-banner__media").hide();
    });
  });
})(jQuery);
