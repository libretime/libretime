<?php



/**
 * This class defines the structure of the 'cc_subjs' table.
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
class CcSubjsTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'airtime.map.CcSubjsTableMap';

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
		$this->setName('cc_subjs');
		$this->setPhpName('CcSubjs');
		$this->setClassname('CcSubjs');
		$this->setPackage('airtime');
		$this->setUseIdGenerator(true);
		$this->setPrimaryKeyMethodInfo('cc_subjs_id_seq');
		// columns
		$this->addPrimaryKey('ID', 'DbId', 'INTEGER', true, null, null);
		$this->addColumn('LOGIN', 'DbLogin', 'VARCHAR', true, 255, '');
		$this->addColumn('PASS', 'DbPass', 'VARCHAR', true, 255, '');
		$this->addColumn('TYPE', 'DbType', 'CHAR', true, 1, 'U');
		$this->addColumn('FIRST_NAME', 'DbFirstName', 'VARCHAR', true, 255, '');
		$this->addColumn('LAST_NAME', 'DbLastName', 'VARCHAR', true, 255, '');
		$this->addColumn('LASTLOGIN', 'DbLastlogin', 'TIMESTAMP', false, null, null);
		$this->addColumn('LASTFAIL', 'DbLastfail', 'TIMESTAMP', false, null, null);
		$this->addColumn('SKYPE_CONTACT', 'DbSkypeContact', 'VARCHAR', false, 255, null);
		$this->addColumn('JABBER_CONTACT', 'DbJabberContact', 'VARCHAR', false, 255, null);
		$this->addColumn('EMAIL', 'DbEmail', 'VARCHAR', false, 255, null);
		$this->addColumn('CELL_PHONE', 'DbCellPhone', 'VARCHAR', false, 255, null);
		$this->addColumn('LOGIN_ATTEMPTS', 'DbLoginAttempts', 'INTEGER', false, null, 0);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('CcFilesRelatedByDbOwnerId', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'owner_id', ), null, null);
    $this->addRelation('CcFilesRelatedByDbEditedby', 'CcFiles', RelationMap::ONE_TO_MANY, array('id' => 'editedby', ), null, null);
    $this->addRelation('CcPerms', 'CcPerms', RelationMap::ONE_TO_MANY, array('id' => 'subj', ), 'CASCADE', null);
    $this->addRelation('CcShowHosts', 'CcShowHosts', RelationMap::ONE_TO_MANY, array('id' => 'subjs_id', ), 'CASCADE', null);
    $this->addRelation('CcPlaylist', 'CcPlaylist', RelationMap::ONE_TO_MANY, array('id' => 'creator_id', ), 'CASCADE', null);
    $this->addRelation('CcBlock', 'CcBlock', RelationMap::ONE_TO_MANY, array('id' => 'creator_id', ), 'CASCADE', null);
    $this->addRelation('CcPref', 'CcPref', RelationMap::ONE_TO_MANY, array('id' => 'subjid', ), 'CASCADE', null);
    $this->addRelation('CcSess', 'CcSess', RelationMap::ONE_TO_MANY, array('id' => 'userid', ), 'CASCADE', null);
    $this->addRelation('CcSubjsToken', 'CcSubjsToken', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null);
	} // buildRelations()

} // CcSubjsTableMap
