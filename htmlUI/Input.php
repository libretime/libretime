<?php
/**
 * @package Campsite
 */

/**
 * @var array $g_inputErrors Used to store error messages.
 */
global $g_inputErrors;
$g_inputErrors = array();


/**
 * @package Campsite
 */
class Input {
	/**
	 * Please see: {@link http://ca.php.net/manual/en/function.get-magic-quotes-gpc.php
	 * this PHP.net page specifically the user note by php at kaiundina dot de},
	 * for why this is so complicated.
	 *
	 * @param array $p_array
	 * @return array
	 */
	function CleanMagicQuotes($p_array)
	{
	   $gpcList = array();

	   foreach ($p_array as $key => $value) {
	       $decodedKey = stripslashes($key);
	       if (is_array($value)) {
	           $decodedValue = Input::CleanMagicQuotes($value);
	       } else {
	           $decodedValue = stripslashes($value);
	       }
	       $gpcList[$decodedKey] = $decodedValue;
	   }
	   return $gpcList;
	} // fn CleanMagicQuotes


	/**
	 * Get an input value from the $_REQUEST array and check its type.
	 * The default value is returned if the value is not defined in the
	 * $_REQUEST array, or if the value does not match the required type.
	 *
	 * The type 'checkbox' is special - you cannot specify a default
	 * value for this.  The return value will be TRUE or FALSE, but
	 * you can change this by specifying 'numeric' as the 3rd parameter
	 * in which case it will return '1' or '0'.
	 *
	 * Use Input::IsValid() to check if any errors were generated.
	 *
	 * @param string $p_varName
	 *		The index into the $_REQUEST array.
	 *
	 * @param string $p_type
	 *		The type of data expected; can be:
	 * 		"int"
	 * 		"string"
	 *      "array"
	 * 		"checkbox"
	 * 		"boolean"
	 *
	 *      Default is 'string'.
	 *
	 * @param mixed $p_defaultValue
	 * 		The default value to return if the value is not defined in
	 *      the $_REQUEST array, or if the value does not match
	 *      the required type.
	 *
	 * @param boolean $p_errorsOk
	 *		Set to true to ignore any errors for this variable (i.e.
	 *      Input::IsValid() will still return true even if there
	 *      are errors for this varaible).
	 *
	 * @return mixed
	 */
	function Get($p_varName, $p_type = 'string', $p_defaultValue = null, $p_errorsOk = false)
	{
		global $g_inputErrors;
        $p_type = strtolower($p_type);

        if ($p_type == 'checkbox') {
            if (strtolower($p_defaultValue) != 'numeric') {
                return isset($_REQUEST[$p_varName]);
            } else {
                return isset($_REQUEST[$p_varName]) ? '1' : '0';
            }
        }
		if (!isset($_REQUEST[$p_varName])) {
			if (!$p_errorsOk) {
				$g_inputErrors[$p_varName] = 'not set';
			}
			return $p_defaultValue;
		}
		// Clean the slashes
		if (get_magic_quotes_gpc()) {
			if (is_array($_REQUEST[$p_varName])) {
				$_REQUEST[$p_varName] = Input::CleanMagicQuotes($_REQUEST[$p_varName]);
			} else {
				$_REQUEST[$p_varName] = stripslashes($_REQUEST[$p_varName]);
			}
		}
		switch ($p_type) {
		case 'boolean':
			$value = strtolower($_REQUEST[$p_varName]);
			if ( ($value == "true") || (is_numeric($value) && ($value > 0)) ) {
				return true;
			} else {
				return false;
			}
			break;
		case 'int':
			if (!is_numeric($_REQUEST[$p_varName])) {
				if (!$p_errorsOk) {
					$g_inputErrors[$p_varName] = 'Incorrect type.  Expected type '.$p_type
						.', but received type '.gettype($_REQUEST[$p_varName]).'.'
						.' Value is "'.$_REQUEST[$p_varName].'".';
				}
				return $p_defaultValue;
			}
			break;
		case 'string':
			if (!is_string($_REQUEST[$p_varName])) {
				if (!$p_errorsOk) {
					$g_inputErrors[$p_varName] = 'Incorrect type.  Expected type '.$p_type
						.', but received type '.gettype($_REQUEST[$p_varName]).'.'
						.' Value is "'.$_REQUEST[$p_varName].'".';
				}
				return $p_defaultValue;
			}
			break;
		case 'array':
			if (!is_array($_REQUEST[$p_varName])) {
				// Create an array if it isnt one already.
				// Arrays are used with checkboxes and radio buttons.
				// The problem with them is that if there is only one
				// checkbox, the given value will not be an array.  So
				// we make it easy for the programmer by always returning
				// an array.
				$newArray = array();
				$newArray[] = $_REQUEST[$p_varName];
				return $newArray;
//				if (!$p_errorsOk) {
//					$g_inputErrors[$p_varName] = 'Incorrect type.  Expected type '.$p_type
//						.', but received type '.gettype($_REQUEST[$p_varName]).'.'
//						.' Value is "'.$_REQUEST[$p_varName].'".';
//				}
//				return $p_defaultValue;
			}
		}
		return $_REQUEST[$p_varName];
	} // fn get


	/**
	 * Return FALSE if any calls to Input::Get() resulted in an error.
	 * @return boolean
	 */
	function IsValid()
	{
		global $g_inputErrors;
		if (count($g_inputErrors) > 0) {
			return false;
		} else {
			return true;
		}
	} // fn isValid


	/**
	 * Return a default error string.
	 * @return string
	 */
	function GetErrorString()
	{
		global $g_inputErrors;
		ob_start();
		print_r($g_inputErrors);
		$str = ob_get_clean();
		return $str;
	} // fn GetErrorString

} // class Input

?>