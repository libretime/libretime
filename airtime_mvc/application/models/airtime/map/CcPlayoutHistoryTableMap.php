<?php



/**
 * This class defines the structure of the 'cc_playout_history' table.
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
class CcPlayoutHistoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcPlayoutHistoryTableMap';

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
        $this->setName('cc_playout_history');
        $this->setPhpName('CcPlayoutHistory');
        $this->setClassname('CcPlayoutHistory');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_playout_history_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addForeignKey('file_id', 'DbFileId', 'INTEGER', 'cc_files', 'id', false, null, null);
        $this->addColumn('starts', 'DbStarts', 'TIMESTAMP', true, null, null);
        $this->addColumn('ends', 'DbEnds', 'TIMESTAMP', false, null, null);
        $this->addForeignKey('instance_id', 'DbInstanceId', 'INTEGER', 'cc_show_instances', 'id', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CcPlayoutHistoryMetaData', 'CcPlayoutHistoryMetaData', RelationMap::ONE_TO_MANY, array('id' => 'history_id', ), 'CASCADE', null, 'CcPlayoutHistoryMetaDatas');
    } // buildRelations()

} // CcPlayoutHistoryTableMap
