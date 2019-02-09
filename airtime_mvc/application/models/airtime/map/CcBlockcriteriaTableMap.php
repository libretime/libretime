<?php



/**
 * This class defines the structure of the 'cc_blockcriteria' table.
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
class CcBlockcriteriaTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcBlockcriteriaTableMap';

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
        $this->setName('cc_blockcriteria');
        $this->setPhpName('CcBlockcriteria');
        $this->setClassname('CcBlockcriteria');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_blockcriteria_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('criteria', 'DbCriteria', 'VARCHAR', true, 32, null);
        $this->addColumn('modifier', 'DbModifier', 'VARCHAR', true, 16, null);
        $this->addColumn('value', 'DbValue', 'VARCHAR', true, 512, null);
        $this->addColumn('extra', 'DbExtra', 'VARCHAR', false, 512, null);
        $this->addColumn('criteriagroup', 'DbCriteriaGroup', 'INTEGER', false, null, null);
        $this->addForeignKey('block_id', 'DbBlockId', 'INTEGER', 'cc_block', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcBlock', 'CcBlock', RelationMap::MANY_TO_ONE, array('block_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CcBlockcriteriaTableMap
