<?php



/**
 * This class defines the structure of the 'cc_login_attempts' table.
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
class CcLoginAttemptsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcLoginAttemptsTableMap';

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
        $this->setName('cc_login_attempts');
        $this->setPhpName('CcLoginAttempts');
        $this->setClassname('CcLoginAttempts');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('ip', 'DbIP', 'VARCHAR', true, 32, null);
        $this->addColumn('attempts', 'DbAttempts', 'INTEGER', false, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // CcLoginAttemptsTableMap
