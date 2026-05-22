(function () {
  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  ready(function () {
    var buyerEmail = document.getElementById("billing_email");
    var elearningEmail = document.getElementById("tcp_elearing_email");
    var sameAsBuyer = document.getElementById("tcp_elearing_same_as_buyer");

    // UI wrappers
    var emailWrap = document.querySelector(".js-elearning-email");
    var displayWrap = document.querySelector(".js-elearning-email-display");
    var displayValue = document.querySelector(".js-elearning-email-value");

    if (!buyerEmail || !elearningEmail || !sameAsBuyer) return;

    function setElearning(value) {
      elearningEmail.value = value || "";
      elearningEmail.dispatchEvent(new Event("change", { bubbles: true }));
      elearningEmail.dispatchEvent(new Event("input", { bubbles: true }));
    }

    function lockElearning(locked) {
      elearningEmail.disabled = !!locked;
      elearningEmail.readOnly = !!locked;
      elearningEmail.classList.toggle("is-disabled", !!locked);
      elearningEmail.setAttribute("aria-disabled", locked ? "true" : "false");
    }

    function renderDisplay(email) {
      if (!displayWrap || !displayValue) return;
      displayValue.textContent = email || "";
    }

    function showInputMode() {
      if (emailWrap) emailWrap.style.display = "";
      if (displayWrap) displayWrap.style.display = "none";
      lockElearning(false);
    }

    function showMirrorMode() {
      if (emailWrap) emailWrap.style.display = "none";
      if (displayWrap) displayWrap.style.display = "";
      lockElearning(true);
    }

    function syncFromBuyer() {
      var v = buyerEmail.value || "";
      setElearning(v);
      renderDisplay(v);
    }

    function onToggle() {
      if (sameAsBuyer.checked) {
        syncFromBuyer();
        showMirrorMode();
      } else {
        showInputMode();
        // Nếu muốn khi bỏ tick thì clear email elearning:
        // setElearning("");
      }
    }

    // Init
    onToggle();

    // Toggle event
    sameAsBuyer.addEventListener("change", onToggle);

    // Nếu buyer email thay đổi khi đang mirror => sync lại + update UI text
    buyerEmail.addEventListener("input", function () {
      if (sameAsBuyer.checked) syncFromBuyer();
    });
    buyerEmail.addEventListener("change", function () {
      if (sameAsBuyer.checked) syncFromBuyer();
    });
  });
})();
