<?php



/**
 * This class defines the structure of the 'celery_tasks' table.
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
class CeleryTasksTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.CeleryTasksTableMap';

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
        $this->setName('celery_tasks');
        $this->setPhpName('CeleryTasks');
        $this->setClassname('CeleryTasks');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('celery_tasks_id_seq');
        // columns
        $this->addPrimaryKey('id', 'DbId', 'INTEGER', true, null, null);
        $this->addColumn('task_id', 'DbTaskId', 'VARCHAR', true, 256, null);
        $this->addForeignKey('track_reference', 'DbTrackReference', 'INTEGER', 'third_party_track_references', 'id', true, null, null);
        $this->addColumn('name', 'DbName', 'VARCHAR', false, 256, null);
        $this->addColumn('dispatch_time', 'DbDispatchTime', 'TIMESTAMP', false, null, null);
        $this->addColumn('status', 'DbStatus', 'VARCHAR', true, 256, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('ThirdPartyTrackReferences', 'ThirdPartyTrackReferences', RelationMap::MANY_TO_ONE, array('track_reference' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // CeleryTasksTableMap
