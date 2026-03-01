<?php



/**
 * This class defines the structure of the 'cc_blockcontents' table.
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
class CcBlockcontentsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcBlockcontentsTableMap';

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
        $this->setName('cc_blockcontents');
        $this->setPhpName('CcBlockcontents');
        $this->setClassname('CcBlockcontents');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_blockcontents_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addForeignKey('block_id', 'DbBlockId', 'INTEGER', 'cc_block', 'id', false, null, null);
        $this->addForeignKey('file_id', 'DbFileId', 'INTEGER', 'cc_files', 'id', false, null, null);
        $this->addColumn('position', 'DbPosition', 'INTEGER', false, null, null);
        $this->addColumn('trackoffset', 'DbTrackOffset', 'REAL', true, null, 0);
        $this->addColumn('cliplength', 'DbCliplength', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cuein', 'DbCuein', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cueout', 'DbCueout', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('fadein', 'DbFadein', 'TIME', false, null, '00:00:00');
        $this->addColumn('fadeout', 'DbFadeout', 'TIME', false, null, '00:00:00');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcBlock', 'CcBlock', RelationMap::MANY_TO_ONE, array('block_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'aggregate_column_relation' =>  array (
  'foreign_table' => 'cc_block',
  'update_method' => 'updateDbLength',
),
        );
    } // getBehaviors()

} // CcBlockcontentsTableMap
