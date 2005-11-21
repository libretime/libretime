<?PHP
/**
 * @package Campware
 */

/**
 * Abstract interface for the localizer to access data from different sources.
 * @package Campware
 * @abstract 
 */
class LocalizerFileFormat {
	function load(&$p_localizerLanguage) { }	
	function save(&$p_localizerLanguage) { }
	function getFilePath($p_localizerLanguage) { }
} // class LocalizerFileFormat


/**
 * @package Campware
 */
class LocalizerFileFormat_GS extends LocalizerFileFormat {
    /**
     * Load the translation table from a PHP-GS file.
     *
     * @param LocalizerLanguage $p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return boolean
     */
	function load(&$p_localizerLanguage) 
	{
	    global $g_localizerConfig;
    	$p_localizerLanguage->setMode('gs');
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);     
        //echo "Loading $filePath<br>";
        if (file_exists($filePath)) {
	        $lines = file($filePath);
	        foreach ($lines as $line) {
	        	if (strstr($line, "regGS")) {
			        $line = preg_replace('/regGS/', '$p_localizerLanguage->registerString', $line);			    
	        		$success = eval($line);
	        		if ($success === FALSE) {
	        			echo "Error evaluating: ".htmlspecialchars($line)."<br>";
	        		}
	        	}
	        }
	        return true;
        }
        else {
        	return false;
        }	
	} // fn load
	    
	
    /**
     * Save the translation table to a PHP-GS file.
     *
     * @param LocalizerLanguage $p_localizerLanguage
     *		LocalizerLanguage object.
     *
     * @return string
     *		File contents
     */
	function save(&$p_localizerLanguage) 
	{
	    global $g_localizerConfig;
    	$data = "<?php\n";
    	$translationTable = $p_localizerLanguage->getTranslationTable();
    	foreach ($translationTable as $key => $value) {
    	    // Escape quote characters.
    	    $key = str_replace('"', '\"', $key);
    	    $value = str_replace('"', '\"', $value);
    		$data .= "regGS(\"$key\", \"$value\");\n";
    	}
    	$data .= "?>";
        $filePath = LocalizerFileFormat_GS::GetFilePath($p_localizerLanguage);
        //echo $filePath;
        $p_localizerLanguage->_setSourceFile($filePath);
        
        // Create the language directory if it doesnt exist.
        $dirName = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_localizerLanguage->getLanguageCode();
        if (!file_exists($dirName)) {
            mkdir($dirName);
        }
        
        // write data to file        
        if (PEAR::isError(File::write($filePath, $data, FILE_MODE_WRITE))) {
        	echo "<br>error writing file<br>";
            return FALSE;
        }
        File::close($filePath, FILE_MODE_WRITE);
        return $data;    	
    } // fn save
    
	
	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage $p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage) 
	{
	    global $g_localizerConfig;
       	return $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_localizerLanguage->getLanguageCode()
       	    .'/'.$p_localizerLanguage->getPrefix().'.php';	    
	} // fn getFilePath
	
	
	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 *     An array of LanguageMetadata
	 */
	function getLanguages() 
	{
	    /*
    	global $Campsite;
        $query = 'SELECT  Name, OrigName AS NativeName, Code as LanguageCode, Code AS Id
                    FROM Languages
                    ORDER BY Name';
        $languages = $Campsite['db']->getAll($query);
        if (!$languages) {
        	//echo 'Cannot read database campsite.Languages<br>';
        	return array();
        }
        */
	    global $languages;
	    
        $metadata = array();
        foreach ($languages as $language) {
            $tmpMetadata =& new LanguageMetadata();
            $tmpMetadata->m_englishName     = $language['Name'];
            $tmpMetadata->m_nativeName      = $language['NativeName'];
            $tmpMetadata->m_languageCode    = $language['LanguageCode'];
            $tmpMetadata->m_countryCode     = '';
            $tmpMetadata->m_languageId      = $language['LanguageCode'];
            $metadata[] = $tmpMetadata;
        }
        return $metadata;
	} // fn getLanguages
	
} // class LocalizerFileFormat_GS


/**
 * @package Campware
 */
class LocalizerFileFormat_XML extends LocalizerFileFormat {
    var $m_serializeOptions     = array();
    var $m_unserializeOptions   = array();
    var $l_serializeOptions     = array();
    var $l_unserializeOptions   = array();
    
    function LocalizerFileFormat_XML() 
    {
        global $g_localizerConfig;
        $this->m_serializeOptions = array(
				// indent with tabs
           	"indent"           => "\t",       
           	// root tag
           	"rootName"         => "translations",  
           	// tag for values with numeric keys 
           	"defaultTagName"   => "item", 
           	"keyAttribute"     => "position",
           	"addDecl"          => true,
           	"encoding"         => $g_localizerConfig['FILE_ENCODING'],
           	"indentAttributes" => true
		);  
        $this->l_serializeOptions = array(
				// indent with tabs
           	"indent"           => "\t",       
           	// root tag
           	"rootName"         => "languages",  
           	// tag for values with numeric keys 
           	"defaultTagName"   => "item", 
           	"keyAttribute"     => "position",
           	"addDecl"          => true,
           	"encoding"         => $g_localizerConfig['FILE_ENCODING'],
           	"indentAttributes" => true
		);        
    }
    
    
    /**
     * Read an XML-format translation file into the translation table.
     * @param LocalizerLanguage $p_localizerLanguage
     * @return boolean
     */
	function load(&$p_localizerLanguage) 
	{
	    global $g_localizerConfig;
	    
    	$p_localizerLanguage->setMode('xml');
        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        if (file_exists($filePath)) {
            $xml = File::readAll($filePath);
            File::close($filePath, FILE_MODE_READ);
	        $unserializer =& new XML_Unserializer($this->m_unserializeOptions);
	        $unserializer->unserialize($xml);
	        $translationArray = $unserializer->getUnserializedData();

	        if ($g_localizerConfig['ORDER_KEYS']) $translationArray['item'] = $this->_xSortArray($translationArray['item'], 'key');
	            	
	        $p_localizerLanguage->clearValues();
	        if (isset($translationArray['item'])) {
		        foreach ($translationArray['item'] as $translationPair) {
		        	$p_localizerLanguage->registerString($translationPair['key'], $translationPair['value']);
		        }
	        }
	        return true;
        }    	
        else {
        	return false;
        }
	} // fn load
	
	
    /**
     * Write a XML-format translation file.
     * @param LocalizerLanguage $p_localizerLanguage
     * @return mixed
     *      The XML that was written on success,
     *      FALSE on error.
     */
	function save(&$p_localizerLanguage) 
	{
    	$saveData = array();
    	$saveData["Id"] = $p_localizerLanguage->getLanguageId();
    	$origTranslationTable =& $p_localizerLanguage->getTranslationTable();
		$saveTranslationTable = array();
		foreach ($origTranslationTable as $key => $value) {
			$saveTranslationTable[] = array('key' => $key, 'value' => $value);
		}
    	$saveData = array_merge($saveData, $saveTranslationTable);
    	
        $serializer =& new XML_Serializer($this->m_serializeOptions);
        $serializer->serialize($saveData);
        $xml = $serializer->getSerializedData();
        
        if (PEAR::isError($xml)) {
        	echo "<br>error serializing data<br>";
            return FALSE;
        }
        
        $filePath = LocalizerFileFormat_XML::GetFilePath($p_localizerLanguage);
        //echo "Saving as ".$this->m_filePath."<Br>";
        // write data to file        
        if (PEAR::isError(File::write($filePath, $xml, FILE_MODE_WRITE))) {
        	echo "<br>error writing file $filePath<br>";
            return FALSE;
        }        
        
        File::close($filePath, FILE_MODE_WRITE);
        
        return $xml;		
	} // fn save
	
	
	/**
	 * Get the full path to the translation file.
	 * @param LocalizerLanguage $p_localizerLanguage
	 * @return string
	 */
	function getFilePath($p_localizerLanguage) 
	{
	    global $g_localizerConfig;
       	return $g_localizerConfig['TRANSLATION_DIR'].'/'
       	    .$p_localizerLanguage->getLanguageCode()
       	    .'_'.$p_localizerLanguage->getCountryCode()
       	    .'/'.$p_localizerLanguage->getPrefix().'.xml';
	} // fn getFilePath

	
	/**
	 * Get all supported languages as an array of LanguageMetadata objects.
	 * @return array
	 */
	function getLanguages($p_default=TRUE) 
	{
	    global $g_localizerConfig; 
	    
	    $fileName = $g_localizerConfig['TRANSLATION_DIR']
	               .$g_localizerConfig['LANGUAGE_METADATA_FILENAME'];
	               
    	if (!file_exists($fileName)) { 
    	   echo "Connot read ".$g_localizerConfig['LANGUAGE_METADATA_FILENAME'];   
    	   return FALSE; 
    	}
    	
		$xml = File::readAll($fileName);
		File::rewind($fileName, FILE_MODE_READ);                
		$handle =& new XML_Unserializer($this->l_unserializeOptions);
    	$handle->unserialize($xml);
    	$arr = $handle->getUnserializedData();
    	
    	if (array_key_exists(0, $arr['item'])) {
            $languages = $arr['item'];
    	} else {
    	   $languages[0] = $arr['item'];    
    	}
        foreach ($languages as $language) {
            // just display default language in maintainance mode
            if ($p_default || $language['Id'] !== $g_localizerConfig['DEFAULT_LANGUAGE']) {
                list ($langCode, $countryCode) = explode('_', $language['Id']);
                $languageDef =& new LanguageMetadata();
                $languageDef->m_languageId      = $language['Id'];
                $languageDef->m_languageCode    = $langCode;
                $languageDef->m_countryCode     = $countryCode;
                $languageDef->m_englishName     = $language['Name'];
                $languageDef->m_nativeName      = $language['NativeName'];
                $return[] = $languageDef;
            }
        }
        return $return;
	} // fn getLanguages
	
	
	function addLanguage($new)
	{
	    global $g_localizerConfig; 
	    $fileName = $g_localizerConfig['TRANSLATION_DIR']
	               .$g_localizerConfig['LANGUAGE_METADATA_FILENAME'];	
	                   
    	if (!file_exists($fileName)) { 
    	    echo "$fileName not found";
    	    return FALSE;    
    	}
    	
		$xml = File::readAll($fileName);
		File::rewind($fileName, FILE_MODE_READ);                
		$handle =& new XML_Unserializer($this->l_unserializeOptions);
    	$handle->unserialize($xml);	
    	$arr = $handle->getUnserializedData();
    	
    	if (array_key_exists(0, $arr['item'])) {
            $languages = $arr['item'];
    	} else {
    	   $languages[0] = $arr['item'];    
    	}
    	
    	$languages[] = array(
    	   'Id'            => $new['Id'],
           'Name'          => $new['Name'],
           'NativeName'    => $new['NativeName']
        );
        $languages = $this->_xSortArray($languages, 'Id');
    	$handle =& new XML_Serializer($this->l_serializeOptions);  
    	$handle->serialize($languages);
    	
    	if (!$xml = $handle->getSerializedData()) {
    	    echo "Cannot serialize date";
    	    return FALSE;    
    	}
    	
    	if (!File::write($fileName, $xml, FILE_MODE_WRITE)) {
    	    echo "Cannot add langauge to file $fileName";
    	    return FALSE;    
    	}
    	
    	// create the path/file to stor translations in
    	if (!mkdir($g_localizerConfig['TRANSLATION_DIR'].'/'.$new['Id'])) {
    	    echo "Cannot create path ".$g_localizerConfig['TRANSLATION_DIR'].'/'.$new['Id'];
    	    return FALSE;   
    	}
    	
    	return TRUE;  
	}
	
	function _xSortArray($array, $key)
	{
	   if (!is_array($array) || !count($array)) {
	       return;    
	   }
	   
	   foreach($array as $k=>$v) {
	       $trans[$v[$key]] = $v;    
	   } 
	   
	   ksort($trans);

	   foreach ($trans as $v) {
	       $ret[] = $v;   
	   } 
	   
	   return $ret;  
	}
	
} // class LocalizerFileFormat_XML
?>