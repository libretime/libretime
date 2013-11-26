<?php

namespace Airtime\MediaItem\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'media_audiofile' table.
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
class AudioFileTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.AudioFileTableMap';

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
        $this->setName('media_audiofile');
        $this->setPhpName('AudioFile');
        $this->setClassname('Airtime\\MediaItem\\AudioFile');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignKey('directory', 'Directory', 'INTEGER', 'cc_music_dirs', 'id', false, null, null);
        $this->addColumn('filepath', 'Filepath', 'LONGVARCHAR', false, null, '');
        $this->addColumn('md5', 'Md5', 'CHAR', false, 32, null);
        $this->addColumn('track_title', 'TrackTitle', 'VARCHAR', false, 512, null);
        $this->addColumn('artist_name', 'ArtistName', 'VARCHAR', false, 512, null);
        $this->addColumn('bit_rate', 'BitRate', 'INTEGER', false, null, null);
        $this->addColumn('sample_rate', 'SampleRate', 'INTEGER', false, null, null);
        $this->addColumn('album_title', 'AlbumTitle', 'VARCHAR', false, 512, null);
        $this->addColumn('genre', 'Genre', 'VARCHAR', false, 64, null);
        $this->addColumn('comments', 'Comments', 'LONGVARCHAR', false, null, null);
        $this->addColumn('year', 'Year', 'VARCHAR', false, 16, null);
        $this->addColumn('track_number', 'TrackNumber', 'INTEGER', false, null, null);
        $this->addColumn('channels', 'Channels', 'INTEGER', false, null, null);
        $this->addColumn('bpm', 'Bpm', 'INTEGER', false, null, null);
        $this->addColumn('encoded_by', 'EncodedBy', 'VARCHAR', false, 255, null);
        $this->addColumn('mood', 'Mood', 'VARCHAR', false, 64, null);
        $this->addColumn('label', 'Label', 'VARCHAR', false, 512, null);
        $this->addColumn('composer', 'Composer', 'VARCHAR', false, 512, null);
        $this->addColumn('copyright', 'Copyright', 'VARCHAR', false, 512, null);
        $this->addColumn('conductor', 'Conductor', 'VARCHAR', false, 512, null);
        $this->addColumn('isrc_number', 'IsrcNumber', 'VARCHAR', false, 512, null);
        $this->addColumn('info_url', 'InfoUrl', 'VARCHAR', false, 512, null);
        $this->addColumn('language', 'Language', 'VARCHAR', false, 512, null);
        $this->addColumn('replay_gain', 'ReplayGain', 'NUMERIC', false, null, null);
        $this->addColumn('cuein', 'Cuein', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('cueout', 'Cueout', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('silan_check', 'IsSilanChecked', 'BOOLEAN', false, null, false);
        $this->addColumn('file_exists', 'FileExists', 'BOOLEAN', false, null, true);
        $this->addColumn('hidden', 'FileHidden', 'BOOLEAN', false, null, false);
        $this->addColumn('is_scheduled', 'IsScheduled', 'BOOLEAN', false, null, false);
        $this->addColumn('is_playlist', 'IsPlaylist', 'BOOLEAN', false, null, false);
        $this->addForeignPrimaryKey('id', 'Id', 'INTEGER' , 'media_item', 'id', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', false, 128, null);
        $this->addForeignKey('owner_id', 'OwnerId', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 512, null);
        $this->addColumn('last_played', 'LastPlayedTime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('play_count', 'PlayCount', 'INTEGER', false, null, 0);
        $this->addColumn('length', 'Length', 'VARCHAR', false, null, '00:00:00');
        $this->addColumn('mime', 'Mime', 'VARCHAR', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcMusicDirs', 'Airtime\\CcMusicDirs', RelationMap::MANY_TO_ONE, array('directory' => 'id', ), null, null);
        $this->addRelation('MediaItem', 'Airtime\\MediaItem', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcSubjs', 'Airtime\\CcSubjs', RelationMap::MANY_TO_ONE, array('owner_id' => 'id', ), null, null);
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
            'concrete_inheritance' =>  array (
  'extends' => 'media_item',
  'descendant_column' => 'descendant_class',
  'copy_data_to_parent' => 'true',
  'schema' => '',
  'excluded_parent_behavior' => 'nested_set',
),
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
        );
    } // getBehaviors()

} // AudioFileTableMap
