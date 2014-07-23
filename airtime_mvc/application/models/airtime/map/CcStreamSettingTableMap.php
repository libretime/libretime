<?php



/**
 * This class defines the structure of the 'cc_stream_setting' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.airtime.map
 */
class CcStreamSettingTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcStreamSettingTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('cc_stream_setting');
        $this->setPhpName('CcStreamSetting');
        $this->setClassname('CcStreamSetting');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('keyname', 'DbKeyName', 'VARCHAR', true, 64, null);
        $this->addColumn('value', 'DbValue', 'VARCHAR', false, 255, null);
        $this->addColumn('type', 'DbType', 'VARCHAR', true, 16, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // CcStreamSettingTableMap
