<?php



/**
 * This class defines the structure of the 'cc_webstream_metadata' table.
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
class CcWebstreamMetadataTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcWebstreamMetadataTableMap';

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
        $this->setName('cc_webstream_metadata');
        $this->setPhpName('CcWebstreamMetadata');
        $this->setClassname('CcWebstreamMetadata');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_webstream_metadata_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addForeignKey('instance_id', 'DbInstanceId', 'INTEGER', 'cc_schedule', 'id', true, null, null);
        $this->addColumn('start_time', 'DbStartTime', 'TIMESTAMP', true, null, null);
        $this->addColumn('liquidsoap_data', 'DbLiquidsoapData', 'VARCHAR', true, 1024, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcSchedule', 'CcSchedule', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CcWebstreamMetadataTableMap
