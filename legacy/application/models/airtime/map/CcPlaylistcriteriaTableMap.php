<?php



/**
 * This class defines the structure of the 'cc_playlistcriteria' table.
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
class CcPlaylistcriteriaTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcPlaylistcriteriaTableMap';

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
		$this->setName('cc_playlistcriteria');
		$this->setPhpName('CcPlaylistcriteria');
		$this->setClassname('CcPlaylistcriteria');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_playlistcriteria_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('CRITERIA', 'DbCriteria', 'VARCHAR', true, 16, null);
		$this->addColumn('MODIFIER', 'DbModifier', 'VARCHAR', true, 16, null);
		$this->addColumn('VALUE', 'DbValue', 'VARCHAR', true, 512, null);
		$this->addColumn('EXTRA', 'DbExtra', 'VARCHAR', false, 512, null);
		$this->addForeignKey('PLAYLIST_ID', 'DbPlaylistId', 'INTEGER', 'cc_playlist', 'ID', true, null, null);
		$this->addColumn('SET_NUMBER', 'DbSetNumber', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcPlaylist', 'CcPlaylist', RelationMap::MANY_TO_ONE, array('playlist_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // CcPlaylistcriteriaTableMap
