<?php



/**
 * This class defines the structure of the 'podcast_contents' table.
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
class PodcastContentsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.PodcastContentsTableMap';

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
        $this->setName('podcast_contents');
        $this->setPhpName('PodcastContents');
        $this->setClassname('PodcastContents');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('podcast_contents_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addForeignKey('file_id', 'DbFileId', 'INTEGER', 'cc_files', 'id', true, null, null);
        $this->addForeignKey('podcast_id', 'DbPodcastId', 'INTEGER', 'podcast', 'id', true, null, null);
        $this->addColumn('publication_date', 'DbPublicationDate', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Podcast', 'Podcast', RelationMap::MANY_TO_ONE, array('podcast_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // PodcastContentsTableMap
