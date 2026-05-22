export const modules = new Set(
  (document.body.dataset.modules || "")
    .split(",")
    .map(s => s.trim())
    .filter(Boolean)
);