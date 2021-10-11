<?php



/**
 * This class defines the structure of the 'cc_schedule' table.
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
class CcScheduleTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcScheduleTableMap';

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
        $this->setName('cc_schedule');
        $this->setPhpName('CcSchedule');
        $this->setClassname('CcSchedule');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_schedule_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('starts', 'DbStarts', 'TIMESTAMP', true, null, null);
        $this->addColumn('ends', 'DbEnds', 'TIMESTAMP', true, null, null);
        $this->addForeignKey('file_id', 'DbFileId', 'INTEGER', 'cc_files', 'id', false, null, null);
        $this->addForeignKey('stream_id', 'DbStreamId', 'INTEGER', 'cc_webstream', 'id', false, null, null);
        $this->addColumn('clip_length', 'DbClipLength', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('fade_in', 'DbFadeIn', 'TIME', false, null, '00:00:00');
        $this->addColumn('fade_out', 'DbFadeOut', 'TIME', false, null, '00:00:00');
        $this->addColumn('cue_in', 'DbCueIn', 'VARCHAR', true, null, null);
        $this->addColumn('cue_out', 'DbCueOut', 'VARCHAR', true, null, null);
        $this->addColumn('media_item_played', 'DbMediaItemPlayed', 'BOOLEAN', false, null, false);
        $this->addForeignKey('instance_id', 'DbInstanceId', 'INTEGER', 'cc_show_instances', 'id', true, null, null);
        $this->addColumn('playout_status', 'DbPlayoutStatus', 'SMALLINT', true, null, 1);
        $this->addColumn('broadcasted', 'DbBroadcasted', 'SMALLINT', true, null, 0);
        $this->addColumn('position', 'DbPosition', 'INTEGER', true, null, 0);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcWebstream', 'CcWebstream', RelationMap::MANY_TO_ONE, array('stream_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcWebstreamMetadata', 'CcWebstreamMetadata', RelationMap::ONE_TO_MANY, array('id' => 'instance_id', ), 'CASCADE', null, 'CcWebstreamMetadatas');
    } // buildRelations()

} // CcScheduleTableMap
