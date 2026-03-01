<?php



/**
 * This class defines the structure of the 'third_party_track_references' table.
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
class ThirdPartyTrackReferencesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.ThirdPartyTrackReferencesTableMap';

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
        $this->setName('third_party_track_references');
        $this->setPhpName('ThirdPartyTrackReferences');
        $this->setClassname('ThirdPartyTrackReferences');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('third_party_track_references_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('service', 'DbService', 'VARCHAR', true, 256, null);
        $this->addColumn('foreign_id', 'DbForeignId', 'VARCHAR', false, 256, null);
        $this->addForeignKey('file_id', 'DbFileId', 'INTEGER', 'cc_files', 'id', false, null, 0);
        $this->addColumn('upload_time', 'DbUploadTime', 'TIMESTAMP', false, null, null);
        $this->addColumn('status', 'DbStatus', 'VARCHAR', false, 256, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CeleryTasks', 'CeleryTasks', RelationMap::ONE_TO_MANY, array('id' => 'track_reference', ), 'CASCADE', null, 'CeleryTaskss');
    } // buildRelations()

} // ThirdPartyTrackReferencesTableMap
