// (function ($) {
//   function toastTypeFromEl(el) {
//     if (!el) return "info";
//     const cls = el.className || "";
//     if (cls.includes("woocommerce-error")) return "error";
//     if (cls.includes("woocommerce-message")) return "success";
//     if (cls.includes("woocommerce-info")) return "info";
//     return "info";
//   }

//   function showToast(message, type) {
//     const text = (message || "").replace(/\s+/g, " ").trim();
//     if (!text) return;

//     Toastify({
//       text,
//       duration: 3500,
//       gravity: "top",
//       position: "right",
//       close: true,
//       stopOnFocus: true,
//       className: "tcp-toast tcp-toast--" + type
//     }).showToast();
//   }

//   // Đọc notices có sẵn trong DOM rồi bắn toast, sau đó xoá khỏi DOM
//   function consumeNoticesFromDOM(root) {
//     root = root || document;

//     // 1) Flatsome hay bọc kiểu: ul.woocommerce-error.message-wrapper > li > .message-container
//     const wrappers = root.querySelectorAll(
//       ".woocommerce-notices-wrapper, ul.woocommerce-error, ul.woocommerce-message, ul.woocommerce-info, div.woocommerce-message, div.woocommerce-error, div.woocommerce-info"
//     );

//     wrappers.forEach(function (wrap) {
//       const type = toastTypeFromEl(wrap);

//       // Nếu là UL có LI
//       if (wrap.tagName === "UL") {
//         const lis = wrap.querySelectorAll("li");
//         if (lis.length) {
//           lis.forEach(li => showToast(li.textContent, type));
//         } else {
//           showToast(wrap.textContent, type);
//         }
//       } else {
//         showToast(wrap.textContent, type);
//       }

//       // Xoá để không chiếm chỗ trên layout
//       wrap.remove();
//     });
//   }

//   // Parse notices từ AJAX fragments (Woo thường trả fragments trong responseJSON)
//   function consumeNoticesFromAjax(xhr) {
//     try {
//       const json = xhr.responseJSON || null;
//       if (!json) return;

//       const fragments = json.fragments || null;
//       if (!fragments) return;

//       // fragments là object: { "div.widget_shopping_cart_content": "<div>...</div>", ... }
//       Object.keys(fragments).forEach(function (key) {
//         const html = fragments[key];
//         if (!html || typeof html !== "string") return;

//         // Nếu fragment có chứa notice thì parse ra
//         if (
//           html.includes("woocommerce-error") ||
//           html.includes("woocommerce-message") ||
//           html.includes("woocommerce-info") ||
//           html.includes("woocommerce-notices-wrapper") ||
//           html.includes("message-wrapper")
//         ) {
//           const temp = document.createElement("div");
//           temp.innerHTML = html;
//           consumeNoticesFromDOM(temp);
//         }
//       });
//     } catch (e) {
//       // ignore
//     }
//   }

//   // --- Init: page load notices
//   $(function () {
//     consumeNoticesFromDOM(document);
//   });

//   // --- Woo events hay gặp (AJAX add to cart, checkout errors, update cart...)
//   $(document.body).on("added_to_cart", function () {
//     setTimeout(() => consumeNoticesFromDOM(document), 50);
//   });

//   $(document.body).on("checkout_error", function () {
//     setTimeout(() => consumeNoticesFromDOM(document), 50);
//   });

//   $(document.body).on("updated_wc_div updated_cart_totals applied_coupon removed_coupon update_checkout", function () {
//     setTimeout(() => consumeNoticesFromDOM(document), 50);
//   });

//   // --- Catch-all: sau mỗi AJAX request, thử bóc notices từ response fragments
//   $(document).ajaxComplete(function (_evt, xhr) {
//     consumeNoticesFromAjax(xhr);
//     // và nếu Woo đã inject notices vào DOM thì consume luôn
//     setTimeout(() => consumeNoticesFromDOM(document), 50);
//   });
// })(jQuery);


// (function () {
//   const SELECTOR = '.woocommerce-error, .woocommerce-message, .woocommerce-info';

//   const seen = new Set();

//   function ensureRoot() {
//     let root = document.getElementById('tcp-toast-root');
//     if (!root) {
//       root = document.createElement('div');
//       root.id = 'tcp-toast-root';
//       document.body.appendChild(root);
//     }
//     return root;
//   }

//   function typeFromEl(el) {
//     if (el.classList.contains('woocommerce-error')) return 'error';
//     if (el.classList.contains('woocommerce-message')) return 'success';
//     return 'info';
//   }

//   function normalizeText(html) {
//     const tmp = document.createElement('div');
//     tmp.innerHTML = html;

//     // bỏ các button/link forward ra khỏi phần text (để đưa vào actions)
//     tmp.querySelectorAll('a.button, a.wc-forward, .button.wc-forward').forEach(a => a.remove());

//     const text = (tmp.textContent || '').replace(/\s+/g, ' ').trim();
//     return text;
//   }

//   function extractActionLink(el) {
//     // Woo thường render: <a class="button wc-forward" href="...">Xem giỏ hàng</a>
//     const a = el.querySelector('a.button, a.wc-forward, .button.wc-forward');
//     if (!a) return null;
//     return { href: a.getAttribute('href'), label: (a.textContent || '').trim() || 'Xem' };
//   }

//   function hashKey(type, message, action) {
//     return `${type}|${message}|${action ? action.href : ''}`;
//   }

//   function toast({ type, title, message, action, duration = 4500 }) {
//     const root = ensureRoot();

//     const key = hashKey(type, message, action);
//     if (seen.has(key)) return;
//     seen.add(key);

//     const item = document.createElement('div');
//     item.className = 'tcp-toast';
//     item.dataset.type = type;

//     const headerTitle = title || (type === 'error' ? 'Thông báo' : type === 'success' ? 'Thành công' : 'Lưu ý');

//     item.innerHTML = `
//       <div>
//         <p class="tcp-toast__title">${escapeHtml(headerTitle)}</p>
//         <p class="tcp-toast__desc">${escapeHtml(message)}</p>
//         <div class="tcp-toast__actions"></div>
//       </div>
//       <button class="tcp-toast__close" aria-label="Close">✕</button>
//     `;

//     const actions = item.querySelector('.tcp-toast__actions');

//     if (action && action.href) {
//       const btn = document.createElement('a');
//       btn.className = 'tcp-toast__btn tcp-toast__btn-primary';
//       btn.href = action.href;
//       btn.textContent = action.label || 'Xem';
//       actions.appendChild(btn);
//     }

//     item.querySelector('.tcp-toast__close').addEventListener('click', () => {
//       item.remove();
//     });

//     root.appendChild(item);

//     if (duration > 0) {
//       setTimeout(() => item.remove(), duration);
//     }
//   }

//   function escapeHtml(str) {
//     return String(str)
//       .replaceAll('&', '&amp;')
//       .replaceAll('<', '&lt;')
//       .replaceAll('>', '&gt;')
//       .replaceAll('"', '&quot;')
//       .replaceAll("'", '&#039;');
//   }

//   function processNotices(rootNode) {
//     const nodes = rootNode.querySelectorAll ? rootNode.querySelectorAll(SELECTOR) : [];
//     nodes.forEach((el) => {
//       const type = typeFromEl(el);
//       const action = extractActionLink(el);

//       // Woo notice đôi khi nằm trong <ul><li>... nên lấy innerHTML để parse được link
//       const message = normalizeText(el.innerHTML);

//       if (!message) return;

//       toast({
//         type,
//         message,
//         action,
//         duration: type === 'error' ? 6000 : 4500
//       });

//       // remove DOM để khỏi “dính đầu trang”
//       el.remove();
//     });
//   }

//   // 1) Chạy ngay khi DOM ready
//   document.addEventListener('DOMContentLoaded', function () {
//     processNotices(document);
//   });

//   // 2) Bắt notice phát sinh sau (AJAX add to cart, checkout update…)
//   const mo = new MutationObserver((mutations) => {
//     for (const m of mutations) {
//       for (const n of m.addedNodes) {
//         if (!(n instanceof HTMLElement)) continue;

//         // nếu node thêm vào chính là notice
//         if (n.matches && n.matches(SELECTOR)) {
//           processNotices(document);
//           continue;
//         }

//         // hoặc notice nằm bên trong node mới thêm
//         if (n.querySelector && n.querySelector(SELECTOR)) {
//           processNotices(n);
//         }
//       }
//     }
//   });

//   mo.observe(document.documentElement, { childList: true, subtree: true });
// })();



(function () {
  // tự add class để CSS hide notice gốc
  document.documentElement.classList.add('tcp-js');

  const SELECTOR = '.woocommerce-error, .woocommerce-message, .woocommerce-info';
  const seen = new Set();

  function ensureRoot() {
    let root = document.getElementById('tcp-toast-root');
    if (!root) {
      root = document.createElement('div');
      root.id = 'tcp-toast-root';
      document.body.appendChild(root);
    }
    return root;
  }

  function typeFromEl(el) {
    if (el.classList.contains('woocommerce-error')) return 'error';
    if (el.classList.contains('woocommerce-message')) return 'success';
    return 'info';
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function normalizeText(html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;

    tmp.querySelectorAll('a.button, a.wc-forward, .button.wc-forward').forEach(a => a.remove());
    return (tmp.textContent || '').replace(/\s+/g, ' ').trim();
  }

  function extractActionLink(el) {
    const a = el.querySelector('a.button, a.wc-forward, .button.wc-forward');
    if (!a) return null;
    return { href: a.getAttribute('href'), label: (a.textContent || '').trim() || 'Xem' };
  }

  function toast({ type, message, action, duration = 5000 }) {
    const key = `${type}|${message}|${action ? action.href : ''}`;
    if (seen.has(key)) return;
    seen.add(key);

    const root = ensureRoot();

    const item = document.createElement('div');
    item.className = 'tcp-toast';
    item.dataset.type = type;

    const title = type === 'error' ? 'Thông báo' : type === 'success' ? 'Thành công' : 'Lưu ý';

    item.innerHTML = `
      <div>
        <p class="tcp-toast__title">${escapeHtml(title)}</p>
        <p class="tcp-toast__desc">${escapeHtml(message)}</p>
        <div class="tcp-toast__actions"></div>
      </div>
      <button class="tcp-toast__close" aria-label="Close">✕</button>
    `;

    const actions = item.querySelector('.tcp-toast__actions');
    if (action && action.href) {
      const btn = document.createElement('a');
      btn.className = 'tcp-toast__btn tcp-toast__btn-primary';
      btn.href = action.href;
      btn.textContent = action.label || 'Xem';
      actions.appendChild(btn);
    }

    item.querySelector('.tcp-toast__close').addEventListener('click', () => item.remove());
    root.appendChild(item);

    if (duration > 0) setTimeout(() => item.remove(), duration);
  }

  function processNotices(scope) {
    const nodes = (scope.querySelectorAll ? scope.querySelectorAll(SELECTOR) : []);
    nodes.forEach((el) => {
      const message = normalizeText(el.innerHTML);
      if (!message) return;

      const type = typeFromEl(el);
      const action = extractActionLink(el);

      toast({
        type,
        message,
        action,
        duration: type === 'error' ? 6500 : 4500
      });

      // remove notice gốc để không dính đầu trang
      el.remove();
    });
  }

  function boot() {
    // bắt notice đã có sẵn
    processNotices(document);

    // bắt notice xuất hiện sau (AJAX)
    const mo = new MutationObserver((mutations) => {
      for (const m of mutations) {
        for (const n of m.addedNodes) {
          if (!(n instanceof HTMLElement)) continue;

          if (n.matches && n.matches(SELECTOR)) processNotices(document);
          else if (n.querySelector && n.querySelector(SELECTOR)) processNotices(n);
        }
      }
    });

    mo.observe(document.documentElement, { childList: true, subtree: true });
  }

  // chạy chắc chắn dù script load trước hay sau DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
