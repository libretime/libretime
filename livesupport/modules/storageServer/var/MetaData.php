<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/MetaData.php,v $

------------------------------------------------------------------------------*/
define('DEBUG', FALSE);
//define('DEBUG', TRUE);
define('MODIFY_LAST_MATCH', TRUE);

require_once "XML/Util.php";

/**
 *  MetaData class
 *
 *  LiveSupport file storage support class.<br>
 *  Store metadata tree in relational database.<br>
 *
 *  @see StoredFile
 *  @see XmlParser
 *  @see DataEngine
 */
class MetaData{
    /**
     *  Constructor
     *
     *  @param gb reference to GreenBox object
     *  @param gunid string, global unique id
     *  @param resDir string, resource directory
     *  @return this
     */
    function MetaData(&$gb, $gunid, $resDir)
    {
        $this->config     =& $gb->config;
        $this->dbc        =& $gb->dbc;
        $this->mdataTable = $gb->mdataTable;
        $this->gunid      = $gunid;
        $this->resDir     = $resDir;
        $this->fname      = $this->makeFname();
        $this->exists     =
            $this->dbCheck($gunid) &&
            is_file($this->fname) &&
            is_readable($this->fname)
        ;
    }

    /**
     *  Parse and store metadata from XML file or XML string
     *
     *  @param mdata string, local path to metadata XML file or XML string
     *  @param loc string - location: 'file'|'string'
     *  @param format string, metadata format for validation
     *      ('audioclip' | 'playlist' | 'webstream' | NULL)
     *      (NULL = no validation)
     *  @return true or PEAR::error
     */
    function insert($mdata, $loc='file', $format=NULL)
    {
        if($this->exists) return FALSE;
        $tree =& $this->parse($mdata, $loc);
        if(PEAR::isError($tree)) return $tree;
        $this->format = $format;
        $res = $this->validate($tree);
        if(PEAR::isError($res)) return $res;
        $res = $this->storeDoc($tree);
        if(PEAR::isError($res)) return $res;
        switch($loc){
        case"file":
            if(! @copy($mdata, $this->fname)){
                return PEAR::raiseError(
                    "MetaData::insert: file save failed".
                    " ($mdata, {$this->fname})",GBERR_FILEIO
                );
            }
            break;
        case"string":
            $fname = $this->fname;
            $e  = FALSE;
            if(!$fh = fopen($fname, "w")){ $e = TRUE; }
            elseif(fwrite($fh, $mdata) === FALSE){ $e = TRUE; }
            if($e){
                return PEAR::raiseError(
                    "BasicStor::bsOpenDownload: can't write ($fname)",
                    GBERR_FILEIO);
            }
            fclose($fh);
            break;
        default:
            return PEAR::raiseError(
                "MetaData::insert: unsupported metadata location ($loc)"
            );
        }
        $this->exists = TRUE;
        return TRUE;
    }

    /**
     *  Call delete and insert
     *
     *  @param mdata string, local path to metadata XML file or XML string
     *  @param loc string 'file'|'string'
     *  @param format string, metadata format for validation
     *      ('audioclip' | 'playlist' | 'webstream' | NULL)
     *      (NULL = no validation)
     *  @return true or PEAR::error
     */
    function replace($mdata, $loc='file', $format=NULL)
    {
        if($this->exists){
            $res = $this->delete();
            if(PEAR::isError($res)) return $res;
        }
        return $this->insert($mdata, $loc, $format);
    }

    /**
     *  Return true if metadata exists
     *
     *  @return boolean
     */
    function exists()
    {
        return $this->exists;
    }

    /**
     *  Delete all file's metadata
     *
     *  @return true or PEAR::error
     */
    function delete()
    {
        if(file_exists($this->fname)) @unlink($this->fname);
        $res = $this->dbc->query("
            DELETE FROM {$this->mdataTable}
            WHERE gunid=x'{$this->gunid}'::bigint
        ");
        if(PEAR::isError($res)) return $res;
        $this->exists = FALSE;
        return TRUE;
    }

    /**
     *  Return metadata XML string
     *
     *  @return string
     */
    function getMetaData()
    {
        // return $this->genXMLDoc();       // obsolete
        if(file_exists($this->fname)){
            $res = file_get_contents($this->fname);
            //require_once "XML/Beautifier.php";
            //$fmt = new XML_Beautifier();
            //$res = $fmt->formatString($res);
            return $res;
        }else
            return file_get_contents(dirname(__FILE__).'/emptyMdata.xml');
    }

    /**
     *  Get metadata element value and record id
     *
     *  @param category string, metadata element name
     *  @param parid int, metadata record id of parent element
     *  @return hash {mid: int - record id, value: metadata alue}
     */
    function getMetadataEl($category, $parid=NULL)
    {
        // handle predicate namespace shortcut
        $a     = XML_Util::splitQualifiedName($category);
        if(PEAR::isError($a)) return $a;
        $catNs = $a['namespace'];
        $cat   = $a['localPart'];
        $cond = "gunid=x'{$this->gunid}'::bigint AND predicate='$cat'";
        if(!is_null($catNs)) $cond .= " AND predns='$catNs'";
        if(!is_null($parid)) $cond .= " AND subjns='_I' AND subject='$parid'";
        $sql = "
            SELECT id as mid, object as value
            FROM {$this->mdataTable}
            WHERE $cond
            ORDER BY id
        ";
        $all = $this->dbc->getAll($sql);
        $transTbl = get_html_translation_table();
        $transTbl = array_flip($transTbl);
        foreach($all as $i=>$v){
            if(!is_null($all[$i]['value'])){
                $all[$i]['value'] = strtr($all[$i]['value'], $transTbl);
            }else{
                $all[$i]['value'] = '';
            }
        }
        if(PEAR::isError($all)) return $all;
        return $all;
    }
    
    /**
     *  Set metadata value / delete metadata record
     *
     *  @param mid int, metadata record id
     *  @param value string, new metadata value (NULL for delete)
     *  @return boolean
     */
    function setMetadataEl($mid, $value=NULL)
    {
        if(!is_null($value)){
            $transTbl = get_html_translation_table();
            $value = strtr($value, $transTbl);
        }
        $info = $this->dbc->getRow("
            SELECT parmd.predns as parns, parmd.predicate as parname,
                md.predxml, md.predns as chns, md.predicate as chname
            FROM {$this->mdataTable} parmd
            INNER JOIN {$this->mdataTable} md
                ON parmd.id=md.subject AND md.subjns='_I'
            WHERE md.id=$mid
        ");
        if(PEAR::isError($info)) return $info;
        if(is_null($info)){
            return PEAR::raiseError(
                "MetaData::setMetadataEl: parent container not found"
            );
        }
        extract($info);
        $parname = ($parns ? "$parns:" : '').$parname;
        $category = ($chns ? "$chns:" : '').$chname;
        $r = $this->validateOneValue($parname, $category, $predxml, $value);
        if(PEAR::isError($r)) return $r;
        if(!is_null($value)){
            $sql = "
                UPDATE {$this->mdataTable}
                SET object='$value', objns='_L'
                WHERE id={$mid}
            ";
            $res = $this->dbc->query($sql);
        }else{
            $res = $this->deleteRecord($mid);
        }
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Insert new metadata element
     *
     *  @param parid int, metadata record id of parent element
     *  @param category string, metadata element name
     *  @param value string, new metadata value (NULL for delete)
     *  @param predxml srring, 'T' | 'A' | 'N' (tag, attr., namespace)
     *  @return int, new metadata record id
     */
    function insertMetadataEl($parid, $category, $value=NULL, $predxml='T')
    {
        //$category = strtolower($category);
        $parent = $this->dbc->getRow("
            SELECT predns, predicate, predxml FROM {$this->mdataTable}
            WHERE gunid=x'{$this->gunid}'::bigint AND id=$parid
        ");
        if(PEAR::isError($parent)) return $parent;
        if(is_null($parent)){
            return PEAR::raiseError(
                "MetaData::insertMetadataEl: container not found"
            );
        }
        $parNs = ($parent['predns'] ? "{$parent['predns']}:" : '');
        $parName = $parNs.$parent['predicate'];
        $r = $this->validateOneValue($parName, $category, $predxml, $value);
        if(PEAR::isError($r)) return $r;
        $a     = XML_Util::splitQualifiedName($category);
        if(PEAR::isError($a)) return $a;
        $catNs = $a['namespace'];
        $cat   = $a['localPart'];
        $objns = (is_null($value) ? '_blank' : '_L' );
        if(!is_null($value)){
            $transTbl = get_html_translation_table();
            $value = strtr($value, $transTbl);
        }
        $nid= $this->storeRecord('_I', $parid, $catNs, $cat, $predxml,
            $objns, $value);
        if(PEAR::isError($nid)) return $nid;
        return $nid;
    }
    
    /**
     *  Get metadata element value
     *
     *  @param category string, metadata element name
     *  @param lang string, optional xml:lang value for select language version
     *  @param deflang string, optional xml:lang for default language
     *  @param objns string, object namespace prefix - for internal use only
     *  @return array of matching records as hash with fields:
     *   <ul>
     *      <li>mid int, local metadata record id</li>
     *      <li>value string, element value</li>
     *      <li>attrs hasharray of element's attributes indexed by
     *          qualified name (e.g. xml:lang)</li>
     *   </ul>
     *  @see BasicStor::bsGetMetadataValue
     */
    function getMetadataValue($category, $lang=NULL, $deflang=NULL, $objns='_L')
    {
        $all = $this->getMetadataEl($category);
        $res = array();
        $exact = NULL;
        $def = NULL;
        $plain = NULL;
        // add attributes to result
        foreach($all as $i=>$rec){
            $pom = $this->getSubrows($rec['mid']);
            if(PEAR::isError($pom)) return $pom;
            $all[$i]['attrs'] = $pom['attrs'];
            $atlang = (isset($pom['attrs']['xml:lang']) ?
                $pom['attrs']['xml:lang'] : NULL);
            // select only matching lang (en is default)
            if(is_null($lang)){
                $res[] = $all[$i];
            }else{
                switch(strtolower($atlang)){
                    case '':
                        $plain = array($all[$i]);
                    break;
                    case strtolower($lang):
                        $exact = array($all[$i]);
                    break;
                    case strtolower($deflang):
                        $def = array($all[$i]);
                    break;
                }
            }
        }
        if($exact) return $exact;
        if($def) return $def;
        if($plain) return $plain;
        return $res;
    }

    /**
     *  Set metadata element value
     *
     *  @param category string, metadata element name (e.g. dc:title)
     *  @param value string/NULL value to store, if NULL then delete record
     *  @param lang string, optional xml:lang value for select language version
     *  @param mid int, metadata record id (OPTIONAL on unique elements)
     *  @param container string, container element name for insert
     *  @return boolean
     */
    function setMetadataValue($category, $value, $lang=NULL, $mid=NULL,
        $container='metadata')
    {
        // resolve aktual element:
        $rows   = $this->getMetadataValue($category, $lang);
        $aktual = NULL;
        if(count($rows)>1){
            if(is_null($mid)){
                if(MODIFY_LAST_MATCH){ $aktual = array_pop($rows); }
                else{
                    return PEAR::raiseError(
                        "MetaData::setMdataValue:".
                        " nonunique category, mid required"
                    );
                }
            }else{
                foreach($rows as $i=>$row){
                    if($mid == intval($row['mid'])) $aktual = $row;
                }
            }
        }else $aktual = (isset($rows[0])? $rows[0] : NULL);
        if(!is_null($aktual)){
            $res = $this->setMetadataEl($aktual['mid'], $value);
            if(PEAR::isError($res)) return $res;
            if(!is_null($lang) && $aktual['attrs']['xml:lang']!=$lang){
                $lg = $this->getMetadataEl('xml:lang', $aktual['mid']);
                if(PEAR::isError($lg)) return $lg;
                if(isset($lg['mid'])){
                    $res = $this->setMetadataEl($lg['mid'], $lang);
                    if(PEAR::isError($res)) return $res;
                }else{
                    $res = $this->insertMetadataEl(
                        $aktual['mid'], 'xml:lang', $lang, 'A');
                    if(PEAR::isError($res)) return $res;
                }
            }
        }else{
            // resolve container:
            $contArr = $this->getMetadataValue($container, NULL, NULL, '_blank');
            if(PEAR::isError($contArr)) return $contArr;
            $parid = $contArr[0]['mid'];
            if(is_null($parid)){
                return PEAR::raiseError(
                    "MetaData::setMdataValue: container ($container) not found"
                );
            }
            $nid = $this->insertMetadataEl($parid, $category, $value);
            if(PEAR::isError($nid)) return $nid;
            if(!is_null($lang)){
                $res = $this->insertMetadataEl($nid, 'xml:lang', $lang, 'A');
                if(PEAR::isError($res)) return $res;
            }
        }
        return TRUE;
    }

    /**
     *  Regenerate XML metadata file after metadata value change
     *
     *  @return boolean
     */
    function regenerateXmlFile()
    {
        $fn = $this->fname;
        $xml = $this->genXMLDoc();
        if(PEAR::isError($xml)) return $xml;
        if (!$fh = fopen($fn, 'w')) {
            return PEAR::raiseError(
                "MetaData::regenerateXmlFile: cannot open for write ($fn)"
            );
        }
        if(fwrite($fh, $xml) === FALSE) {
            return PEAR::raiseError(
                "MetaData::regenerateXmlFile: write error ($fn)"
            );
        }
        fclose($fh);
        return TRUE;
    }
    
    /**
     *  Contruct filepath of metadata file
     *
     *  @return string
     */
    function makeFname()
    {
        return "{$this->resDir}/{$this->gunid}.xml";
    }
    
    /**
     *  Return filename
     *
     *  @return string
     */
    function getFname()
    {
        return $this->fname;
    }
    
    /**
     *  Set the metadata format to the object instance
     */
    function setFormat($format=NULL)
    {
        $this->format = $format;
    }
    
    /**
     *  Check if there are any file's metadata in database
     *
     *  @param gunid string, global unique id
     *  @return boolean
     */
    function dbCheck($gunid)
    {
        $cnt = $this->dbc->getOne("
            SELECT count(*)as cnt
            FROM {$this->mdataTable}
            WHERE gunid=x'$gunid'::bigint
        ");
        if(PEAR::isError($cnt)) return $cnt;
        return (intval($cnt) > 0);
    }

    /* ============================================= parse and store metadata */
    /**
     *  Parse XML metadata
     *
     *  @param mdata string, local path to metadata XML file or XML string
     *  @param loc string, location: 'file'|'string'
     *  @return array reference, parse result tree (or PEAR::error)
     */
    function &parse($mdata='', $loc='file')
    {
        switch($loc){
        case"file":
            if(!is_file($mdata)){
                return PEAR::raiseError(
                    "MetaData::parse: metadata file not found ($mdata)"
                );
            }
            if(!is_readable($mdata)){
                return PEAR::raiseError(
                    "MetaData::parse: can't read metadata file ($mdata)"
                );
            }
            $mdata = file_get_contents($mdata);
        case"string":
            require_once"XmlParser.php";
            $parser =& new XmlParser($mdata);
            if($parser->isError()){
                return PEAR::raiseError(
                    "MetaData::parse: ".$parser->getError()
                );
            }
            $tree = $parser->getTree();
            break;
        default:
            return PEAR::raiseError(
                "MetaData::parse: unsupported metadata location ($loc)"
            );
        }
        return $tree;
    }
    
    /**
     *  Validate parsed metadata
     *
     *  @param tree array, parsed tree
     *  @return true or PEAR::error
     */
    function validate(&$tree)
    {
        if($this->config['validate'] && !is_null($this->format)){
            require_once"Validator.php";
            $val =& new Validator($this->format, $this->gunid);
            if(PEAR::isError($val)) return $val;
            $res = $val->validate($tree);
            if(PEAR::isError($res)) return $res;
        }
        return TRUE;
    }
    
    /**
     *  Validate one metadata value (on insert/update)
     *
     *  @param parName string - parent element name
     *  @param category string - qualif.category name
     *  @param predxml string - 'A' | 'T' (attr or tag)
     *  @param value string - validated element value
     *  @return true or PEAR::error
     */
    function validateOneValue($parName, $category, $predxml, $value)
    {
        if($this->config['validate'] && !is_null($this->format)){
            require_once"Validator.php";
            $val =& new Validator($this->format, $this->gunid);
            if(PEAR::isError($val)) return $val;
            $r = $val->validateOneValue($parName, $category, $predxml, $value);
            if(PEAR::isError($r)) return $r;
        }
        return TRUE;
    }
    
    /**
     *  Insert parsed metadata into database
     *
     *  @param tree array, parsed tree
     *  @return true or PEAR::error
     */
    function storeDoc(&$tree)
    {
        $this->dbc->query("BEGIN");
        $res = $this->storeNode($tree);
        if(PEAR::isError($res)){
            $this->dbc->query("ROLLBACK"); return $res;
        }
        $res = $this->dbc->query("COMMIT");
        if(PEAR::isError($res)){ $this->dbc->query("ROLLBACK"); return $res; }
        return TRUE;


        return $root;
    }

    /**
     *  Process one node of metadata XML for insert or update.<br>
     *
     *  @param node object, node in tree returned by XmlParser
     *  @param parid int, parent id
     *  @param nSpaces array of name spaces definitions
     *  @return int, local metadata record id
     */
    function storeNode(&$node, $parid=NULL, $nSpaces=array())
    {
        //echo $node->node_name().", ".$node->node_type().", ".$node->prefix().", $parid.\n";
        $nSpaces = array_merge($nSpaces, $node->nSpaces);
        // null parid = root node of metadata tree
        $subjns  = (is_null($parid)? '_G'         : '_I');
        $subject = (is_null($parid)? $this->gunid : $parid);
        $object  = $node->content;
        if(is_null($object) || $object == ''){
            $objns  = '_blank';
            $object = NULL;
        }else $objns = '_L';
        $id = $this->storeRecord($subjns, $subject,
            $node->ns, $node->name, 'T', $objns, $object);
        // process attributes
        foreach($node->attrs as $atn=>$ato){
            $this->storeRecord('_I', $id,
                $ato->ns, $ato->name, 'A', '_L', $ato->val);
        }
        // process child nodes
        foreach($node->children as $ch){
            $this->storeNode($ch, $id, $nSpaces);
        }
        // process namespace definitions
        foreach($node->nSpaces as $ns=>$uri){
            $this->storeRecord('_I', $id,
                'xmlns', $ns, 'N', '_L', $uri);
        }
        return $id;
    }

    /**
     *  Update object namespace and value of one metadata record
     *  identified by metadata record id
     *
     *  @param mdid int, metadata record id
     *  @param object string, object value, e.g. title string
     *  @param objns string, object namespace prefix, have to be defined
     *          in file's metadata
     *  @return true or PEAR::error
     */
    function updateRecord($mdid, $object, $objns='_L')
    {
        $objns_sql  = (is_null($objns) ? "NULL" : "'$objns'" );
        $object_sql = (is_null($object)? "NULL" : "'$object'");
        $res = $this->dbc->query("UPDATE {$this->mdataTable}
            SET objns  = $objns_sql,  object    = $object_sql
            WHERE gunid = x'{$this->gunid}'::bigint AND id='$mdid'
        ");
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }

    /**
     *  Insert or update of one metadata record completely
     *
     *  @param subjns string, subject namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @param subject string, subject value, e.g. gunid
     *  @param predns string, predicate namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @param predicate string, predicate value, e.g. name of DC element
     *  @param predxml string 'T'|'A'|'N' - XML tag, attribute or NS def.
     *  @param objns string, object namespace prefix, have to be defined
     *          in file's metadata (or reserved prefix)
     *  @param object string, object value, e.g. title of song
     *  @return int, new metadata record id
     */
    function storeRecord($subjns, $subject, $predns, $predicate, $predxml='T',
        $objns=NULL, $object=NULL)
    {
        //echo "$subjns, $subject, $predns, $predicate, $predxml, $objns, $object\n";
        //$predns = strtolower($predns);
        //$predicate = strtolower($predicate);
        $predns_sql = (is_null($predns) ? "NULL" : "'$predns'" );
        $objns_sql  = (is_null($objns) ? "NULL" : "'$objns'" );
        $object_sql = (is_null($object)? "NULL" : "'$object'");
        $id = $this->dbc->nextId("{$this->mdataTable}_id_seq");
        if(PEAR::isError($id)) return $id;
        $res = $this->dbc->query("
            INSERT INTO {$this->mdataTable}
                (id , gunid           , subjns   , subject   ,
                    predns     , predicate   , predxml   ,
                    objns     , object
                )
            VALUES
                ($id, x'{$this->gunid}'::bigint, '$subjns', '$subject',
                    $predns_sql, '$predicate', '$predxml',
                    $objns_sql, $object_sql
                )
        ");
        if(PEAR::isError($res)) return $res;
        return $id;
    }

    /**
     *  Delete metadata record recursively
     *
     *  @param mid int local metadata record id
     *  @return boolean
     */
    function deleteRecord($mid)
    {
        $sql = "
            SELECT id FROM {$this->mdataTable}
            WHERE subjns='_I' AND subject='{$mid}' AND
                gunid=x'{$this->gunid}'::bigint
        ";
        $rh = $this->dbc->query($sql);
        if(PEAR::isError($rh)) return $rh;
        while($row = $rh->fetchRow()){
            $r = $this->deleteRecord($row['id']);
            if(PEAR::isError($r)) return $r;
        }
        $rh->free();
        $sql = "
            DELETE FROM {$this->mdataTable}
            WHERE id={$mid} AND
                gunid=x'{$this->gunid}'::bigint
        ";
        $res = $this->dbc->query($sql);
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
    
    /* =========================================== XML reconstruction from db */
    /**
     *  Generate PHP array from metadata database
     *
     *  @return array with metadata tree
     */
    function genPhpArray()
    {
        $res = array();
        $row = $this->dbc->getRow("
            SELECT * FROM {$this->mdataTable}
            WHERE gunid=x'{$this->gunid}'::bigint
                AND subjns='_G' AND subject='{$this->gunid}'
        ");
        if(PEAR::isError($row)) return $row;
        if(is_null($row)){
        }else{
            $node = $this->genXMLNode($row, FALSE);
            if(PEAR::isError($node)) return $node;
            $res = $node;
        }
        return $res;
    }

    /**
     *  Generate XML document from metadata database
     *
     *  @return string with XML document
     */
    function genXMLDoc()
    {
        require_once "XML/Util.php";
        $res = XML_Util::getXMLDeclaration("1.0", "UTF-8")."\n";
        $row = $this->dbc->getRow("
            SELECT * FROM {$this->mdataTable}
            WHERE gunid=x'{$this->gunid}'::bigint
                AND subjns='_G' AND subject='{$this->gunid}'
        ");
        if(PEAR::isError($row)) return $row;
        if(is_null($row)){
            $node = XML_Util::createTagFromArray(array(
                'localpart'=>'none'
            ));
        }else{
            $node = $this->genXMLNode($row);
            if(PEAR::isError($node)) return $node;
        }
        $res .= $node;
        require_once "XML/Beautifier.php";
        $fmt = new XML_Beautifier();
        $res = $fmt->formatString($res);
        return $res;
    }

    /**
     *  Generate XML element from database
     *
     *  @param row array, hash with metadata record fields
     *  @param genXML boolean, if TRUE generate XML else PHP array
     *  @return string, XML serialization of node
     */
    function genXMLNode($row, $genXML=TRUE)
    {
        if(DEBUG) echo"genXMLNode:\n";
        if(DEBUG) var_dump($row);
        extract($row);
        $arr = $this->getSubrows($id, $genXML);
        if(PEAR::isError($arr)) return $arr;
        if(DEBUG) var_dump($arr);
        extract($arr);
        if($genXML){
            $node = XML_Util::createTagFromArray(array(
                'namespace' => $predns,
                'localPart' => $predicate,
                'attributes'=> $attrs,
                'content'   => (is_null($object) ? $children : $object),
            ), FALSE);
        }else{
            $node = array_merge(
                array(
                    'elementname'    => ($predns ? "$predns:" : '').$predicate,
                    'content' => $object,
                    'children'=> $children,
                ), $arr
            );
        }
        return $node;
    }

    /**
     *  Return values of attributes, child nodes and namespaces for
     *  one metadata record
     *
     *  @param parid int, local id of parent metadata record
     *  @param genXML boolean, if TRUE generate XML else PHP array for
     *      children
     *  @return hash with three fields:
     *      - attr hash, attributes
     *      - children array, child nodes
     *      - nSpaces hash, namespace definitions
     */
    function getSubrows($parid, $genXML=TRUE)
    {
        if(DEBUG) echo" getSubrows:\n";
        $qh = $this->dbc->query($q = "
            SELECT
                id, predxml, predns, predicate, objns, object
            FROM {$this->mdataTable}
            WHERE
                subjns='_I' AND subject='$parid' AND
                gunid=x'{$this->gunid}'::bigint
            ORDER BY id
        ");
        if(PEAR::isError($qh)) return $qh;
        $attrs      = array();
        $children   = array();
        $nSpaces    = array();
        if(DEBUG) echo "  #=".$qh->numRows()."\n$q\n";
        while($row = $qh->fetchRow()){
            if(DEBUG) var_dump($row);
            extract($row);
            switch($predxml){
            case"N":
                $nSpaces["$predicate"] = $object;
            case"A":
                $sep=':';
                if($predns=='' || $predicate=='') $sep='';
                $attrs["{$predns}{$sep}{$predicate}"] = $object;
                break;
            case"T":
                $children[] = $this->genXMLNode($row, $genXML);
                break;
            default:
                return PEAR::raiseError(
                    "MetaData::getSubrows: unknown predxml ($predxml)");
            } // switch
        }
        $qh->free();
        if($genXML) $children   = join(" ", $children);
        return compact('attrs', 'children', 'nSpaces');
    }
    
    /* ========================================================= test methods */
    /**
     *  Test method
     *
     *  @return true or PEAR::error
     */
    function test()
    {
        $res = $this->replace(getcwd().'/mdata2.xml');
        if(PEAR::isError($res)) return $res;
        $res = $this->getMetaData();
        if(PEAR::isError($res)) return $res;
        return TRUE;
    }
}
?>
