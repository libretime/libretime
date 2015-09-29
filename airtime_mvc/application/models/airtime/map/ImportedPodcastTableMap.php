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
        $this->setUseIdGenerator(false);
        // columns
        $this->addColumn('url', 'DbUrl', 'VARCHAR', true, 4096, null);
        $this->addColumn('auto_ingest', 'DbAutoIngest', 'BOOLEAN', true, null, false);
        $this->addForeignPrimaryKey('id', 'DbId', 'INTEGER' , 'podcast', 'id', true, null, null);
        $this->addColumn('title', 'DbTitle', 'VARCHAR', true, 4096, null);
        $this->addColumn('creator', 'DbCreator', 'VARCHAR', false, 4096, null);
        $this->addColumn('description', 'DbDescription', 'VARCHAR', false, 4096, null);
        $this->addColumn('language', 'DbLanguage', 'VARCHAR', false, 4096, null);
        $this->addColumn('copyright', 'DbCopyright', 'VARCHAR', false, 4096, null);
        $this->addColumn('link', 'DbLink', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_author', 'DbItunesAuthor', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_keywords', 'DbItunesKeywords', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_summary', 'DbItunesSummary', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_subtitle', 'DbItunesSubtitle', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_category', 'DbItunesCategory', 'VARCHAR', false, 4096, null);
        $this->addColumn('itunes_explicit', 'DbItunesExplicit', 'VARCHAR', false, 4096, null);
        $this->addForeignKey('owner', 'DbOwner', 'INTEGER', 'cc_subjs', 'id', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Podcast', 'Podcast', RelationMap::MANY_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CcSubjs', 'CcSubjs', RelationMap::MANY_TO_ONE, array('owner' => 'id', ), 'CASCADE', null);
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
  'extends' => 'podcast',
  'descendant_column' => 'descendant_class',
  'copy_data_to_parent' => 'true',
  'schema' => '',
  'excluded_parent_behavior' => 'nested_set',
),
        );
    } // getBehaviors()

} // ImportedPodcastTableMap
