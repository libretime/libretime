<?php

/**
 * Creates the <kbd>phptest</kbd> table
 *
 * Tries to drop the table first, in case it already exists.
 *
 * <pre>
 * CREATE TABLE phptest (
 *   a INTEGER NULL,
 *   b CHAR(40) DEFAULT 'def' NOT NULL,
 *   c VARCHAR(255) NULL,
 *   d VARCHAR(20) NULL)
 * </pre>
 *
 * Need <kbd>NOT NULL</kbd> on <kbd>b</kbd> to test
 * <kbd>DB_PORTABILITY_RTRIM</kbd>.  MS SQL and Sybase trim output from
 * <kbd>VARCHAR</kbd>, but not on <kbd>CHAR</kbd>.
 *
 * Need <kbd>DEFAULT</kbd> value on <kbd>b</kbd> because Oracle considers
 * an empty string to be <kbd>NULL</kbd>.
 *
 * In Oracle, when using placeholders in <kbd>WHERE</kbd> clauses on 
 * <kbd>CHAR</kbd> columns, the column must have <kbd>RTRIM()</kbd> run on
 * the column:
 * <samp>
 *    SELECT * FROM phptest WHERE RTRIM(b) = ?
 * </samp>
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Database
 * @package    DB
 * @author     Daniel Convissor <danielc@php.net>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    $Id: mktable.inc,v 1.19 2005/02/14 23:33:20 danielc Exp $
 * @link       http://pear.php.net/package/DB
 */

/**
 * Establishes the DB object and connects to the database
 */
require_once './connect.inc';

/**
 * Get the drop_table() function
 */
require_once './droptable.inc';

/**
 * The error handler for the drop table procedure
 *
 * Prints out an error message and dies.
 */
function debug_die($o){
    die($o->toString());
}


$dbh->setErrorHandling(PEAR_ERROR_RETURN);
drop_table($dbh, 'phptest');

//$dbh->setErrorHandling(PEAR_ERROR_TRIGGER);
$dbh->setErrorHandling(PEAR_ERROR_CALLBACK, 'debug_die');

if ($dbh->phptype == 'odbc') {
    if ($dbh->dbsyntax == 'odbc') {
        $type = $dbh->phptype;
    } else {
        $type = $dbh->dbsyntax;
    }
} else {
    $type = $dbh->phptype;
}


switch ($type) {
    case 'access':
        $null = 'NULL';
        $chrc = 'VARCHAR(255)';
        $chrd = 'VARCHAR(20)';
        $default = '';
        $tabletype = '';
        break;
    case 'db2':
    case 'ibase':
        $null = '';
        $chrc = 'VARCHAR(255)';
        $chrd = 'VARCHAR(20)';
        $default = "DEFAULT 'def' NOT NULL";
        $tabletype = '';
        break;
    case 'fbsql':
        $null = '';
        $chrc = 'CHAR(255)';
        $chrd = 'CHAR(20)';
        $default = "DEFAULT 'def' NOT NULL";
        $date_literal = ' DATE ';
        $tabletype = '';
        break;
    case 'ifx':
        // doing this for ifx to keep certain versions happy
        $null = '';
        $chrc = 'CHAR(255)';
        $chrd = 'CHAR(20)';
        $default = "DEFAULT 'def' NOT NULL";
        $tabletype = '';
        break;
    case 'msql':
        $null = '';
        $chrc = 'CHAR(255)';
        $chrd = 'CHAR(20)';
        $default = '';
        $tabletype = '';
        break;
    case 'mysql':
    case 'mysqli':
        $null = 'NULL';
        $chrc = 'VARCHAR(255)';
        $chrd = 'VARCHAR(20)';
        $default = "DEFAULT 'def' NOT NULL";
        if (!empty($needinnodb)) {
            $tabletype = 'TYPE=INNODB';
        } else {
            $tabletype = '';
        }
        break;
    default:
        $null = 'NULL';
        $chrc = 'VARCHAR(255)';
        $chrd = 'VARCHAR(20)';
        $default = "DEFAULT 'def' NOT NULL";
        $tabletype = '';
}

switch ($dbh->phptype) {
    case 'dbase':
        // file exists or was created in DB_dbase::connect()
        break;
    default:
        $test_mktable_query = "
            CREATE TABLE phptest (
              a INTEGER $null,
              b CHAR(40) $default,
              c $chrc $null,
              d $chrd $null) $tabletype
        ";
}


$dbh->query($test_mktable_query);
$dbh->query("INSERT INTO phptest VALUES(42, 'bing', 'This is a test', '1999-11-21')");

$dbh->setErrorHandling(PEAR_ERROR_RETURN);
