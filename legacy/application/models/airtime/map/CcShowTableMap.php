<?php



/**
 * This class defines the structure of the 'cc_show' table.
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
class CcShowTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcShowTableMap';

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
        $this->setName('cc_show');
        $this->setPhpName('CcShow');
        $this->setClassname('CcShow');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_show_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', true, 255, '');
        $this->addColumn('url', 'DbUrl', 'VARCHAR', false, 255, '');
        $this->addColumn('genre', 'DbGenre', 'VARCHAR', false, 255, '');
        $this->addColumn('description', 'DbDescription', 'VARCHAR', false, 8192, null);
        $this->addColumn('color', 'DbColor', 'VARCHAR', false, 6, null);
        $this->addColumn('background_color', 'DbBackgroundColor', 'VARCHAR', false, 6, null);
        $this->addColumn('live_stream_using_airtime_auth', 'DbLiveStreamUsingAirtimeAuth', 'BOOLEAN', false, null, false);
        $this->addColumn('live_stream_using_custom_auth', 'DbLiveStreamUsingCustomAuth', 'BOOLEAN', false, null, false);
        $this->addColumn('live_stream_user', 'DbLiveStreamUser', 'VARCHAR', false, 255, null);
        $this->addColumn('live_stream_pass', 'DbLiveStreamPass', 'VARCHAR', false, 255, null);
        $this->addColumn('linked', 'DbLinked', 'BOOLEAN', true, null, false);
        $this->addColumn('is_linkable', 'DbIsLinkable', 'BOOLEAN', true, null, true);
        $this->addColumn('image_path', 'DbImagePath', 'VARCHAR', false, 255, '');
        $this->addColumn('has_autoplaylist', 'DbHasAutoPlaylist', 'BOOLEAN', true, null, false);
        $this->addForeignKey('autoplaylist_id', 'DbAutoPlaylistId', 'INTEGER', 'cc_playlist', 'id', false, null, null);
        $this->addColumn('autoplaylist_repeat', 'DbAutoPlaylistRepeat', 'BOOLEAN', true, null, false);
        $this->addForeignKey('intro_playlist_id', 'DbIntroPlaylistId', 'INTEGER', 'cc_playlist', 'id', false, null, null);
        $this->addForeignKey('outro_playlist_id', 'DbOutroPlaylistId', 'INTEGER', 'cc_playlist', 'id', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcPlaylistRelatedByDbAutoPlaylistId', 'CcPlaylist', RelationMap::MANY_TO_ONE, array('autoplaylist_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CcPlaylistRelatedByDbIntroPlaylistId', 'CcPlaylist', RelationMap::MANY_TO_ONE, array('intro_playlist_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CcPlaylistRelatedByDbOutroPlaylistId', 'CcPlaylist', RelationMap::MANY_TO_ONE, array('outro_playlist_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null, 'CcShowInstancess');
        $this->addRelation('CcShowDays', 'CcShowDays', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null, 'CcShowDayss');
        $this->addRelation('CcShowRebroadcast', 'CcShowRebroadcast', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null, 'CcShowRebroadcasts');
        $this->addRelation('CcShowHosts', 'CcShowHosts', RelationMap::ONE_TO_MANY, array('id' => 'show_id', ), 'CASCADE', null, 'CcShowHostss');
    } // buildRelations()

} // CcShowTableMap
