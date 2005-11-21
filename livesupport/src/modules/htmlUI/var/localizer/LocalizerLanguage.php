<?PHP
/**
 * @package Campware
 */

/**
 * Includes
 */
require_once 'PEAR.php';
require_once dirname(__FILE__).'/LocalizerConfig.php';
require_once dirname(__FILE__).'/LocalizerFileFormat.php';

/**
 * @package Campware
 */
class LocalizerLanguage {
	var $m_translationTable = array();
	var $m_languageCode = '';
	var $m_countryCode = '';
	var $m_languageId = '';
	var $m_mode = '';
	var $m_prefix = '';
	var $m_filePath = '';
	
	/**
	 * A LocalizerLanguage is basically a translation table.
	 *
	 * You can use this class to manipulate the translation table: 
	 * such as add, delete, and move strings.
	 *
	 * @param string $p_prefix
	 *		The beginning of the file name, up to the first dot ('.').
	 *
	 * @param string $p_directory
	 *		The location of the language file, relative to LOCALIZER_BASE_DIR.
	 *
	 * @param string $p_languageId
	 *		The language ID for this language, which can be in one of two forms:
	 *      1) The two-letter language code (e.g. "en").
	 *      2) The two-letter language code, underscore, two-letter country code
	 *         (e.g. "en_US")
	 */
	function LocalizerLanguage($p_prefix, $p_languageId = null) 
	{
		if (!is_null($p_languageId)) {
			$this->setLanguageId($p_languageId);
		}
		$this->m_prefix = $p_prefix;
	} // constructor
	

	/**
	 * Return the filename prefix.
	 * @return string
	 */
	function getPrefix() 
	{
	    return $this->m_prefix;
	} // fn getPrefix
	
	
	/**
	 * This will return 'gs' or 'xml'
	 * @return string
	 */
	function getMode() 
	{
		return $this->m_mode;
	} // fn getMode
	
	
	/**
	 * Set the mode to be 'xml' or 'gs'.
	 * @param string $p_value
	 * @return void
	 */
	function setMode($p_value) 
	{
		$p_value = strtolower($p_value);
		if (($p_value == 'xml') || ($p_value == 'gs')) {
			$this->m_mode = $p_value;
		}
	} // fn setMode
	
	
	/**
	 * Set the language code - this can take either the two-letter language code
	 * or the LL_CC extended version , where LL is the language code and CC
	 * is the country code.
	 *
	 * @param string $p_languageId
	 * @return void
	 */
	function setLanguageId($p_languageId) 
	{	    
		if (strlen($p_languageId) > 2) {
			list ($this->m_languageCode, $this->m_countryCode) = explode('_', $p_languageId);
			$this->m_languageId = $p_languageId;
		}
		else {
			$this->m_languageCode = $p_languageId;
			$this->m_languageId = $p_languageId;
		}		
	} // fn setLanguageId
	
	
    /** 
     * Register a string in the translation table.
     * @param string $p_key
     * @param string $p_value
     * @return void
     */
    function registerString($p_key, $p_value) 
    {
        if (substr($p_value, strlen($p_value)-3) == ":en"){
            $p_value = substr($p_value, 0, strlen($p_value)-3);
        }
        $this->m_translationTable[$p_key] = $p_value;
    } // fn registerString


    /**
     * Return the total number of strings in the translation table.
     * @return int
     */
    function getNumStrings() 
    {
    	return count($this->m_translationTable);
    } // fn getNumStrings
    
    
    /**
     * Get the language code that is in the form <two_letter_language_code>_<english_name_of_language>.
     *
     * @return string
     */
	function getLanguageId() 
	{
		return $this->m_languageId;
	} // fn getLanguageId
	
	
	/**
	 * Get the two-letter language code for the translation table.
	 * @return string
	 */
	function getLanguageCode() 
	{
		return $this->m_languageCode;
	} // fn getLanguageCode
	

	/**
	 * Get the two-letter country code.
	 * @return string
	 */
	function getCountryCode() 
	{
	    return $this->m_countryCode;
	} // fn getCountryCode
	
	
	/**
	 * Return the file path for the last file loaded.
	 * @return string
	 */
	function getSourceFile() 
	{
		return $this->m_filePath;
	} // fn getSourceFile
	
	
	/**
	 * This is only for use by the LocalizerFileFormat functions.
	 * @access private
	 */
	function _setSourceFile($p_value) 
	{
	    $this->m_filePath = $p_value;
	} // fn _setSourceFile
	
	
	/**
	 * Return true if this LocalizerLanguage has the exact same
	 * translation strings as the given LocalizerLanguage.
	 *
	 * @param LocalizerLanguage $p_localizerLanguage
	 * @return boolean
	 */
	function equal($p_localizerLanguage) 
	{
		if (count($this->m_translationTable) != count($p_localizerLanguage->m_translationTable)) {
			return false;
		}
		foreach ($this->m_translationTable as $key => $value) {
			if (!array_key_exists($key, $p_localizerLanguage->m_translationTable)) {
				//echo "missing translation string: '$key'<br>";
				return false;
			}
			if ($p_localizerLanguage->m_translationTable[$key] != $value) {
				//echo "Non-matching values: '".$p_localizerLanguage->m_translationTable[$key]."' != '".$value."'<br>";
				return false;
			}
		}
		return true;
	} // fn equal
	

	/**
	 * Return a table indexed by the english language name, with the value being the 
	 * target language equivalent.
	 *
	 * @return array
	 */
	function getTranslationTable() 
	{
		return $this->m_translationTable;
	}
	
	
	/**
	 * Get the full path to the translation file.
	 *
	 * @param string $p_mode
	 *		Either 'gs' or 'xml'.
     * @return string
     */
    function getFilePath($p_mode = null) 
    {
        global $g_localizerConfig;
    	if (is_null($p_mode)) {
    		$p_mode = $this->m_mode;
    	}    	
    	if ($p_mode == 'xml') {
        	$relativePath = '/'.$this->m_languageId.'/'.$this->m_prefix.'.xml';
    	}
    	else {
    		$relativePath = '/'.$this->m_languageCode.'/'.$this->m_prefix.'.php';
    	}
    	return $g_localizerConfig['TRANSLATION_DIR'].$relativePath;
    } // fn getFilePath

    
    /**
     * Return TRUE if the given string exists in the translation table.
     *
     * @param string $p_string
     *
     * @return boolean
     */
    function keyExists($p_string) 
    {
    	return (isset($this->m_translationTable[$p_string]));
    } // fn stringExists
    
    
    /**
     * Add a string to the translation table.
     *
     * @param string $p_key
     *		The english translation of the string.
     *
     * @param string $p_value
     *		Optional.  If not specified, the value will be set to the same
     *		value as the key.
     *
     * @param int $p_position
     *		Optional.  By default the string will be added to the end of the 
     *		translation table.
     *
     * @return boolean
     */
    function addString($p_key, $p_value = null, $p_position = null) 
    {
    	if (!is_null($p_position) 
    		&& (!is_numeric($p_position) || ($p_position < 0) 
    			|| ($p_position > count($this->m_translationTable)))) {
    		return false;
    	}
    	if (!is_string($p_key) || !is_string($p_value)) {
    		return false;
    	}
    	if (is_null($p_position)) {
    		// Position is not specified - add the string at the end
    		if (is_null($p_value)) {
    			$this->m_translationTable[$p_key] = $p_key;
    		}
    		else {
    			$this->m_translationTable[$p_key] = $p_value;
    		}
    		return true;
    	}
		else {
			// The position is specified
			$begin = array_slice($this->m_translationTable, 0, $p_position);
			$end = array_slice($this->m_translationTable, $p_position);
			if (is_null($p_value)) {
				$newStr = array($p_key => $p_key);
			}
			else {
				$newStr = array($p_key => $p_value);
			}
			$this->m_translationTable = array_merge($begin, $newStr, $end);
			return true;
		}
    } // fn addString
    
    
    /**
     * Get the position of a key or a value.
     * @param string $p_key
     * @param string $p_value
     * @return mixed
     *		The position of the key/value in the array, FALSE if not found.
     */
    function getPosition($p_key = null, $p_value = null) 
    {
    	$position = 0;
    	if (!is_null($p_key)) {
	    	foreach ($this->m_translationTable as $key => $value) {
	    		if ($p_key == $key) {
	    			return $position;
	    		}
	    		$position++;
	    	}
    	}
    	elseif (!is_null($p_value)) {
	    	foreach ($this->m_translationTable as $value) {
	    		if ($p_value == $value) {
	    			return $position;
	    		}
	    		$position++;
	    	}    		
    	}
    	return false;
    } // fn getPosition
    

    /**
     * Get the string at the given position.
     *
     * @return array
     * 		An array of two elements, the first is the key, the second is the value.
     *		They are indexed by 'key' and 'value'.
     */
    function getStringAtPosition($p_position) 
    {
    	if (is_null($p_position) || !is_numeric($p_position) || ($p_position < 0) 
    			|| ($p_position > count($this->m_translationTable))) {
    		return false;
    	}
    	$returnValue = array_splice($this->m_translationTable, $p_position, 0);
    	$keys = array_keys($returnValue);
    	$key = array_pop($keys);
    	$value = array_pop($returnValue);
    	return array('key' => $key, 'value' => $value);
    } // fn getStringAtPosition
    
    
    /**
     * Change the key and optionally the value of the 
     * translation string.  If the value isnt specified,
     * it is not changed.  If the key does not exist,
     * it will be added.  In this case, you can use p_position
     * to specify where to add the string.
     *
     * @param string $p_oldKey
     * @param string $p_newKey
     * @param string $p_value
     * @param int $p_position
     * @return boolean
     */
    function updateString($p_oldKey, $p_newKey, $p_value = null, $p_position = null) 
    {
    	if (!is_string($p_oldKey) || !is_string($p_newKey)) {
    		return false;
    	}
    	// Does the old string exist?
    	if (!isset($this->m_translationTable[$p_oldKey])) {
    		return $this->addString($p_newKey, $p_value, $p_position);
    	}
    	if ($p_oldKey == $p_newKey) {
	    	// Just updating the value
    		if (!is_null($p_value) && ($p_value != $this->m_translationTable[$p_oldKey])) {
    			$this->m_translationTable[$p_oldKey] = $p_value;
    			return true;
    		}
    		// No changes
    		else {
    			return true;
    		}
    	}
    	
    	// Updating the key (and possibly the value)
    	if (is_null($p_value)) {
    		$p_value = $this->m_translationTable[$p_oldKey];
    	}
    	$position = $this->getPosition($p_oldKey);
    	$success = $this->deleteString($p_oldKey);
    	$success &= $this->addString($p_newKey, $p_value, $position);
    	return $success;
    } // fn updateString
    
    
    /**
     * Move a string to a different position in the translation array.
     * This allows similiar strings to be grouped together.
     *
     * @param int $p_startPositionOrKey
     * @param int $p_endPosition
     *
     * @return boolean
     *		TRUE on success, FALSE on failure.
     */
    function moveString($p_startPositionOrKey, $p_endPosition) 
    {
    	// Check parameters
    	if (is_numeric($p_startPositionOrKey) && (($p_startPositionOrKey < 0) 
    		|| ($p_startPositionOrKey > count($this->m_translationTable)))) {
    		return false;
    	}
    	if (!is_numeric($p_endPosition) || ($p_endPosition < 0)
    		|| ($p_endPosition > count($this->m_translationTable))) {
    		return false;
    	}
    	$startPosition = null;
    	if (is_numeric($p_startPositionOrKey)) {
			$startPosition = $p_startPositionOrKey;
    	}
    	elseif (is_string($p_startPositionOrKey)) {
    		if (!isset($this->m_translationTable[$p_startPositionOrKey])) {
    			return false;
    		}
    		$startPosition = $this->getPosition($p_startPositionOrKey);
    	}
    	else {
    		return false;
    	}
    	
    	// Success if we dont have to move the string anywhere
		if ($startPosition == $p_endPosition) {
			return true;
		} 	
    	// Delete the string in the old position
    	$result = $this->deleteStringAtPosition($startPosition);
    	if (!$result) {
    		return false;
    	}
    	$key = $result['key'];
    	$value = $result['value'];
    	
    	// Add the string in the new position
    	$result = $this->addString($key, $value, $p_endPosition);
    	if (!$result) {
    		return false;
    	}
    	return true;
    } // fn moveString
    
    
    /**
     * Delete the string given by $p_key.
     * @param string $p_key
     * @return mixed
     *		The deleted string as array('key' => $key, 'value' => $value) on success,
     *		FALSE if it didnt exist.
     */
    function deleteString($p_key) 
    {
    	if (isset($this->m_translationTable[$p_key])) {
    		$value = $this->m_translationTable[$p_key];
    		unset($this->m_translationTable[$p_key]);
    		return array('key'=>$p_key, 'value'=>$value);
    	}
    	return false;
    } // fn deleteString
    
    
    /**
     * Delete a string at a specific position in the array.
     * @param int $p_position
     * @return mixed
     *		The deleted string as array($key, $value) on success, FALSE on failure.
     */
    function deleteStringAtPosition($p_position) 
    {
    	if (!is_numeric($p_position) || ($p_position < 0) 
    		|| ($p_position > count($this->m_translationTable))) {
    		return false;
    	}
    	$returnValue = array_splice($this->m_translationTable, $p_position, 1);
    	$keys = array_keys($returnValue);
    	$key = array_pop($keys);
    	$value = array_pop($returnValue);
    	return array('key' => $key, 'value' => $value);
    } // fn deleteStringAtPosition
    
    
    /**
     * Synchronize the positions of the strings in the translation table
     * with the positions of the string in the default language translation table.
     */
    function fixPositions() 
    {
        global $g_localizerConfig;
        $defaultLanguage =& new LocalizerLanguage($this->m_prefix, 
                                                  $g_localizerConfig['DEFAULT_LANGUAGE']);
        $defaultLanguage->loadFile(Localizer::GetMode());
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
    	$count = 0;
    	$modified = false;
    	foreach ($defaultTranslationTable as $key => $value) {
    		if ($this->getPosition($key) != $count) {
    			$this->moveString($key, $count);
    			$modified = true;
    		}
    		$count++;
    	}
    	return $modified;
    } // fn fixPositions
    
    
    /**
     * Sync with the default language file.  This means
     * adding any missing strings and fixing the positions of the strings to 
     * be the same as the default language file.
     */
    function syncToDefault() 
    {
        global $g_localizerConfig;
        $defaultLanguage =& new LocalizerLanguage($this->m_prefix, 
                                                  $g_localizerConfig['DEFAULT_LANGUAGE']);
        $defaultLanguage->loadFile(Localizer::GetMode());
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
    	$count = 0;
    	$modified = false;
    	foreach ($defaultTranslationTable as $key => $value) { 
    		if (!isset($this->m_translationTable[$key])) {
    			$this->addString($key, '', $count);
    			$modified = true;
    		}
    		$count++;
    	} 
        if ($g_localizerConfig['DELETE_UNUSED_ON_SYNC'] === true) {
        	foreach ($this->m_translationTable as $key => $value) { 
        		if (!isset($defaultTranslationTable[$key])) {
        			$this->deleteString($key, '', $count);
        			$modified = true;
        		}
        		$count++;
        	} 
        }
        
    	return ($this->fixPositions() || $modified);
    } // fn syncToDefault
    
    
    /**
     * Find the keys/values that match the given keyword.
     *
     * @param string $p_keyword
     *
     * @return array
     */
    function search($p_keyword) 
    {
    	$matches = array();
    	foreach ($this->m_translationTable as $key => $value) {
    		if (empty($p_keyword) || stristr($key, $p_keyword) || stristr($value, $p_keyword)) {
    			$matches[$key] = $value;
    		}
    	}
    	return $matches;
    } // fn search
    
    
    /**
     * Load a language file of the given type.
     *
     * @param string $p_type
     *		If not specified, it will use the current mode.
     *
     * @return boolean
     */
    function loadFile($p_type = null) 
    {
        if (is_null($p_type)) {
            if (!empty($this->m_mode)) {
                $p_type = $this->m_mode;
            }
            else {
        		$p_type = Localizer::GetMode();
        		if (is_null($p_type)) {
        		    return false;
        		}
            }
        }
        $className = 'LocalizerFileFormat_'.strtoupper($p_type);
        if (class_exists($className)) {
            $object =& new $className();
            if (method_exists($object, 'load')) {
                return $object->load($this);
            }
        }
    	return false;
    } // fn loadFile
    
    
    /**
     * Save the translation table as the given type.
     *
     * @param string $p_type
     *		If not specified, it will use the current mode.
     *
     * @return boolean
     */
    function saveFile($p_type = null) 
    {
        // Figure out the current mode.
        if (is_null($p_type)) {
            if (!empty($this->m_mode)) {
                $p_type = $this->m_mode;
            }
            else {
        		$p_type = Localizer::GetMode();
        		if (is_null($p_type)) {
        		    return false;
        		}
            }
        }
        // Save in the requested mode.
        $className = 'LocalizerFileFormat_'.strtoupper($p_type);
        if (class_exists($className)) {
            $object =& new $className();
            if (method_exists($object, 'save')) {
                return $object->save($this);
            }
        }
    	return false;
    } // fn saveFile
    
    
    /**
     * Erase all the values in the translation table, but 
     * keep the keys.
     * @return void
     */
    function clearValues() 
    {
    	foreach ($this->m_translationTable as $key => $value) {
    		$this->m_translationTable[$key] = '';
    	}
    } // fn clearValues
    
    
    /**
     * For debugging purposes, displays the the translation table 
     * in an HTML table.
     */
    function dumpToHtml() 
    {
    	echo "<pre>";
    	if (!empty($this->m_filePath)) {
    		echo "<b>File: ".$this->m_filePath."</b><br>";
    	}
    	echo "<b>Language Code: ".$this->m_languageId."</b><br>";
    	echo "<table>";
    	foreach ($this->m_translationTable as $key => $value) {
    		echo "<tr><td>'$key'</td><td>'$value'</td></tr>";
    	}
    	echo "</table>";
    	echo "</pre>";
    } // fn dumpToHtml
    
} // class LocalizerLanguage

?>