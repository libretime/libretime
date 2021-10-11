/*
 * jQuery i18n plugin
 * @requires jQuery v1.1 or later
 *
 * See http://recursive-design.com/projects/jquery-i18n/
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Version: 1.0.0 (201210141329)
 */
 (function($) {
  /**
   * i18n provides a mechanism for translating strings using a jscript dictionary.
   *
   */

  /*
   * i18n property list
   */
  $.i18n = {
    
    dict: null,
    
    /**
     * setDictionary()
     *
     * Initialises the dictionary.
     *
     * @param  property_list i18n_dict : The dictionary to use for translation.
     */
    setDictionary: function(i18n_dict) {
        this.dict = i18n_dict;
    },
    
    /**
     * _()
     *
     * Looks the given string up in the dictionary and returns the translation if 
     * one exists. If a translation is not found, returns the original word.
     *
     * @param  string str           : The string to translate.
     * @param  property_list params : params for using printf() on the string.
     *
     * @return string               : Translated word.
     */
    _: function (str, params) {
        var result = str;
        if (this.dict && this.dict[str]) {
            result = this.dict[str];
        }
        
        // Substitute any params.
        return this.printf(result, params);
    },

    /*
     * printf()
     *
     * Substitutes %s with parameters given in list. %%s is used to escape %s.
     *
     * @param  string str    : String to perform printf on.
     * @param  string args   : Array of arguments for printf.
     *
     * @return string result : Substituted string
     */
    printf: function(str, args) {
        if (!args) return str;

        var result = '';
        var search = /%(\d+)\$s/g;
        
        // Replace %n1$ where n is a number.
        var matches = search.exec(str);
        while (matches) {
            var index = parseInt(matches[1], 10) - 1;
            str       = str.replace('%' + matches[1] + '\$s', (args[index]));
          matches   = search.exec(str);
        }
        var parts = str.split('%s');

        if (parts.length > 1) {
            for(var i = 0; i < args.length; i++) {
              // If the part ends with a '%' chatacter, we've encountered a literal
              // '%%s', which we should output as a '%s'. To achieve this, add an
              // 's' on the end and merge it with the next part.
                if (parts[i].length > 0 && parts[i].lastIndexOf('%') == (parts[i].length - 1)) {
                    parts[i] += 's' + parts.splice(i + 1, 1)[0];
                }
                
                // Append the part and the substitution to the result.
                result += parts[i] + args[i];
            }
        }
        
        return result + parts[parts.length - 1];
    }

  };

  /*
   * _t()
   *
   * Allows you to translate a jQuery selector.
   *
   * eg $('h1')._t('some text')
   * 
   * @param  string str           : The string to translate .
   * @param  property_list params : Params for using printf() on the string.
   * 
   * @return element              : Chained and translated element(s).
  */
  $.fn._t = function(str, params) {
    return $(this).text($.i18n._(str, params));
  };

})(jQuery);