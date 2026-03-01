<?php



/**
 * This class defines the structure of the 'cc_track_types' table.
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
class CcTracktypesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CcTracktypesTableMap';

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
        $this->setName('cc_track_types');
        $this->setPhpName('CcTracktypes');
        $this->setClassname('CcTracktypes');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('cc_track_types_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('code', 'DbCode', 'VARCHAR', true, 16, '');
        $this->addColumn('visibility', 'DbVisibility', 'BOOLEAN', true, null, true);
        $this->addColumn('type_name', 'DbTypeName', 'VARCHAR', true, 64, '');
        $this->addColumn('description', 'DbDescription', 'VARCHAR', true, 255, '');
        $this->addColumn('analyze_cue_points', 'DbAnalyzeCuePoints', 'BOOLEAN', true, null, false);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CcFiles', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'track_type_id', ), null, null, 'CcFiless');
    } // buildRelations()

} // CcTracktypesTableMap
