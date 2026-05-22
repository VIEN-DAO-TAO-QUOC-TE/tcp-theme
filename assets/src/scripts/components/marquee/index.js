
// Marquee Logo
function Marquee(selector, speed) {
  const parentSelector = document.querySelector(selector);
  if (!parentSelector) {
    return;
  }
  const clone = parentSelector.innerHTML;
  const firstElement = parentSelector.children[0];
  if (!firstElement) {
    console.warn(`Marquee: selector "${selector}" không có children.`);
    return;
  }

  let i = 0;
  let marqueeInterval;

  parentSelector.insertAdjacentHTML("beforeend", clone);
  parentSelector.insertAdjacentHTML("beforeend", clone);

  function startMarquee() {
    marqueeInterval = setInterval(function () {
      firstElement.style.marginLeft = `-${i}px`;
      if (i > firstElement.clientWidth) {
        i = 0;
      }
      i = i + speed;
    }, 0);
  }

  function stopMarquee() {
    clearInterval(marqueeInterval);
  }

  parentSelector.addEventListener("mouseenter", stopMarquee);
  parentSelector.addEventListener("mouseleave", startMarquee);

  startMarquee();
}

window.addEventListener("load", () => Marquee(".logo-slider__swiper", 0.4));
