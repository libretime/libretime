/**
@namespace Converts JSON to CSV.

Compress with: http://jscompress.com/
*/
(function (window) {
    "use strict";
    /**
    Default constructor
    */
    var _CSV = function (JSONData) {
        if (typeof JSONData === 'undefined')
            return;

        var csvData = typeof JSONData != 'object' ? JSON.parse(settings.JSONData) : JSONData,
            csvHeaders,
            csvEncoding = 'data:text/csv;charset=utf-8,',
            csvOutput = "",
            csvRows = [],
            BREAK = '\r\n',
            DELIMITER = ',',
			FILENAME = "export.csv";

        // Get and Write the headers
        csvHeaders = Object.keys(csvData[0]);
        csvOutput += csvHeaders.join(',') + BREAK;

        for (var i = 0; i < csvData.length; i++) {
            var rowElements = [];
            for(var k = 0; k < csvHeaders.length; k++) {
                rowElements.push(csvData[i][csvHeaders[k]]);
            } // Write the row array based on the headers
            csvRows.push(rowElements.join(DELIMITER));
        }

        csvOutput += csvRows.join(BREAK);

        // Initiate Download
        var a = document.createElement("a");

        if (navigator.msSaveBlob) { // IE10
            navigator.msSaveBlob(new Blob([csvOutput], { type: "text/csv" }), FILENAME);
        } else if ('download' in a) { //html5 A[download]
            a.href = csvEncoding + encodeURIComponent(csvOutput);
            a.download = FILENAME;
            document.body.appendChild(a);
            setTimeout(function() {
                a.click();
                document.body.removeChild(a);
            }, 66);
        } else if (document.execCommand) { // Other version of IE
            var oWin = window.open("about:blank", "_blank");
            oWin.document.write(csvOutput);
            oWin.document.close();
            oWin.document.execCommand('SaveAs', true, FILENAME);
            oWin.close();
        } else {
            alert("Support for your specific browser hasn't been created yet, please check back later.");
        }
    };

    window.CSVExport = _CSV;

})(window);

// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys) {
  Object.keys = (function() {
    'use strict';
    var hasOwnProperty = Object.prototype.hasOwnProperty,
        hasDontEnumBug = !({ toString: null }).propertyIsEnumerable('toString'),
        dontEnums = [
          'toString',
          'toLocaleString',
          'valueOf',
          'hasOwnProperty',
          'isPrototypeOf',
          'propertyIsEnumerable',
          'constructor'
        ],
        dontEnumsLength = dontEnums.length;

    return function(obj) {
      if (typeof obj !== 'object' && (typeof obj !== 'function' || obj === null)) {
        throw new TypeError('Object.keys called on non-object');
      }

      var result = [], prop, i;

      for (prop in obj) {
        if (hasOwnProperty.call(obj, prop)) {
          result.push(prop);
        }
      }

      if (hasDontEnumBug) {
        for (i = 0; i < dontEnumsLength; i++) {
          if (hasOwnProperty.call(obj, dontEnums[i])) {
            result.push(dontEnums[i]);
          }
        }
      }
      return result;
    };
  }());
}