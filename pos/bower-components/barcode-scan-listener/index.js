'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj; };

/* eslint-env browser */

exports.default = {
  /**
   * Listen for scan with specified characteristics
   * @param  {String} scanOptions.barcodePrefix
   * @param  {RegExp} scanOptions.barcodeValueTest - RegExp defining valid scan value (not including prefix).
   * @param  {Boolean} [scanOptions.finishScanOnMatch] - if true, test scan value (not including prefix)
   *   match with barcodeValueTest on each character. If matched, immediately
   *   call the scanHandler with the value. This will generally make scans faster.
   * @param  {Number} [scanOptions.scanDuration] - time allowed to complete the scan.
   * @param  {Function} scanHandler - called with the results of the scan
   * @return {Function} remove this listener
   */

  onScan: function onScan() {
    var _ref = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

    var barcodePrefix = _ref.barcodePrefix;
    var barcodeValueTest = _ref.barcodeValueTest;
    var finishScanOnMatch = _ref.finishScanOnMatch;
    var scanDuration = _ref.scanDuration;
    var scanHandler = arguments[1];

    if (typeof barcodePrefix !== 'string') {
      throw new TypeError('barcodePrefix must be a string');
    }
    if ((typeof barcodeValueTest === 'undefined' ? 'undefined' : _typeof(barcodeValueTest)) !== 'object' || typeof barcodeValueTest.test !== 'function') {
      throw new TypeError('barcodeValueTest must be a regular expression');
    }
    if (finishScanOnMatch != null && typeof finishScanOnMatch !== 'boolean') {
      // eslint-disable-line no-eq-null
      throw new TypeError('finishScanOnMatch must be a boolean');
    }
    if (scanDuration && typeof scanDuration !== 'number') {
      throw new TypeError('scanDuration must be a number');
    }
    if (typeof scanHandler !== 'function') {
      throw new TypeError('scanHandler must be a function');
    }

    scanDuration = scanDuration || 50;
    var finishScanTimeoutId = null;
    var prefixBuffer = '';
    var valueBuffer = '';
    var matchedPrefix = false;
    var finishScan = function finishScan() {
      if (matchedPrefix && barcodeValueTest.test(valueBuffer)) {
        scanHandler(valueBuffer);
      }
      resetScanState();
    };
    var resetScanState = function resetScanState() {
      finishScanTimeoutId = null;
      prefixBuffer = '';
      valueBuffer = '';
      matchedPrefix = false;
    };
    var keypressHandler = function keypressHandler(e) {
      var char = String.fromCharCode(e.which);
      var charIndex = barcodePrefix.indexOf(char);
      var expectedPrefixSlice = barcodePrefix.slice(0, charIndex);

      if (!finishScanTimeoutId) {
        finishScanTimeoutId = setTimeout(finishScan, scanDuration);
      }

      if (prefixBuffer === expectedPrefixSlice && char === barcodePrefix.charAt(charIndex)) {
        prefixBuffer += char;
      } else if (matchedPrefix) {
        valueBuffer += char;
      }

      if (prefixBuffer === barcodePrefix) {
        matchedPrefix = true;
        if (finishScanOnMatch && barcodeValueTest.test(valueBuffer)) {
          clearTimeout(finishScanTimeoutId);
          finishScan();
        }
      }
    };
    var removeListener = function removeListener() {
      document.removeEventListener('keypress', keypressHandler);
    };
    document.addEventListener('keypress', keypressHandler);
    return removeListener;
  }
};
module.exports = exports['default'];