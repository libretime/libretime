<?php



/**
 * This class defines the structure of the 'cc_playlist' table.
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
class CcPlaylistTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcPlaylistTableMap';

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
        $this->setName('cc_playlist');
        $this->setPhpName('CcPlaylist');
        $this->setClassname('CcPlaylist');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_playlist_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', true, 255, '');
        $this->addColumn('mtime', 'DbMtime', 'TIMESTAMP', false, 6, null);
        $this->addColumn('utime', 'DbUtime', 'TIMESTAMP', false, 6, null);
        $this->addForeignKey('creator_id', 'DbCreatorId', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        $this->addColumn('description', 'DbDescription', 'VARCHAR', false, 512, null);
        $this->addColumn('length', 'DbLength', 'VARCHAR', false, null, '00:00:00');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('creator_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcShow', 'CcShow', RelationMap::ONE_TO_MANY, array('id' => 'autoplaylist_id', ), 'SET NULL', null, 'CcShows');
        $this->addRelation('CcShow', 'CcShow', RelationMap::ONE_TO_MANY, array('id' => 'intro_playlist_id', ), 'SET NULL', null, 'CcShows');
        $this->addRelation('CcShow', 'CcShow', RelationMap::ONE_TO_MANY, array('id' => 'outro_playlist_id', ), 'SET NULL', null, 'CcShows');
        $this->addRelation('CcPlaylistcontents', 'CcPlaylistcontents', RelationMap::ONE_TO_MANY, array('id' => 'playlist_id', ), 'CASCADE', null, 'CcPlaylistcontentss');
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
            'aggregate_column' =>  array (
  'name' => 'length',
  'expression' => 'SUM(cliplength)',
  'condition' => NULL,
  'foreign_table' => 'cc_playlistcontents',
  'foreign_schema' => NULL,
),
        );
    } // getBehaviors()

} // CcPlaylistTableMap
