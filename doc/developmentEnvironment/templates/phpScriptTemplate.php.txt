<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

define('CONSTANT1', 10);
define('CONSTANT2', 20);
define('ERROR_CODE', 404);

require_once "DB.php";
require_once "../../../othermodule/var/ClassName.php";
require_once "LocalBaseClass.php";

/**
 *  Example class
 *
 *  Description of Example class
 *
 *  @author  $Author$
 *  @version $Revision$
 *  @see LocalBaseClass
 */
class Example extends LocalBaseClass{
    var $property1;
    var $property2;

    /* ========================================================== constructor */
    /**
     *  Constructor
     *
     *  @param paramName parameter type, description
     *  @return return type, description
     */
    function Example($paramName)
    {
        parent::LocalBaseClass($paramName);
        /* commands here */
    }

    /* ======================================================= public methods */
    /**
     *  First Example method
     *
     *  @param paramName parameter type, description
     *  @return return type, description
     */
    function firstExampleMethod($paramName)
    {
        /* commands here */
        return $variable;
    }

    /**
     *  Second Example method
     *
     *  @param paramName parameter type, description
     *  @return return type, description
     */
    function secondExampleMethod($paramName)
    {
        /* commands here */
        return $variable;
    }

    /* ----------------------------------------------------------- subsection */
    /* ---------------------------------------------------- redefined methods */

    /* ==================================================== "private" methods */
    /* =============================================== test and debug methods */
}

/* optional runable code here */
?>
