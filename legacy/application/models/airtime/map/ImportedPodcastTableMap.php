<?php



/**
 * This class defines the structure of the 'imported_podcast' table.
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
class ImportedPodcastTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.ImportedPodcastTableMap';

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
        $this->setName('imported_podcast');
        $this->setPhpName('ImportedPodcast');
        $this->setClassname('ImportedPodcast');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('imported_podcast_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('auto_ingest', 'DbAutoIngest', 'BOOLEAN', true, null, false);
        $this->addColumn('auto_ingest_timestamp', 'DbAutoIngestTimestamp', 'TIMESTAMP', false, null, null);
        $this->addColumn('album_override', 'DbAlbumOverride', 'BOOLEAN', true, null, false);
        $this->addForeignKey('podcast_id', 'DbPodcastId', 'INTEGER', 'podcast', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Podcast', 'Podcast', RelationMap::MANY_TO_ONE, array('podcast_id' => 'id', ), 'CASCADE', null);
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
            'delegate' =>  array (
  'to' => 'podcast',
),
        );
    } // getBehaviors()

} // ImportedPodcastTableMap
