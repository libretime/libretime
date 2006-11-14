<?php
/**
 * @package Campware
 * This file would normally be split into multiple files but since it must
 * be fast (it gets loaded for every hit to the admin screen), we put it
 * all in one file.
 */

/**
 * Includes
 */
require_once 'File.php';
require_once 'File/Find.php';
require_once  dirname(__FILE__).'/LocalizerConfig.php';
require_once  dirname(__FILE__).'/LocalizerLanguage.php';
require_once  dirname(__FILE__).'/LanguageMetadata.php';

/**
 * Translate the given string and print it.  This function accepts a variable
 * number of parameters and works something like printf().
 *
 * @param string $p_translateString
 *		The string to translate.
 *
 * @return void
 */
function putGS($p_translateString)
{
	$args = func_get_args();
	echo call_user_func_array('getGS', $args);
} // fn putGS


/**
 * Translate the given string and return it.  This function accepts a variable
 * number of parameters and works something like printf().
 *
 * @param string $p_translateString -
 *		The string to translate.
 *
 * @return string
 */
function getGS($p_translateString)
{
	global $g_translationStrings, $TOL_Language;
	$numFunctionArgs = func_num_args();
	if (!isset($g_translationStrings[$p_translateString]) || ($g_translationStrings[$p_translateString]=='')) {
		$translatedString = "$p_translateString (*)";
	}
	else {
		$translatedString = $g_translationStrings[$p_translateString];
	}
	if ($numFunctionArgs > 1) {
		for ($i = 1; $i < $numFunctionArgs; $i++){
			$name = '$'.$i;
			$val = func_get_arg($i);
			$translatedString = str_replace($name, $val, $translatedString);
		}
	}
	return $translatedString;
} // fn getGS


/**
 * Register a string in the global translation file. (Legacy code for GS files)
 *
 * @param string $p_value
 * @param string $p_key
 * @return void
 */
function regGS($p_key, $p_value)
{
	global $g_translationStrings;
	if (isset($g_translationStrings[$p_key])) {
		if ($p_key!='') {
			print "The global string is already set in ".$_SERVER[PHP_SELF].": $key<BR>";
		}
	}
	else{
		if (substr($p_value, strlen($p_value)-3)==(":".$_REQUEST["TOL_Language"])){
			$p_value = substr($p_value, 0, strlen($p_value)-3);
		}
		$g_translationStrings[$p_key] = $p_value;
	}
} // fn regGS


/**
 * The Localizer class handles groups of translation tables (LocalizerLanguages).
 * This class simply acts as a namespace for a group of static methods.
 * @package Campware
 */
class Localizer {

    /**
     * Return the type of files we are currently using, currently
     * either 'gs' or 'xml'.  If not set in the config file, we will
     * do our best to figure out the current mode.
     *
     * @return mixed
     *		Will return 'gs' or 'xml' on success, or NULL on failure.
     */
    function GetMode()
    {
        global $g_localizerConfig;
    	if ($g_localizerConfig['DEFAULT_FILE_TYPE'] != '') {
    		return $g_localizerConfig['DEFAULT_FILE_TYPE'];
    	}
	    $defaultLang = new LocalizerLanguage('globals',
	                                          $g_localizerConfig['DEFAULT_LANGUAGE']);
	    if ($defaultLang->loadGsFile()) {
	    	return 'gs';
	    }
	    elseif ($defaultLang->loadXmlFile()) {
	    	return 'xml';
	    }
	    else {
	    	return null;
	    }
    } // fn GetMode


    /**
     * Load the translation strings into a global variable and return them.
     *
     * @param string $p_prefix -
     *      Beginning of the file name, before the ".php" extension.
     * @param string $p_languageCode
     *      id of language to load
     * @param bool $p_return
     *      return translation array
     *
     * @return void
     */
	function LoadLanguageFiles($p_prefix, $p_languageCode = null, $p_return = false)
	{
	    global $g_localizerConfig;

	    if ($p_return) {
	       static $g_translationStrings;
	    } else {
	       global $g_translationStrings;
	    }

	    if (is_null($p_languageCode)){
	        $p_languageCode = $g_localizerConfig['DEFAULT_LANGUAGE'];
	    }

	    if (!isset($g_translationStrings)) {
    		$g_translationStrings = array();
        }

        $key = $p_prefix."_".$g_localizerConfig['DEFAULT_LANGUAGE'];
	    if (!isset($g_localizerConfig['LOADED_FILES'][$key])) {
	        $defaultLang = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
    	    $defaultLang->loadFile(Localizer::GetMode());
    	    $defaultLangStrings = $defaultLang->getTranslationTable();
    	    // Merge default language strings into the translation array.
    	    #$g_translationStrings = array_merge($g_translationStrings, $defaultLangStrings);
    	    $g_translationStrings = Localizer::_arrayValuesMerge($g_translationStrings, $defaultLangStrings);
	        $g_localizerConfig['LOADED_FILES'][$key] = true;
	    }
	    $key = $p_prefix."_".$p_languageCode;
	    if (!isset($g_localizerConfig['LOADED_FILES'][$key])) {
    	    $userLang = new LocalizerLanguage($p_prefix, $p_languageCode);
    	    $userLang->loadFile(Localizer::GetMode());
    	    $userLangStrings = $userLang->getTranslationTable();
    	    // Merge user strings into translation array.
    	    #$g_translationStrings = array_merge($g_translationStrings, $userLangStrings);
    	    $g_translationStrings = Localizer::_arrayValuesMerge($g_translationStrings, $userLangStrings);
	        $g_localizerConfig['LOADED_FILES'][$key] = true;
	    }

	    if ($p_return) {
	       return $g_translationStrings;
	    }
	} // fn LoadLanguageFiles

	function _arrayValuesMerge($arr1, $arr2)
	{
	   foreach ($arr2 as $k=>$v) {
	       if (strlen($v)) {
	           $arr1[$k] = $v;
	       }
	   }

	   return $arr1;
	}

    /**
     * Compare a particular language's keys with the default language set.
     *
     * @param string $p_prefix -
     *		The prefix of the language files.
     *
     * @param array $p_data -
     *		A set of keys.
     *
     * @param boolean $p_findExistingKeys -
     *		Set this to true to return the set of keys (of the keys given) that already exist,
     *		set this to false to return the set of keys (of the keys given) that do not exist.
     *
     * @return array
     */
    function CompareKeys($p_prefix, $p_data, $p_findExistingKeys = true)
    {
        global $g_localizerConfig;
		$localData = new LocalizerLanguage($p_prefix,
		                                    $g_localizerConfig['DEFAULT_LANGUAGE']);
		$localData->loadFile(Localizer::GetMode());
        $globaldata = new LocalizerLanguage($g_localizerConfig['FILENAME_PREFIX_GLOBAL'],
                                             $g_localizerConfig['DEFAULT_LANGUAGE']);
        $globaldata->loadFile(Localizer::GetMode());

        $returnValue = array();
        foreach ($p_data as $key) {
        	$globalKeyExists = $globaldata->keyExists($key);
        	$localKeyExists = $localData->keyExists($key);
        	if ($p_findExistingKeys && ($globalKeyExists || $localKeyExists)) {
                $returnValue[$key] = $key;
            }
            elseif (!$p_findExistingKeys && !$globalKeyExists && !$localKeyExists) {
            	$returnValue[$key] = $key;
            }
        }

        return $returnValue;
    } // fn CompareKeys


    /**
     * Search through PHP files and find all the strings that need to be translated.
     * @param string $p_directory -
     * @return array
     */
    function FindTranslationStrings($p_prefix, $p_depth=0)
    {
        global $g_localizerConfig;

        //Start search here
        $dir   = $g_localizerConfig["mapPrefixToDir"][$p_prefix]['path'];
        $depth = $g_localizerConfig["mapPrefixToDir"][$p_prefix]['depth'];

        // Scan which files
        $filePatterns    = $g_localizerConfig['mapPrefixToDir'][$p_prefix]['filePatterns'];
        $execludePattern = $g_localizerConfig['mapPrefixToDir'][$p_prefix]['execlPattern'];
        if ($g_localizerConfig['DEBUG']) echo "<br>Scan files match ".print_r($filePatterns, 1);

        // Scan for what
        $funcPatterns = $g_localizerConfig['mapPrefixToDir'][$p_prefix]['funcPatterns'];
        if ($g_localizerConfig['DEBUG']) echo "<br>Scan for ".print_r($funcPatterns, 1);
        // Get all files in this directory
        if ($g_localizerConfig['DEBUG']) echo "<br>Search in: {$g_localizerConfig['BASE_DIR']}$dir Depth: {$depth}";
        $files = File_Find::mapTreeMultiple($g_localizerConfig['BASE_DIR'].$dir, $depth);
        $files = Localizer::_flatFileList($files);
        #print_r($files);

        // Get all the Matching files
        foreach ($filePatterns as $fp) {
            foreach ($files as $name) {
                if (preg_match($fp, $name) && !preg_match($execludePattern, $name)) {
                    $filelist[] = $name;
                }
            }
            reset($files);
        }
        #print_r($filelist);

		// Read in all the PHP files.
		$data = array();
        foreach ($filelist as $name) {
            $data = array_merge($data, file($g_localizerConfig['BASE_DIR'].$dir.'/'.$name));
        }
        #print_r($data);

       	// Collect all matches
       	$matches = array();

      	foreach ($data as $line) {
            foreach ($funcPatterns as $fp => $pos) {
                if (preg_match_all($fp, $line, $m)) {
                    foreach ($m[$pos] as $match) {
                        #$match = str_replace("\\\\", "\\", $match);
                        if (strlen($match)) $matches[$match] = $match;
                    }
                }
            }
            reset($funcPatterns);
        }
        asort($matches);
        #print_r($matches);

        return $matches;
    } // fn FindTranslationStrings


    function _flatFileList($files, $appdir='', $init=TRUE)
    {
        static $_flatList;
        if ($init === TRUE) $_flatList = array();

        foreach ($files as $dir => $name) {
            if (is_array($name)) {
                Localizer::_flatFileList($name, $appdir.'/'.$dir, FALSE);
            } else {
                $_flatList[] = $appdir.'/'.$name;
            }
        }
        return $_flatList;
    }


    /**
     * Return the set of strings in the code that are not in the translation files.
     * @param string $p_directory -
     * @return array
     */
    function FindMissingStrings($p_prefix)
    {
        global $g_localizerConfig;

        if (empty($p_prefix)) {
            return array();
        }

	    $newKeys     =& Localizer::FindTranslationStrings($p_prefix);
	    $missingKeys =& Localizer::CompareKeys($p_prefix, $newKeys, false);
	    $missingKeys =  array_unique($missingKeys);
	    return $missingKeys;
    } // fn FindMissingStrings


    /**
     * Return the set of strings in the translation files that are not used in the code.
     * @param string $p_prefix
     * @param string $p_directory -
     * @return array
     */
    function FindUnusedStrings($p_prefix)
    {
        global $g_localizerConfig;

        if (empty($p_prefix)) {
            return array();
        }

	    $existingKeys =& Localizer::FindTranslationStrings($p_prefix);
		$localData = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
		$localData->loadFile(Localizer::GetMode());
		$localTable = $localData->getTranslationTable();
		$unusedKeys = array();
		foreach ($localTable as $key => $value) {
			if (!in_array($key, $existingKeys)) {
				$unusedKeys[$key] = $key;
			}
		}
	    $unusedKeys = array_unique($unusedKeys);
	    return $unusedKeys;
    } // fn FindUnusedStrings


    /**
     * Update a set of strings in a language file.
     * @param string $p_prefix
     * @param string $p_languageCode
     * @param array $p_data
     *
     * @return void
     */
    function ModifyStrings($p_prefix, $p_languageId, $p_data)
    {
        global $g_localizerConfig;
      	// If we change a string in the default language,
      	// then all the language files must be updated with the new key.
        if ($p_languageId == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        $languages = Localizer::GetAllLanguages();
	        foreach ($languages as $language) {

	        	// Load the language file
	        	$source = new LocalizerLanguage($p_prefix, $language->getLanguageId());
	        	$source->loadFile(Localizer::GetMode());

	        	// For the default language, we set the key & value to be the same.
	        	if ($p_languageId == $language->getLanguageId()) {
	        		foreach ($p_data as $pair) {
	        			$source->updateString($pair['key'], $pair['value'], $pair['value']);
	        		}
	        	}
	        	// For all other languages, we just change the key and keep the old value.
	        	else {
	        		foreach ($p_data as $pair) {
	        			$source->updateString($pair['key'], $pair['value']);
	        		}
	        	}

	        	// Save the file
				$source->saveFile(Localizer::GetMode());
	        }
        }
      	// We only need to change the values in one file.
        else {
        	// Load the language file
        	$source = new LocalizerLanguage($p_prefix, $p_languageId);
        	$source->loadFile(Localizer::GetMode());
    		foreach ($p_data as $pair) {
    			$source->updateString($pair['key'], $pair['key'], $pair['value']);
    		}
        	// Save the file
			$source->saveFile(Localizer::GetMode());
        }
    } // fn ModifyStrings


    /**
     * Synchronize the positions of the strings to the default language file order.
     * @param string $p_prefix
     * @return void
     */
    function FixPositions($p_prefix)
    {
        global $g_localizerConfig;
        $defaultLanguage = new LocalizerLanguage($p_prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);
        $defaultLanguage->loadFile(Localizer::GetMode());
        $defaultTranslationTable = $defaultLanguage->getTranslationTable();
        $languageIds = Localizer::GetAllLanguages();
        foreach ($languageIds as $languageId) {

        	// Load the language file
        	$source = new LocalizerLanguage($p_prefix, $languageId);
        	$source->loadFile(Localizer::GetMode());

        	$count = 0;
        	foreach ($defaultTranslationTable as $key => $value) {
        		$source->moveString($key, $count);
        		$count++;
        	}

        	// Save the file
			$source->saveFile(Localizer::GetMode());
        }
    } // fn FixPositions


    /**
     * Go through all files matching $p_prefix in $p_directory and add entry(s).
     *
     * @param string $p_prefix
     * @param int $p_position
     * @param array $p_newKey
     *
     * @return void
     */
    function AddStringAtPosition($p_prefix, $p_position, $p_newKey)
    {
        global $g_localizerConfig;
        $languages = Localizer::GetAllLanguages();
        foreach ($languages as $language) {
        	$source = new LocalizerLanguage($p_prefix, $language->getLanguageId());
        	$source->loadFile(Localizer::GetMode());
        	if (is_array($p_newKey)) {
        		foreach (array_reverse($p_newKey) as $key) {
        			if ($language->getLanguageId() == $g_localizerConfig['DEFAULT_LANGUAGE']) {
        				$source->addString($key, $key, $p_position);
        			}
        			else {
        				$source->addString($key, '', $p_position);
        			}
        		}
        	}
        	else {
       			if ($Id == $g_localizerConfig['DEFAULT_LANGUAGE']) {
	        		$source->addString($p_newKey, $p_newKey, $p_position);
       			}
       			else {
	        		$source->addString($p_newKey, '', $p_position);
       			}
        	}
			$source->saveFile(Localizer::GetMode());
        }
    } // fn AddStringAtPosition


    /**
     * Go through all files matching $p_prefix remove selected entry.
     * @param string $p_prefix
     * @param mixed $p_key -
     *		Can be a string or an array of strings.
     * @return void
     */
    function RemoveString($p_prefix, $p_key)
    {
        $languages = Localizer::GetAllLanguages();

        foreach ($languages as $language) {
        	$target = new LocalizerLanguage($p_prefix, $language->getLanguageId());
        	$target->loadFile(Localizer::GetMode());
        	if (is_array($p_key)) {
        		foreach ($p_key as $key) {
        			$target->deleteString($key);
        		}
        	}
        	else {
        		$target->deleteString($p_key);
        	}
			$target->saveFile(Localizer::GetMode());
        }
    } // fn RemoveString


    /**
     * Go through all files matching $p_prefix swap selected entrys.
     *
     * @param string $p_prefix
     * @param int $p_pos1
     * @param int $p_pos2
     *
     * @return void
     */
    function MoveString($p_prefix, $p_pos1, $p_pos2)
    {
        $languages = Localizer::GetAllLanguages();
        foreach ($languages as $language) {
			$target = new LocalizerLanguage($p_prefix, $language->getLanguageId());
			$target->loadFile(Localizer::GetMode());
			$success = $target->moveString($p_pos1, $p_pos2);
			$target->saveFile(Localizer::GetMode());
        }
    } // fn MoveString


   	/**
     * Get all the languages that the interface supports.
     *
     * When in PHP mode, it will get the list from the database.
     * When in XML mode, it will first try to look in the languages.xml file located
     * in the current directory, and if it doesnt find that, it will look at the file names
     * in the top directory and deduce the languages from that.
     *
     * @param string $p_mode
     * @return array
     *		An array of LanguageMetadata objects.
     */
    function GetAllLanguages($p_mode = null, $p_default=TRUE, $p_completed_only=FALSE)
    {
		if (is_null($p_mode)) {
			$p_mode = Localizer::GetMode();
		}
		$className = "LocalizerFileFormat_".strtoupper($p_mode);
		if (class_exists($className)) {
		    $object = new $className();
		    if (method_exists($object, "getLanguages")) {
		        $languages = $object->getLanguages($p_default, $p_completed_only);
		    }
		}
        //$this->m_languageDefs =& $languages;
    	return $languages;
    } // fn GetAllLanguages


    /**
     * Get a list of all files matching the pattern given.
     * Return an array of strings, each the full path name of a file.
     * @param string $p_startdir
     * @param string $p_pattern
     * @return array
     */
    function SearchFilesRecursive($p_startdir, $p_pattern)
    {
        $structure = File_Find::mapTreeMultiple($p_startdir);

        // Transform it into a flat structure.
        $filelist = array();
        foreach ($structure as $dir => $file) {
        	// it's a directory
            if (is_array($file)) {
                $filelist = array_merge($filelist,
                    Localizer::SearchFilesRecursive($p_startdir.'/'.$dir, $p_pattern));
            }
            else {
            	// it's a file
                if (preg_match($p_pattern, $file)) {
                    $filelist[] = $p_startdir.'/'.$file;
                }
            }
        }
        return $filelist;
    } // fn SearchFilesRecursive


	/**
     * Create a new directory and make a copy of current default language files.
     * @param string $p_languageId
     * @return void
     */
    function CreateLanguageFiles($p_languageId)
    {
        global $g_localizerConfig;

        // Make new directory
        if (!mkdir($g_localizerConfig['TRANSLATION_DIR']."/".$p_languageId)) {
            return;
        }

        // Copy files from reference language

//        $className = "LocalizerFileFormat_".strtoupper(Localizer::GetMode());
//        foreach ($files as $pathname) {
//            if ($pathname) {
//                $fileNameParts = explode('.', basename($pathname));
//                $base = $fileNameParts[0];
//                $dir = str_replace($g_localizerConfig['BASE_DIR'], '', dirname($pathname));
//                // read the default file
//                $defaultLang = new LocalizerLanguage($base, $g_localizerConfig['DEFAULT_LANGUAGE']);
//                $defaultLang->loadFile(Localizer::GetMode());
//                $defaultLang->clearValues();
//                $defaultLang->setLanguageId($p_languageId);
//                // if file already exists -> skip
//                if (!file_exists($defaultLang->getFilePath())) {
//	                $defaultLang->saveFile(Localizer::GetMode());
//                }
//            }
//        }
    } // fn CreateLanguageFiles


    /**
     * Go through subdirectorys and delete language files for given Id.
     * @param string $p_languageId
     * @return void
     */
    function DeleteLanguageFiles($p_languageId)
    {
        global $g_localizerConfig;
        $langDir = $g_localizerConfig['TRANSLATION_DIR'].'/'.$p_languageId;
        if (!file_exists($langDir)) {
            return;
        }
        $files = File_Find::mapTreeMultiple($langDir, 1);
        //echo "<pre>";print_r($files);echo "</pre>";
        foreach ($files as $pathname) {
            if (file_exists($pathname)) {
                echo 'deleteing '.$pathname.'<br>';
                //unlink($pathname);
            }
        }
    } // fn DeleteLanguageFiles

} // class Localizer
?>