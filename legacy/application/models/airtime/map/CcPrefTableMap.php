<?php



/**
 * This class defines the structure of the 'cc_pref' table.
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
class CcPrefTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcPrefTableMap';

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
        $this->setName('cc_pref');
        $this->setPhpName('CcPref');
        $this->setClassname('CcPref');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_pref_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('subjid', 'Subjid', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('keystr', 'Keystr', 'VARCHAR', false, 255, null);
        $this->addColumn('valstr', 'Valstr', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('subjid' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CcPrefTableMap
