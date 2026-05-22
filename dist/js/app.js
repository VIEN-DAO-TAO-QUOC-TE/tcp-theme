/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/scripts/theme.js"
/*!*************************************!*\
  !*** ./assets/src/scripts/theme.js ***!
  \*************************************/
() {

eval("{function Marquee(selector, speed) {\n  var parentSelector = document.querySelector(selector);\n  if (!parentSelector) {\n    return;\n  }\n  var clone = parentSelector.innerHTML;\n  var firstElement = parentSelector.children[0];\n  if (!firstElement) {\n    console.warn(\"Marquee: selector \\\"\".concat(selector, \"\\\" kh\\xF4ng c\\xF3 children.\"));\n    return;\n  }\n  var i = 0;\n  var marqueeInterval;\n  parentSelector.insertAdjacentHTML(\"beforeend\", clone);\n  parentSelector.insertAdjacentHTML(\"beforeend\", clone);\n  function startMarquee() {\n    marqueeInterval = setInterval(function () {\n      firstElement.style.marginLeft = \"-\".concat(i, \"px\");\n      if (i > firstElement.clientWidth) {\n        i = 0;\n      }\n      i = i + speed;\n    }, 0);\n  }\n  function stopMarquee() {\n    clearInterval(marqueeInterval);\n  }\n  parentSelector.addEventListener(\"mouseenter\", stopMarquee);\n  parentSelector.addEventListener(\"mouseleave\", startMarquee);\n  startMarquee();\n}\nwindow.addEventListener(\"load\", function () {\n  return Marquee(\".logo-slider__swiper\", 0.4);\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvc3JjL3NjcmlwdHMvdGhlbWUuanMiLCJuYW1lcyI6WyJNYXJxdWVlIiwic2VsZWN0b3IiLCJzcGVlZCIsInBhcmVudFNlbGVjdG9yIiwiZG9jdW1lbnQiLCJxdWVyeVNlbGVjdG9yIiwiY2xvbmUiLCJpbm5lckhUTUwiLCJmaXJzdEVsZW1lbnQiLCJjaGlsZHJlbiIsImNvbnNvbGUiLCJ3YXJuIiwiY29uY2F0IiwiaSIsIm1hcnF1ZWVJbnRlcnZhbCIsImluc2VydEFkamFjZW50SFRNTCIsInN0YXJ0TWFycXVlZSIsInNldEludGVydmFsIiwic3R5bGUiLCJtYXJnaW5MZWZ0IiwiY2xpZW50V2lkdGgiLCJzdG9wTWFycXVlZSIsImNsZWFySW50ZXJ2YWwiLCJhZGRFdmVudExpc3RlbmVyIiwid2luZG93Il0sInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UQ1AtVGhlbWUvLi9hc3NldHMvc3JjL3NjcmlwdHMvdGhlbWUuanM/YzQ4YSJdLCJzb3VyY2VzQ29udGVudCI6WyJmdW5jdGlvbiBNYXJxdWVlKHNlbGVjdG9yLCBzcGVlZCkge1xyXG4gIGNvbnN0IHBhcmVudFNlbGVjdG9yID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihzZWxlY3Rvcik7XHJcbiAgaWYgKCFwYXJlbnRTZWxlY3Rvcikge1xyXG4gICAgcmV0dXJuO1xyXG4gIH1cclxuICBjb25zdCBjbG9uZSA9IHBhcmVudFNlbGVjdG9yLmlubmVySFRNTDtcclxuICBjb25zdCBmaXJzdEVsZW1lbnQgPSBwYXJlbnRTZWxlY3Rvci5jaGlsZHJlblswXTtcclxuICBpZiAoIWZpcnN0RWxlbWVudCkge1xyXG4gICAgY29uc29sZS53YXJuKGBNYXJxdWVlOiBzZWxlY3RvciBcIiR7c2VsZWN0b3J9XCIga2jDtG5nIGPDsyBjaGlsZHJlbi5gKTtcclxuICAgIHJldHVybjtcclxuICB9XHJcblxyXG4gIGxldCBpID0gMDtcclxuICBsZXQgbWFycXVlZUludGVydmFsO1xyXG5cclxuICBwYXJlbnRTZWxlY3Rvci5pbnNlcnRBZGphY2VudEhUTUwoXCJiZWZvcmVlbmRcIiwgY2xvbmUpO1xyXG4gIHBhcmVudFNlbGVjdG9yLmluc2VydEFkamFjZW50SFRNTChcImJlZm9yZWVuZFwiLCBjbG9uZSk7XHJcblxyXG4gIGZ1bmN0aW9uIHN0YXJ0TWFycXVlZSgpIHtcclxuICAgIG1hcnF1ZWVJbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uICgpIHtcclxuICAgICAgZmlyc3RFbGVtZW50LnN0eWxlLm1hcmdpbkxlZnQgPSBgLSR7aX1weGA7XHJcbiAgICAgIGlmIChpID4gZmlyc3RFbGVtZW50LmNsaWVudFdpZHRoKSB7XHJcbiAgICAgICAgaSA9IDA7XHJcbiAgICAgIH1cclxuICAgICAgaSA9IGkgKyBzcGVlZDtcclxuICAgIH0sIDApO1xyXG4gIH1cclxuXHJcbiAgZnVuY3Rpb24gc3RvcE1hcnF1ZWUoKSB7XHJcbiAgICBjbGVhckludGVydmFsKG1hcnF1ZWVJbnRlcnZhbCk7XHJcbiAgfVxyXG5cclxuICBwYXJlbnRTZWxlY3Rvci5hZGRFdmVudExpc3RlbmVyKFwibW91c2VlbnRlclwiLCBzdG9wTWFycXVlZSk7XHJcbiAgcGFyZW50U2VsZWN0b3IuYWRkRXZlbnRMaXN0ZW5lcihcIm1vdXNlbGVhdmVcIiwgc3RhcnRNYXJxdWVlKTtcclxuXHJcbiAgc3RhcnRNYXJxdWVlKCk7XHJcbn1cclxuXHJcbndpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwibG9hZFwiLCAoKSA9PiBNYXJxdWVlKFwiLmxvZ28tc2xpZGVyX19zd2lwZXJcIiwgMC40KSk7XHJcbiJdLCJtYXBwaW5ncyI6IkFBQUEsU0FBU0EsT0FBT0EsQ0FBQ0MsUUFBUSxFQUFFQyxLQUFLLEVBQUU7RUFDaEMsSUFBTUMsY0FBYyxHQUFHQyxRQUFRLENBQUNDLGFBQWEsQ0FBQ0osUUFBUSxDQUFDO0VBQ3ZELElBQUksQ0FBQ0UsY0FBYyxFQUFFO0lBQ25CO0VBQ0Y7RUFDQSxJQUFNRyxLQUFLLEdBQUdILGNBQWMsQ0FBQ0ksU0FBUztFQUN0QyxJQUFNQyxZQUFZLEdBQUdMLGNBQWMsQ0FBQ00sUUFBUSxDQUFDLENBQUMsQ0FBQztFQUMvQyxJQUFJLENBQUNELFlBQVksRUFBRTtJQUNqQkUsT0FBTyxDQUFDQyxJQUFJLHdCQUFBQyxNQUFBLENBQXVCWCxRQUFRLGdDQUFzQixDQUFDO0lBQ2xFO0VBQ0Y7RUFFQSxJQUFJWSxDQUFDLEdBQUcsQ0FBQztFQUNULElBQUlDLGVBQWU7RUFFbkJYLGNBQWMsQ0FBQ1ksa0JBQWtCLENBQUMsV0FBVyxFQUFFVCxLQUFLLENBQUM7RUFDckRILGNBQWMsQ0FBQ1ksa0JBQWtCLENBQUMsV0FBVyxFQUFFVCxLQUFLLENBQUM7RUFFckQsU0FBU1UsWUFBWUEsQ0FBQSxFQUFHO0lBQ3RCRixlQUFlLEdBQUdHLFdBQVcsQ0FBQyxZQUFZO01BQ3hDVCxZQUFZLENBQUNVLEtBQUssQ0FBQ0MsVUFBVSxPQUFBUCxNQUFBLENBQU9DLENBQUMsT0FBSTtNQUN6QyxJQUFJQSxDQUFDLEdBQUdMLFlBQVksQ0FBQ1ksV0FBVyxFQUFFO1FBQ2hDUCxDQUFDLEdBQUcsQ0FBQztNQUNQO01BQ0FBLENBQUMsR0FBR0EsQ0FBQyxHQUFHWCxLQUFLO0lBQ2YsQ0FBQyxFQUFFLENBQUMsQ0FBQztFQUNQO0VBRUEsU0FBU21CLFdBQVdBLENBQUEsRUFBRztJQUNyQkMsYUFBYSxDQUFDUixlQUFlLENBQUM7RUFDaEM7RUFFQVgsY0FBYyxDQUFDb0IsZ0JBQWdCLENBQUMsWUFBWSxFQUFFRixXQUFXLENBQUM7RUFDMURsQixjQUFjLENBQUNvQixnQkFBZ0IsQ0FBQyxZQUFZLEVBQUVQLFlBQVksQ0FBQztFQUUzREEsWUFBWSxDQUFDLENBQUM7QUFDaEI7QUFFQVEsTUFBTSxDQUFDRCxnQkFBZ0IsQ0FBQyxNQUFNLEVBQUU7RUFBQSxPQUFNdkIsT0FBTyxDQUFDLHNCQUFzQixFQUFFLEdBQUcsQ0FBQztBQUFBLEVBQUMiLCJpZ25vcmVMaXN0IjpbXX0=\n//# sourceURL=webpack-internal:///./assets/src/scripts/theme.js\n\n}");

/***/ },

/***/ "./assets/src/scss/theme.scss"
/*!************************************!*\
  !*** ./assets/src/scss/theme.scss ***!
  \************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("{__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9hc3NldHMvc3JjL3Njc3MvdGhlbWUuc2NzcyIsIm1hcHBpbmdzIjoiO0FBQUEiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UQ1AtVGhlbWUvLi9hc3NldHMvc3JjL3Njc3MvdGhlbWUuc2Nzcz80N2Q3Il0sInNvdXJjZXNDb250ZW50IjpbIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyJdLCJuYW1lcyI6W10sImlnbm9yZUxpc3QiOltdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./assets/src/scss/theme.scss\n\n}");

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/app": 0,
/******/ 			"css/app": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkTCP_Theme"] = self["webpackChunkTCP_Theme"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./assets/src/scripts/theme.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./assets/src/scss/theme.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;