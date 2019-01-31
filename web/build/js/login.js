webpackJsonp([6],{

/***/ "./assets/js/login.js":
/*!****************************!*\
  !*** ./assets/js/login.js ***!
  \****************************/
/*! dynamic exports provided */
/*! all exports used */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {$(function () {
    var usernameEl = $('#username');
    var passwordEl = $('#password');

    // in a real application, hardcoding the user/password would be idiotic
    // but for the demo application it's very convenient to do so
    if (!usernameEl.val() && !passwordEl.val()) {
        usernameEl.val('jane_admin');
        passwordEl.val('kitten');
    }
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ })

},["./assets/js/login.js"]);