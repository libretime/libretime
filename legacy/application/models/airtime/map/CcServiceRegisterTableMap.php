<?php



/**
 * This class defines the structure of the 'cc_service_register' table.
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
class CcServiceRegisterTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcServiceRegisterTableMap';

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
        $this->setName('cc_service_register');
        $this->setPhpName('CcServiceRegister');
        $this->setClassname('CcServiceRegister');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('name', 'DbName', 'VARCHAR', true, 32, null);
        $this->addColumn('ip', 'DbIp', 'VARCHAR', true, 45, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // CcServiceRegisterTableMap
