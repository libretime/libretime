<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
require_once dirname(__FILE__) . '/../../../../runtime/lib/adapter/DBAdapter.php';
require_once dirname(__FILE__) . '/../../../../runtime/lib/adapter/DBMSSQL.php';
require_once dirname(__FILE__) . '/DBAdapterTestAbstract.php';

/**
 * Tests the DBMSSQL adapter
 *
 * @author Markus Staab <markus.staab@redaxo.de>
 * @package runtime.adapter
 */
class DBMSSQLTest extends DBAdapterTestAbstract
{
    public function testSelectJoinOrderAlias()
    {
        $adapter = new DBMSSQL();
        $sql = 'SELECT Field, Related.Field AS [RelatedField] FROM Record LEFT JOIN Related ON Record.RelatedID = Related.ID ORDER BY [RelatedField] ASC';
        $adapter->applyLimit($sql, 10, 5);
        $this->assertEquals('SELECT [Field], [RelatedField] FROM (SELECT ROW_NUMBER() OVER(ORDER BY Related.Field ASC) AS [RowNumber], Field AS [Field], Related.Field AS [RelatedField] FROM Record LEFT JOIN Related ON Record.RelatedID = Related.ID) AS derivedb WHERE RowNumber BETWEEN 11 AND 15', $sql);
    }

    public function testQuotingIdentifiers()
    {
        $db = new DBMSSQL();
        $this->assertEquals('[Book ISBN]', $db->quoteIdentifier('Book ISBN'));
    }

    public function testCaseQuery()
    {
        $adapter = new DBMSSQL();
        $sql = "SELECT Field1, CASE WHEN Field2 = 'non-relevant' THEN 0 ELSE 1 END AS [Relevant] FROM Record ORDER BY [Relevant] ASC";
        $adapter->applyLimit($sql, 10, 5);
        $this->assertEquals("SELECT [Field1], [Relevant] FROM (SELECT ROW_NUMBER() OVER(ORDER BY CASE WHEN Field2 = 'non-relevant' THEN 0 ELSE 1 END ASC) AS [RowNumber], Field1 AS [Field1], CASE WHEN Field2 = 'non-relevant' THEN 0 ELSE 1 END AS [Relevant] FROM Record) AS derivedb WHERE RowNumber BETWEEN 11 AND 15", $sql);
    }
}
