(function () {
  // Guard
  if (!window.wp || !wp.data) return;

  const cfg = window.TCP_CHECKOUT_PROTECTION || {};
  const NS = cfg.ns || "tcp_protection";

  // Woo checkout store key
  const STORE = "wc/store/checkout";

  function setData(payload) {
    try {
      const dispatch = wp.data.dispatch(STORE);
      if (dispatch && typeof dispatch.setExtensionData === "function") {
        dispatch.setExtensionData(NS, payload);
      }
    } catch (e) {
      // noop
    }
  }

  function showHint(el, msg) {
    if (!el) return;
    el.textContent = msg || "";
    el.style.display = msg ? "" : "none";
  }

  function init() {
    const wrap = document.querySelector("[data-tcp-protection]");
    if (!wrap) return;

    const checkbox = wrap.querySelector("[data-tcp-protection-check]");
    const hint = wrap.querySelector("[data-tcp-protection-hint]");
    const requiredMsg = (cfg.messages && cfg.messages.required) || "Required";

    // Default: not confirmed
    setData({ confirmed: false });

    // Update on change
    checkbox?.addEventListener("change", () => {
      const confirmed = !!checkbox.checked;
      setData({ confirmed });
      showHint(hint, confirmed ? "" : requiredMsg);
    });

    // Optional: chặn UX ngay trên click place order (không thay server validation)
    document.addEventListener(
      "click",
      (e) => {
        const btn =
          e.target &&
          e.target.closest &&
          e.target.closest(".wc-block-components-checkout-place-order-button");
        if (!btn) return;
        if (!checkbox || checkbox.checked) return;

        e.preventDefault();
        e.stopPropagation();
        showHint(hint, requiredMsg);
        checkbox.focus();
      },
      true,
    );
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
