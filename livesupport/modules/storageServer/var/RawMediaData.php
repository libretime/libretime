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
 
 
    Author   : $Author: tomas $
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/var/RawMediaData.php,v $

------------------------------------------------------------------------------*/

/**
 *  RawMediaData class
 *
 *  LiveSupport file storage support class
 *  Store media files in real filesystem and handle access to them.<br>
 *
 *  @see StoredFile
 */

/* ================== RawMediaData ================== */
class RawMediaData{

    /**
     *  Constructor
     *
     *  @param gunid string, global unique id
     *  @param resDir string, directory
     *  @return this
     */
    function RawMediaData($gunid, $resDir)
    {
        $this->gunid  = $gunid;
        $this->resDir = $resDir;
        $this->fname  = $this->makeFname();
        $this->exists = file_exists($this->fname);
    }

    /**
     *  Insert media file to filesystem
     *
     *  @param mediaFileLP string, local path
     *  @return true or PEAR::error
     */
    function insert($mediaFileLP)
    {
        if($this->exists) return FALSE;
        // for files downloaded from archive:
        if($mediaFileLP == $this->fname){
            $this->exists = TRUE;
            return TRUE;
        }
        umask(0002);
        if(@copy($mediaFileLP, $this->fname)){
            $this->exists = TRUE;
            return TRUE;
        }else{
            //@unlink($this->fname);    // maybe useless
            $this->exists  = FALSE;
            return PEAR::raiseError(
                "RawMediaData::insert: file save failed".
                " ($mediaFileLP, {$this->fname})",GBERR_FILEIO
            );
        }
    }
    
    /**
     *  Delete and insert media file
     *
     *  @param mediaFileLP string, local path
     *  @return true or PEAR::error
     */
    function replace($mediaFileLP)
    {
        if($this->exists) $r = $this->delete();
        if(PEAR::isError($r)) return $r;
        return $this->insert($mediaFileLP);
    }
    
    /**
     *  Return true if file corresponding to the object exists
     *
     *  @return boolean
     */
    function exists()
    {
        return $this->exists;
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
    
/*
    /**
     *  Make access symlink to the media file
     *
     *  @param accLinkName string, access symlink name
     *  @return string, access symlink name
     * /
    function access($accLinkName)
    {
        if(!$this->exists) return FALSE;
        if(file_exists($accLinkName))   return $accLinkName;
        if(@symlink($this->fname, $accLinkName)){
            return $accLinkName;
        }else return PEAR::raiseError(
            "RawMediaData::access: symlink create failed ($accLinkName)",
            GBERR_FILEIO
        );
    }
    
    /**
     *  Delete access symlink
     *
     *  @param accLinkName string, access symlink name
     *  @return boolean or PEAR::error
     * /
    function release($accLinkName)
    {
        if(!$this->exists) return FALSE;
        if(@unlink($accLinkName)) return TRUE;
        else return PEAR::raiseError(
            "RawMediaData::release: unlink failed ($accLinkName)", GBERR_FILEIO
        );
    }
*/

    /**
     *  Delete media file from filesystem
     *
     *  @return boolean or PEAR::error
     */
    function delete()
    {
        if(!$this->exists) return FALSE;
        if(@unlink($this->fname)){
            $this->exists = FALSE;
            return TRUE;
        }else{
            return PEAR::raiseError(
                "RawMediaData::delete: unlink failed ({$this->fname})",
                GBERR_FILEIO
            );
        }
        return $this->exists;
    }

    /**
     *  Analyze media file with getid3 module
     *
     *  @return hierarchical hasharray with information about media file
     */
    function analyze()
    {
        if(!$this->exists) return FALSE;
        $ia = GetAllFileinfo($this->fname);
        return $ia;
    }

    /**
     *  Get mime-type returned by getid3 module
     *
     *  @return string
     */
    function getMime()
    {
        $a = $this->analyze();
        if($a === FALSE) return $a;
        return $a['mime_type'];
    }

    /**
     *  Contruct filepath of media file
     *
     *  @return string
     */
    function makeFname()
    {
        return "{$this->resDir}/{$this->gunid}";
    }
    
    /**
     *  Test method
     *
     *  @param testFname1 string
     *  @param testFname2 string
     *  @param accLinkFname string
     *  @return string
     */
    function test($testFname1, $testFname2, $accLinkFname)
    {
        $log = '';
        if($this->exists())
            $log .= "---: exists: YES\n";
        else
            $log .= "---: exists: NO\n";
        if(!($r = $this->delete()))
            $log .= "---: delete: nothing to delete\n";
        if(PEAR::isError($r))
            $log .= "ERR: ".$r->getMessage()."\n";
        if($r = $this->insert($testFname1))
            $log .= "---: insert: already exists\n";
        if(PEAR::isError($r))
            $log .= "ERR: ".$r->getMessage()."\n";
        if($r = $this->replace($testFname2))
            $log .= "---: replace: already exists\n";
        if(PEAR::isError($r))
            $log .= "ERR: ".$r->getMessage()."\n";
        if($this->exists())
            $log .= "---: exists: YES\n";
        else
            $log .= "---: exists: NO\n";
        if(!$this->access($accLinkFname))
            $log .= "---: access: not exists\n";
        if(($ft = filetype($accLinkFname)) == 'link'){
            if(($rl = readlink($accLinkFname)) != $this->fname)
                $log .= "ERR: wrong target ($rl)\n";
        }else
            $log .= "ERR: wrong file type ($ft)\n";
        if(!$this->release($accLinkFname))
            $log .= "---: access: not exists\n";
        return $log;
    }
}
?>