<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once dirname(__FILE__) . '/../../../../../runtime/lib/Propel.php';

require_once dirname(__FILE__) . '/../../../../../generator/lib/reverse/pgsql/PgsqlSchemaParser.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/config/QuickGeneratorConfig.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/PropelTypes.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/model/Database.php';
require_once dirname(__FILE__) . '/../../../../../generator/lib/platform/DefaultPlatform.php';

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../../../../../generator/lib');
require_once dirname(__FILE__) . '/../../../../../generator/lib/task/PropelConvertConfTask.php';

/**
 * Tests for Pgsql database schema parser.
 *
 * @author      Alan Pinstein
 * @version     $Revision$
 * @package     propel.generator.reverse.pgsql
 */
class PgsqlSchemaParserTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('PGSQL unit test');

        parent::setUp();

        $xmlDom = new DOMDocument();
        $xmlDom->load(dirname(__FILE__) . '/../../../../fixtures/reverse/pgsql/runtime-conf.xml');
        $xml = simplexml_load_string($xmlDom->saveXML());
        $phpconf = TestablePropelConvertConfTask::simpleXmlToArray($xml);

        Propel::setConfiguration($phpconf);
        Propel::initialize();

        $this->con = Propel::getConnection('reverse-bookstore');
        $this->con->beginTransaction();
    }

    protected function tearDown()
    {
        if ($this->con) {
            $this->con->rollback();
        }

        parent::tearDown();
        Propel::init(dirname(__FILE__) . '/../../../../fixtures/bookstore/build/conf/bookstore-conf.php');
    }

    public function parseDataProvider()
    {
        return array(
            // columnDDL, expectedColumnPhpName, expectedColumnDefaultType, expectedColumnDefaultValue, expectedSize, expectedScale
            array("my_column varchar(20) default null", "MyColumn", ColumnDefaultValue::TYPE_VALUE, "NULL", 20, null),
            array("my_column varchar(20) default ''", "MyColumn", ColumnDefaultValue::TYPE_VALUE, "", 20, null),
            array("my_column numeric(11,0) default 0", "MyColumn", ColumnDefaultValue::TYPE_VALUE, 0, 11, 0),
            array("my_column numeric(55,8) default 0", "MyColumn", ColumnDefaultValue::TYPE_VALUE, 0, 55, 8),
        );
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse($columnDDL, $expectedColumnPhpName, $expectedColumnDefaultType, $expectedColumnDefaultValue, $expectedSize, $expectedScale)
    {
        $this->con->query("create table foo ( {$columnDDL} );");
        $parser = new PgsqlSchemaParser($this->con);
        $parser->setGeneratorConfig(new QuickGeneratorConfig());

        $database = new Database();
        $database->setPlatform(new DefaultPlatform());

        // make sure our DDL insert produced exactly the SQL we inserted
        $this->assertEquals(1, $parser->parse($database), 'One table and one view defined should return one as we exclude views');
        $tables = $database->getTables();
        $this->assertEquals(1, count($tables));
        $table = $tables[0];
        $columns = $table->getColumns();
        $this->assertEquals(1, count($columns));

        // check out our rev-eng column info
        $defaultValue = $columns[0]->getDefaultValue();
        $this->assertEquals($expectedColumnPhpName, $columns[0]->getPhpName());
        $this->assertEquals($expectedColumnDefaultType, $defaultValue->getType());
        $this->assertEquals($expectedColumnDefaultValue, $defaultValue->getValue());
        $this->assertEquals($expectedSize, $columns[0]->getSize());
        $this->assertEquals($expectedScale, $columns[0]->getScale());
    }
}

class TestablePropelConvertConfTask extends PropelConvertConfTask
{
    public static function simpleXmlToArray($xml)
    {
        return parent::simpleXmlToArray($xml);
    }
}
