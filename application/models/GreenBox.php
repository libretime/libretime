<?php
if (isset($WHITE_SCREEN_OF_DEATH) && $WHITE_SCREEN_OF_DEATH) {
    echo __FILE__.':line '.__LINE__.": Greenbox begin<br>";
}
require_once("BasicStor.php");
if (isset($WHITE_SCREEN_OF_DEATH) && $WHITE_SCREEN_OF_DEATH) {
    echo __FILE__.':line '.__LINE__.": Loaded BasicStor<br>";
}
require_once("Playlist.php");
require_once('Prefs.php');
require_once("Transport.php");

/**
 * GreenBox class
 *
 * File storage module.
 *
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
class GreenBox extends BasicStor {

    /* ====================================================== storage methods */
    /* ------------------------------------------------------------- metadata */

    /**
     * Search in local metadata database.
     *
     * @param array $criteria
     *      with following structure:<br>
     *   <ul>
     *     <li>filetype - string, type of searched files,
     *       meaningful values: 'audioclip', 'webstream', 'playlist', 'all'</li>
     *     <li>operator - string, type of conditions join
     *       (any condition matches / all conditions match),
     *       meaningful values: 'and', 'or', ''
     *       (may be empty or ommited only with less then 2 items in
     *       &quot;conditions&quot; field)
     *     </li>
     *     <li>limit : int - limit for result arrays (0 means unlimited)</li>
     *     <li>offset : int - starting point (0 means without offset)</li>
     *     <li>orderby : string - metadata category for sorting (optional)
     *          or array of strings for multicolumn orderby
     *          [default: dc:creator, dc:source, dc:title]
     *     </li>
     *     <li>desc : boolean - flag for descending order (optional)
     *          or array of boolean for multicolumn orderby
     *          (it corresponds to elements of orderby field)
     *          [default: all ascending]
     *     </li>
     *     <li>conditions - array of hashes with structure:
     *       <ul>
     *           <li>cat - string, metadata category name</li>
     *           <li>op - string, operator - meaningful values:
     *               'full', 'partial', 'prefix', '=', '&lt;',
     *               '&lt;=', '&gt;', '&gt;='</li>
     *           <li>val - string, search value</li>
     *       </ul>
     *     </li>
     *   </ul>
     * @param string $sessid
     *      session id
     * @return array of hashes, fields:
     *   <ul>
     *       <li>cnt : integer - number of matching gunids
     *              of files have been found</li>
     *       <li>results : array of hashes:
     *          <ul>
     *           <li>gunid: string</li>
     *           <li>type: string - audioclip | playlist | webstream</li>
     *           <li>title: string - dc:title from metadata</li>
     *           <li>creator: string - dc:creator from metadata</li>
     *           <li>length: string - dcterms:extent in extent format</li>
     *          </ul>
     *      </li>
     *   </ul>
     *  @see BasicStor::bsLocalSearch
     */
    public function localSearch($criteria, $sessid='')
    {
        $limit = intval(isset($criteria['limit']) ? $criteria['limit'] : 0);
        $offset = intval(isset($criteria['offset']) ? $criteria['offset'] : 0);
        return $this->bsLocalSearch($criteria, $limit, $offset);
    } // fn localSearch


    /*====================================================== playlist methods */
    /**
     * Close import-handle and import playlist
     *
     * @param string $token
     *      import token obtained by importPlaylistOpen method
     * @return int
     * 		result file local id (or error object)
     */
    public function importPlaylistClose($token)
    {
        $arr = $this->bsClosePut($token);
        if (PEAR::isError($arr)) {
            return $arr;
        }
        $fname = $arr['fname'];
        $owner = $arr['owner'];
        $res = $this->bsImportPlaylist($fname, $owner);
        if (file_exists($fname)) {
            @unlink($fname);
        }
        return $res;
    } // fn importPlaylistClose


    /* ========================================================= info methods */
    /* ==================================================== redefined methods */

    /**
     * Change user password.
     *
     *   ('superuser mode'= superuser is changing some password without
     *    knowledge of the old password)
     *
     * @param string $login
     * @param string $oldpass
     *      old password
     *      (should be null or empty for 'superuser mode')
     * @param string $pass
     * @param string $sessid
     *      session id, required for 'superuser mode'
     * @return boolean/err
     */
    public function passwd($login, $oldpass=null, $pass='', $sessid='')
    {
        if (is_null($oldpass) || ($oldpass == '') ) {
            if (($res = BasicStor::Authorize('subjects', $this->rootId, $sessid)) !== TRUE) {
                sleep(2);
                return $res;
            } else {
                $oldpass = null;
            }
        } else {
            if (FALSE === Subjects::Authenticate($login, $oldpass)) {
                sleep(2);
                return PEAR::raiseError(
                    "GreenBox::passwd: access denied (oldpass)", GBERR_DENY);
            }
        }
        $res = Subjects::Passwd($login, $oldpass, $pass);
        if (PEAR::isError($res)) {
            return $res;
        }
        return TRUE;
    } // fn passwd


} // class GreenBox
