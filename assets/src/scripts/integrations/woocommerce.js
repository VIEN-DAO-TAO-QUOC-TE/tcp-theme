jQuery(function ($) {
  
  function refreshFragments() {
    $(document.body).trigger("wc_fragment_refresh");
  }

  // Các event phổ biến Woo phát ra sau khi áp/huỷ coupon (tuỳ flow/theme/plugin)
  $(document.body).on(
    "applied_coupon removed_coupon updated_cart_totals",
    refreshFragments,
  );

  // Fallback: nếu có nút apply coupon trong mini cart custom
  $(document).on(
    "submit",
    "form.checkout_coupon, form.woocommerce-cart-form",
    function () {
      setTimeout(refreshFragments, 300);
    },
  );
});
