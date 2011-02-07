<?php



/**
 * This class defines the structure of the 'cc_schedule' table.
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
class CcScheduleTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcScheduleTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('cc_schedule');
		$this->setPhpName('CcSchedule');
		$this->setClassname('CcSchedule');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_schedule_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('PLAYLIST_ID', 'DbPlaylistId', 'INTEGER', true, null, null);
		$this->addColumn('STARTS', 'DbStarts', 'TIMESTAMP', true, null, null);
		$this->addColumn('ENDS', 'DbEnds', 'TIMESTAMP', true, null, null);
		$this->addColumn('GROUP_ID', 'DbGroupId', 'INTEGER', false, null, null);
		$this->addColumn('FILE_ID', 'DbFileId', 'INTEGER', false, null, null);
		$this->addColumn('CLIP_LENGTH', 'DbClipLength', 'TIME', false, null, '00:00:00');
		$this->addColumn('FADE_IN', 'DbFadeIn', 'TIME', false, null, '00:00:00');
		$this->addColumn('FADE_OUT', 'DbFadeOut', 'TIME', false, null, '00:00:00');
		$this->addColumn('CUE_IN', 'DbCueIn', 'TIME', false, null, '00:00:00');
		$this->addColumn('CUE_OUT', 'DbCueOut', 'TIME', false, null, '00:00:00');
		$this->addColumn('SCHEDULE_GROUP_PLAYED', 'DbScheduleGroupPlayed', 'BOOLEAN', false, null, false);
		$this->addColumn('MEDIA_ITEM_PLAYED', 'DbMediaItemPlayed', 'BOOLEAN', false, null, false);
		$this->addForeignKey('INSTANCE_ID', 'DbInstanceId', 'INTEGER', 'cc_show_instances', 'ID', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcScheduleTableMap
