<?php
/**
 * @package Campsite
 */

/**
 * includes
 */
#require_once dirname(__FILE__).'/common.php';
#require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/lib_campsite.php");

/**
 * Display the copyright notice and close the HTML page.
 */
function camp_html_copyright_notice($p_displayBorder = true)
{
	global $Campsite;
	if ($p_displayBorder) {
	?>
	<table width="100%" align="center" style="border-top: 1px solid black; margin-top: 15px;">
	<?php
	} else {
	?>
	<table width="100%" align="center" style="margin-top: 5px;">
	<?php
	}
	?>
	<tr>
		<td style="padding-left: 5px; padding-top: 10px;" align="center">
			<a style="font-size:8pt; color: black;" href="http://www.campware.org" target="campware">
			Campsite <?php echo $Campsite['VERSION'] ?> &copy 1999-2005 MDLF, 
			maintained and distributed under GNU GPL by CAMPWARE
			</a>
		</td>
	</tr>
	</table>
	<?php
} // fn camp_html_copyright_notice


/**
 * Create a HTML SELECT drop down box.
 *
 */
function camp_html_create_select($p_name, $p_options, $p_selected = null, $p_extras ="", $p_valuesIncluded = false) 
{
	?>
	<select name="<?php echo $p_name ?>" <?php echo $p_extras ?>>
	<?php
	foreach ($p_options as $key => $value) {
		if ($p_valuesIncluded) {
			?>
			<option value="<?php echo $key; ?>" <?php if (!is_null($p_selected) && ($p_selected == $key)) { echo "selected"; } ?>><?php echo htmlspecialchars($value); ?></option>
			<?php
		}
		else {
			?>
			<option <?php if (!is_null($p_selected) && ($p_selected == $value)) { echo "selected"; } ?>><?php echo htmlspecialchars($value); ?></option>
			<?php    			
		}
	}
	?>
	</select>
	<?php
} // fn camp_html_create_select


/**
 * Create a HTML HREF link to an article.
 *
 * @param Article $p_articleObj
 *		The article we want to display.
 *
 * @param int $p_sectionLanguageId
 *		The section language ID. 
 *
 * @param string $p_targetFileName
 *		Which file in the "articles" directory to call.
 *
 * @param string $p_backLink
 *		I'm not entirely sure what this is for.  I put it in for backward compatibility.
 */
function camp_html_article_link($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "") 
{
	$str = '<A HREF="'.camp_html_article_url($p_articleObj, $p_interfaceLanguageId, $p_targetFileName, $p_backLink).'">';
	return $str;
} // fn camp_html_article_link


/**
 * Create a link to an article.
 *
 * @param Article $p_articleObj
 *		The article we want to display.
 *
 * @param int $p_interfaceLanguageId
 *		The language ID for the interface language. 
 *
 * @param string $p_targetFileName
 *		Which file in the "articles" directory to call.
 *
 * @param string $p_backLink
 *		A URL to get back to the previous page the user was on.
 *
 * @param string $p_extraParams
 */
function camp_html_article_url($p_articleObj, $p_interfaceLanguageId, $p_targetFileName = "", $p_backLink = "", $p_extraParams = null) 
{
	global $ADMIN;
	$str = "/$ADMIN/articles/".$p_targetFileName
		."?Pub=".$p_articleObj->getPublicationId()
		."&Issue=".$p_articleObj->getIssueId()
		."&Section=".$p_articleObj->getSectionId()
		."&Article=".$p_articleObj->getArticleId()
		."&Language=".$p_interfaceLanguageId
		."&sLanguage=".$p_articleObj->getLanguageId();
	if ($p_backLink != "") { 
		$str .="&Back=".urlencode($p_backLink);
	}
	if (!is_null($p_extraParams)) {
	    $str .= $p_extraParams;
	}
	return $str;
} // fn camp_html_article_url


/**
 * Redirect to the error page and show the given error message.
 * You can also give a back link for the user to go back to when they
 * click OK on that screen.
 *
 * @param mixed $p_errorStr
 *		This can be a string or an array.  An array is for the case when the
 *		error string requires arguments.
 *
 * @param string $p_backLink
 *
 * @return void
 */
function camp_html_display_error($p_errorStr, $p_backLink = null, $popup = false) 
{
	global $ADMIN;
	$script = $popup ? 'ad_popup.php' : 'ad.php';
	$location = "/$ADMIN/$script?ADReason=".urlencode($p_errorStr);
	if (!is_null($p_backLink)) {
		$location .= '&Back='.urlencode($p_backLink);
	}
	header("Location: $location");
	exit;
} // fn camp_html_display_error



/**
 * Common header for all content screens.
 *
 * @param string $p_title
 *		The title of the page.  This should have a translation in the language files.
 *
 * @param array $p_objArray
 *		This represents your current location in the content tree.  This
 * 		can have the following index values, each containing the appropriate object:
 *		'Pub', 'Issue', 'Section', 'Article'
 *
 * @param boolean $p_includeLinks
 *		Whether to include the links underneath the title or not.  Default TRUE.
 *
 * @param boolean $p_fValidate
 *		Whether to include the fValidate javascript files in the HTML header. Default FALSE.
 *
 * @param array $p_extraBreadcrumbs
 *		An array in the form 'text' => 'link' for more breadcrumbs.
 *
 * @return void
 */
function camp_html_content_top($p_title, $p_objArray, $p_includeLinks = true, $p_fValidate = false, $p_extraBreadcrumbs = null) 
{
	global $Campsite;
	global $ADMIN;
	$publicationObj = camp_array_get_value($p_objArray, 'Pub', null);
	$issueObj = camp_array_get_value($p_objArray, 'Issue', null);
	$sectionObj = camp_array_get_value($p_objArray, 'Section', null);
	$articleObj = camp_array_get_value($p_objArray, 'Article', null);
	
	$breadcrumbs = array();
	if (!is_null($publicationObj)) {
	    $prompt =  getGS("Publication").":";
	    //$strCrumbs .= camp_html_breadcrumb($prompt, "/$ADMIN/pub/", )
	    $name = htmlspecialchars($publicationObj->getName());
    	$breadcrumbs[] = array($prompt, "/$ADMIN/pub/", false);
    	$breadcrumbs[] = array($name, "/$ADMIN/pub/edit.php?Pub=".$publicationObj->getPublicationId());
	}
	
	if (!is_null($issueObj)) { 
	    $prompt = getGS("Issue").":";
    	$breadcrumbs[] = array($prompt, 
    	       "/$ADMIN/issues/"
    	       ."?Pub=".$issueObj->getPublicationId()
    	       ."&Issue=".$issueObj->getIssueId()
    	       ."&Language=".$issueObj->getLanguageId(),
    	       false);
	    $name = htmlspecialchars($issueObj->getName())." (".htmlspecialchars($issueObj->getLanguageName()).")";
        $breadcrumbs[] = array($name, 
    	       "/$ADMIN/issues/edit.php"
    	       ."?Pub=".$issueObj->getPublicationId()
    	       ."&Issue=".$issueObj->getIssueId()
    	       ."&Language=".$issueObj->getLanguageId()); 
	}
	if (!is_null($sectionObj)) { 
	    $prompt = getGS("Section").":";
		$breadcrumbs[] = array($prompt, 
		        "/$ADMIN/sections/"
		        ."?Pub=".$sectionObj->getPublicationId()
                ."&Issue=".$sectionObj->getIssueId()
                ."&Language=".$sectionObj->getLanguageId()
                ."&Section=".$sectionObj->getSectionId(),
                false);
	    $name = htmlspecialchars($sectionObj->getName());
        $breadcrumbs[] = array($name, 
                "/$ADMIN/sections/edit.php"
                ."?Pub=".$sectionObj->getPublicationId()
                ."&Issue=".$sectionObj->getIssueId()
                ."&Language=".$sectionObj->getLanguageId()
                ."&Section=".$sectionObj->getSectionId());
	}
	if (!is_null($articleObj)) {
	    $prompt = getGS("Article").":";
		$breadcrumbs[] = array($prompt, 
                "/$ADMIN/articles/index.php"
                ."?Pub=" . $articleObj->getPublicationId() 
                ."&Issue=".$articleObj->getIssueId()
                ."&Language=".$articleObj->getLanguageId()
                ."&Section=".$articleObj->getSectionId()
                ."&Article=".$articleObj->getArticleId()
                ."&sLanguage=".$sectionObj->getLanguageId(),                
                false);
	    $name = htmlspecialchars($articleObj->getName())." (".htmlspecialchars($articleObj->getLanguageName()).")";
        $breadcrumbs[] = array($name,
                "/$ADMIN/articles/edit.php"
                ."?Pub=" . $articleObj->getPublicationId() 
                ."&Issue=".$articleObj->getIssueId()
                ."&Language=".$articleObj->getLanguageId()
                ."&Section=".$articleObj->getSectionId()
                ."&Article=".$articleObj->getArticleId()
                ."&sLanguage=".$sectionObj->getLanguageId());
	}
	if (is_array($p_extraBreadcrumbs)) {
	    //print_r($p_extraBreadcrumbs);
		foreach ($p_extraBreadcrumbs as $text => $link) {
		    $breadcrumbs[] = array($text, $link);
		}
	}
	$breadcrumbs[] = array($p_title, "");
	//echo "<pre>";
	//print_r($breadcrumbs);
	//echo "</pre>";
	?>
<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
	<?php if ($p_fValidate) { ?>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
    <script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>	
	<?php } ?>
	<TITLE><?php p($p_title); ?></TITLE>
</HEAD>

<BODY>
	<?php
	echo camp_html_breadcrumbs($breadcrumbs);
} // fn camp_html_content_top


/**
 * Create a set of breadcrumbs.
 *
 * @param array $p_crumbs
 *		An array in the form 'text' => 'link' for breadcrumbs.
 *      Farthest-away link comes first, increasing in specificity.
 *
 * @return string
 */
function camp_html_breadcrumbs($p_crumbs) 
{
    $lastCrumb = array_pop($p_crumbs);
    $str = '<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" bgcolor="#D5E2EE" width="100%"><TR><TD align="left" style="border-bottom: 1px solid black; border-top: 1px solid #8BAED1; padding-bottom: 2px; padding-top: 2px; padding-left: 1.3em; ">';
	//$str .= '<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR>';
    $str .= "<span style='padding-right: 1em; font-weight: bold; color: #2F2F2F;'>".getGS("You are here:")."</span>";
    $tmpCrumbs = array();
	foreach ($p_crumbs as $crumb) {
	    if (count($crumb) == 2) {
    	    $str .= camp_html_breadcrumb($crumb[0], $crumb[1]);
	    }
	    else {
    	    $str .= camp_html_breadcrumb($crumb[0], $crumb[1], $crumb[2]);
	    }
	}
//    $numCrumbs = count($p_crumbs);
//	for ($i = 0; i < ($numCrumbs - 1); $i++) {
//	    $str .= camp_html_breadcrumb($text, $link) . "<TD CLASS='breadcrumb_separator'>&nbsp;</TD>";
//	}
	//print_r($lastCrumb);
    $str .= camp_html_breadcrumb($lastCrumb[0], $lastCrumb[1], false, true);
    //$str .= '</TR>';
    //$str .= '</TABLE>';
    $str .= '</TD></TR>';
    $str .= '</TABLE>';
    return $str;
} // fn camp_html_breadcrumbs


/**
 * Create one breadcrumb.
 *
 * @param string $p_text
 * @param mixed $p_link
 * @param boolean $p_active
 * @param boolean $p_separator
 * @return string
 */
function camp_html_breadcrumb($p_text, $p_link, $p_separator = true, $p_active = false) {
    $tmpStr = '';
    if ($p_active) {
        $class = "breadcrumb_active";
    }
    else {
        $class = "breadcrumb";        
    }
    if ($p_separator) {
        $tmpStr .= "<span>";
    }
    else {
        $tmpStr .= "<span class='breadcrumb_intra_separator'>";
    }
	if ($p_link != "") {
        $tmpStr .= "<A HREF='$p_link' class='$class'>$p_text</A>";
	}
	else {
	    $tmpStr .= "<SPAN CLASS='$class'>$p_text</SPAN>";
	}
	$tmpStr .="</span>";
	if ($p_separator) {
        $tmpStr .= "<span CLASS='breadcrumb_separator'>&nbsp;</span>";
	}
	else {
        $tmpStr .= "<span>&nbsp;</spanTD>";	    
	}
    return $tmpStr;
} // fn camp_html_breadcrumb
?>