<?php

class PlatformDatabaseBuildTimeBase extends PHPUnit_Framework_TestCase
{

    /**
     * @var Database
     */
    public $database;

    /**
     * @var MysqlSchemaParser
     */
    public $parser;

    /**
     * @var array
     */
    public $oldPropelConfiguration;

    /**
     * @var PDO
     */
    public $con;

    protected function setUp()
    {
        if (Propel::isInit()) {
            $this->oldPropelConfiguration = Propel::getConfiguration();
        }

        $xmlDom = new DOMDocument();
        $xmlDom->load(dirname(__FILE__) . '/../../fixtures/reverse/mysql/runtime-conf.xml');
        $xml = simplexml_load_string($xmlDom->saveXML());
        $phpconf = PlatformDatabaseBuildTimeBaseTask::simpleXmlToArray($xml);

        Propel::setConfiguration($phpconf);
        Propel::initialize();
        $this->con = Propel::getConnection('reverse-bookstore');

        $this->parser = new MysqlSchemaParser(Propel::getConnection('reverse-bookstore'));
        $this->parser->setGeneratorConfig(new QuickGeneratorConfig(new MysqlPlatform()));

        parent::setUp();
    }

    public function readDatabase()
    {
        $this->database = new Database();
        $this->database->setPlatform(new MysqlPlatform());
        $this->parser->parse($this->database);
    }

    protected function tearDown()
    {
        if ($this->oldPropelConfiguration) {
            Propel::setConfiguration($this->oldPropelConfiguration);
            Propel::initialize();
        }
        parent::tearDown();
    }

    /**
     * Detects the differences between current connected database and $pDatabase
     * and updates the schema. This does not DROP tables.
     *
     * @param Database $pDatabase
     */
    public function updateSchema($pDatabase)
    {
        $diff = PropelDatabaseComparator::computeDiff($this->database, $pDatabase);
        $sql = $this->database->getPlatform()->getModifyDatabaseDDL($diff);

        $statements = PropelSQLParser::parseString($sql);
        foreach ($statements as $statement) {
            if (strpos($statement, 'DROP') === 0) {
                // drop statements cause errors since the table doesn't exist
                continue;
            }
            $stmt = $this->con->prepare($statement);
            if ($stmt instanceof PDOStatement) {
                // only execute if has no error
                $stmt->execute();
            }
        }
    }
}

/*
 * We needs this wrapper since `PropelConvertConfTask::simpleXmlToArray` is protected.
 */
class PlatformDatabaseBuildTimeBaseTask extends PropelConvertConfTask
{
    public static function simpleXmlToArray($xml)
    {
        return parent::simpleXmlToArray($xml);
    }
}
