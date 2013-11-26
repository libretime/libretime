<?php

namespace Airtime\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'media_item' table.
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
class MediaItemTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.MediaItemTableMap';

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
        $this->setName('media_item');
        $this->setPhpName('MediaItem');
        $this->setClassname('Airtime\\MediaItem');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('media_item_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', false, 128, null);
        $this->addForeignKey('owner_id', 'OwnerId', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 512, null);
        $this->addColumn('last_played', 'LastPlayedTime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('play_count', 'PlayCount', 'INTEGER', false, null, 0);
        $this->addColumn('length', 'Length', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('mime', 'Mime', 'VARCHAR', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('descendant_class', 'DescendantClass', 'VARCHAR', false, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcSubjs', 'Airtime\\CcSubjs', RelationMap::MANY_TO_ONE, array('owner_id' => 'id', ), null, null);
        $this->addRelation('CcShowInstances', 'Airtime\\CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'media_id', ), 'CASCADE', null, 'CcShowInstancess');
        $this->addRelation('CcSchedule', 'Airtime\\CcSchedule', RelationMap::ONE_TO_MANY, array('id' => 'media_id', ), 'CASCADE', null, 'CcSchedules');
        $this->addRelation('PlaylistRule', 'Airtime\\MediaItem\\PlaylistRule', RelationMap::ONE_TO_MANY, array('id' => 'media_id', ), 'CASCADE', null, 'PlaylistRules');
        $this->addRelation('MediaContent', 'Airtime\\MediaItem\\MediaContent', RelationMap::ONE_TO_MANY, array('id' => 'media_id', ), 'CASCADE', null, 'MediaContents');
        $this->addRelation('AudioFile', 'Airtime\\MediaItem\\AudioFile', RelationMap::ONE_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Webstream', 'Airtime\\MediaItem\\Webstream', RelationMap::ONE_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Playlist', 'Airtime\\MediaItem\\Playlist', RelationMap::ONE_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
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
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
            'concrete_inheritance_parent' =>  array (
  'descendant_column' => 'descendant_class',
),
        );
    } // getBehaviors()

} // MediaItemTableMap
