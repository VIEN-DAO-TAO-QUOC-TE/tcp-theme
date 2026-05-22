import { modules } from "../../core/config";
import { onReady } from "../../core/dom-ready";

if (modules.has("modal")) {
  onReady(() => {
    // init modal
  });
}