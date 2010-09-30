<?PHP
require_once dirname(__FILE__).'/LocalizerConfig.php';
require_once dirname(__FILE__).'/Localizer.php';
require_once dirname(__FILE__).'/LocalizerLanguage.php';

class LanguageMetadata {
	var $m_languageDefs = null;
	var $m_languageId = '';
	var $m_englishName = '';
	var $m_nativeName = '';
	var $m_languageCode = '';
	var $m_countryCode = '';
	
	function LanguageMetadata() 
	{
	} // constructor
	
	
	/**
	 * The unique ID of the language in the form <Two Letter Language Code>_<Two Letter Country Code>.
	 * For example, english is "en_US".
	 * @return string
	 */
	function getLanguageId() 
	{
		return $this->m_languageId;
	} // fn getLanguageId
	
	
	/**
	 * Return the english name of this language.
	 * @return string
	 */
	function getEnglishName() 
	{
		return $this->m_englishName;
	} // fn getEnglishName
	
	
	/**
	 * Return the name of the language as written in the language itself.
	 * @return string
	 */
	function getNativeName() 
	{
		return $this->m_nativeName;
	} // fn getNativeName
	
	
	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	function getLanguageCode() 
	{
		return $this->m_languageCode;
	} // fn getLanguageCode

	
	/**
	 * Get the two-letter code for the country.
	 * @return string
	 */
	function getCountryCode() 
	{
		return $this->m_countryCode;
	} // fn getCountryCode
		
} // class LanguageMetadata
?>