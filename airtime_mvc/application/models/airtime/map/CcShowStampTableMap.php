<?php



/**
 * This class defines the structure of the 'cc_show_stamp' table.
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
class CcShowStampTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcShowStampTableMap';

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
		$this->setName('cc_show_stamp');
		$this->setPhpName('CcShowStamp');
		$this->setClassname('CcShowStamp');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_show_stamp_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addForeignKey('SHOW_ID', 'DbShowId', 'INTEGER', 'cc_show', 'ID', true, null, null);
		$this->addForeignKey('INSTANCE_ID', 'DbInstanceId', 'INTEGER', 'cc_show_instances', 'ID', false, null, null);
		$this->addForeignKey('FILE_ID', 'DbFileId', 'INTEGER', 'cc_files', 'ID', false, null, null);
		$this->addForeignKey('STREAM_ID', 'DbStreamId', 'INTEGER', 'cc_webstream', 'ID', false, null, null);
		$this->addForeignKey('BLOCK_ID', 'DbBlockId', 'INTEGER', 'cc_block', 'ID', false, null, null);
		$this->addForeignKey('PLAYLIST_ID', 'DbPlaylistId', 'INTEGER', 'cc_playlist', 'ID', false, null, null);
		$this->addColumn('POSITION', 'DbPosition', 'INTEGER', true, null, null);
		$this->addColumn('CLIP_LENGTH', 'DbClipLength', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('CUE_IN', 'DbCueIn', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('CUE_OUT', 'DbCueOut', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('FADE_IN', 'DbFadeIn', 'VARCHAR', false, null, '00:00:00');
		$this->addColumn('FADE_OUT', 'DbFadeOut', 'VARCHAR', false, null, '00:00:00');
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcShow', 'CcShow', RelationMap::MANY_TO_ONE, array('show_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcShowInstances', 'CcShowInstances', RelationMap::MANY_TO_ONE, array('instance_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcFiles', 'CcFiles', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcWebstream', 'CcWebstream', RelationMap::MANY_TO_ONE, array('stream_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcBlock', 'CcBlock', RelationMap::MANY_TO_ONE, array('block_id' => 'id', ), 'CASCADE', null);
    $this->addRelation('CcPlaylist', 'CcPlaylist', RelationMap::MANY_TO_ONE, array('playlist_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcShowStampTableMap
