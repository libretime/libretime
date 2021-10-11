<?php



/**
 * This class defines the structure of the 'cc_country' table.
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
class CcCountryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcCountryTableMap';

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
        $this->setName('cc_country');
        $this->setPhpName('CcCountry');
        $this->setClassname('CcCountry');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('isocode', 'DbIsoCode', 'CHAR', true, 3, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', true, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // CcCountryTableMap
