webpackJsonp([5],{

/***/ "./assets/js/jquery.instantSearch.js":
/*!*******************************************!*\
  !*** ./assets/js/jquery.instantSearch.js ***!
  \*******************************************/
/*! dynamic exports provided */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(__webpack_provided_window_dot_jQuery) {/**
 * jQuery plugin for an instant searching.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
(function ($) {
    $.fn.instantSearch = function (config) {
        return this.each(function () {
            initInstantSearch(this, $.extend(true, defaultConfig, config || {}));
        });
    };

    var defaultConfig = {
        minQueryLength: 2,
        maxPreviewItems: 10,
        previewDelay: 500,
        noItemsFoundMessage: 'No results found.'
    };

    function debounce(fn, delay) {
        var timer = null;
        return function () {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(context, args);
            }, delay);
        };
    }

    var initInstantSearch = function initInstantSearch(el, config) {
        var $input = $(el);
        var $form = $input.closest('form');
        var $preview = $('<ul class="search-preview list-group">').appendTo($form);

        var setPreviewItems = function setPreviewItems(items) {
            $preview.empty();

            $.each(items, function (index, item) {
                if (index > config.maxPreviewItems) {
                    return;
                }

                addItemToPreview(item);
            });
        };

        var addItemToPreview = function addItemToPreview(item) {
            var $link = $('<a>').attr('href', item.url).text(item.title);
            var $title = $('<h3>').attr('class', 'm-b-0').append($link);
            var $summary = $('<p>').text(item.summary);
            var $result = $('<div>').append($title).append($summary);

            $preview.append($result);
        };

        var noItemsFound = function noItemsFound() {
            var $result = $('<div>').text(config.noItemsFoundMessage);

            $preview.empty();
            $preview.append($result);
        };

        var updatePreview = function updatePreview() {
            var query = $.trim($input.val()).replace(/\s{2,}/g, ' ');

            if (query.length < config.minQueryLength) {
                $preview.empty();
                return;
            }

            $.getJSON($form.attr('action') + '?' + $form.serialize(), function (items) {
                if (items.length === 0) {
                    noItemsFound();
                    return;
                }

                setPreviewItems(items);
            });
        };

        $input.focusout(function (e) {
            $preview.fadeOut();
        });

        $input.focusin(function (e) {
            $preview.fadeIn();
            updatePreview();
        });

        $input.keyup(debounce(updatePreview, config.previewDelay));
    };
})(__webpack_provided_window_dot_jQuery);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ }),

/***/ "./assets/js/search.js":
/*!*****************************!*\
  !*** ./assets/js/search.js ***!
  \*****************************/
/*! no exports provided */
/*! all exports used */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* WEBPACK VAR INJECTION */(function($) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__jquery_instantSearch_js__ = __webpack_require__(/*! ./jquery.instantSearch.js */ "./assets/js/jquery.instantSearch.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__jquery_instantSearch_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__jquery_instantSearch_js__);


$(function () {
    $('.search-field').instantSearch({
        previewDelay: 100
    });
});
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ })

},["./assets/js/search.js"]);