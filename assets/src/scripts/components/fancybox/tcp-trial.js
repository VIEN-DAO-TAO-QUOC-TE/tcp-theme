// document.addEventListener("DOMContentLoaded", () => {
//   if (!window.Fancybox) return;

//   Fancybox.bind('[data-fancybox^="tcp-trial-"]', {
//     // video responsive
//     defaultType: "video",

//     // UI
//     animated: true,
//     dragToClose: true,
//     closeButton: "top",
//     showClass: "f-fadeIn",
//     hideClass: "f-fadeOut",

//     // Toolbar tối giản
//     Toolbar: {
//       display: {
//         left: [],
//         middle: [],
//         right: ["close"],
//       },
//     },

//     // Không hiển thị thumbnails
//     Thumbs: false,

//     // Next/Prev theo group module
//     Carousel: {
//       infinite: false,
//       friction: 0.9,
//     },

//     // Nếu muốn thêm caption từ attribute (tuỳ chọn)
//     caption: (fancybox, slide) =>
//       slide.triggerEl?.getAttribute("aria-label") || "",

//     on: {
//       reveal: (fancybox, slide) => {
//         // set max width cho video
//         slide.$content.style.maxWidth = "980px";
//       },
//     },
//   });
// });


document.addEventListener("DOMContentLoaded", () => {
  if (!window.Fancybox) return;

  Fancybox.bind('[data-fancybox^="tcp-preview-p"]', {
    Toolbar: {
      display: { left: [], middle: [], right: ["close"] },
    },
    Thumbs: {
      type: "classic",   // sẽ hiện strip/list thumbnails
    },
    Carousel: {
      infinite: false,
    },
    caption: (fancybox, slide) => slide.caption || slide.triggerEl?.dataset?.caption || "",
  });
});
