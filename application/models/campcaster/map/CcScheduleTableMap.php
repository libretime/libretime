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
 * @package    propel.generator.campcaster.map
 */
class CcScheduleTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'campcaster.map.CcScheduleTableMap';

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
		$this->setPackage('campcaster');
		$this->setUseIdGenerator(false);
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'BIGINT', true, null, null);
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
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

} // CcScheduleTableMap
