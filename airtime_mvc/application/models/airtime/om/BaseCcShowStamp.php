<?php


/**
 * Base class that represents a row from the 'cc_show_stamp' table.
 *
 * 
 *
 * @package    propel.generator.airtime.om
 */
abstract class BaseCcShowStamp extends BaseObject  implements Persistent
{

	/**
	 * Peer class name
	 */
  const PEER = 'CcShowStampPeer';

	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CcShowStampPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the show_id field.
	 * @var        int
	 */
	protected $show_id;

	/**
	 * The value for the instance_id field.
	 * @var        int
	 */
	protected $instance_id;

	/**
	 * The value for the file_id field.
	 * @var        int
	 */
	protected $file_id;

	/**
	 * The value for the stream_id field.
	 * @var        int
	 */
	protected $stream_id;

	/**
	 * The value for the block_id field.
	 * @var        int
	 */
	protected $block_id;

	/**
	 * The value for the playlist_id field.
	 * @var        int
	 */
	protected $playlist_id;

	/**
	 * The value for the position field.
	 * @var        int
	 */
	protected $position;

	/**
	 * The value for the clip_length field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $clip_length;

	/**
	 * The value for the cue_in field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $cue_in;

	/**
	 * The value for the cue_out field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $cue_out;

	/**
	 * The value for the fade_in field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fade_in;

	/**
	 * The value for the fade_out field.
	 * Note: this column has a database default value of: '00:00:00'
	 * @var        string
	 */
	protected $fade_out;

	/**
	 * @var        CcShow
	 */
	protected $aCcShow;

	/**
	 * @var        CcShowInstances
	 */
	protected $aCcShowInstances;

	/**
	 * @var        CcFiles
	 */
	protected $aCcFiles;

	/**
	 * @var        CcWebstream
	 */
	protected $aCcWebstream;

	/**
	 * @var        CcBlock
	 */
	protected $aCcBlock;

	/**
	 * @var        CcPlaylist
	 */
	protected $aCcPlaylist;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->clip_length = '00:00:00';
		$this->cue_in = '00:00:00';
		$this->cue_out = '00:00:00';
		$this->fade_in = '00:00:00';
		$this->fade_out = '00:00:00';
	}

	/**
	 * Initializes internal state of BaseCcShowStamp object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getDbId()
	{
		return $this->id;
	}

	/**
	 * Get the [show_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbShowId()
	{
		return $this->show_id;
	}

	/**
	 * Get the [instance_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbInstanceId()
	{
		return $this->instance_id;
	}

	/**
	 * Get the [file_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbFileId()
	{
		return $this->file_id;
	}

	/**
	 * Get the [stream_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbStreamId()
	{
		return $this->stream_id;
	}

	/**
	 * Get the [block_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbBlockId()
	{
		return $this->block_id;
	}

	/**
	 * Get the [playlist_id] column value.
	 * 
	 * @return     int
	 */
	public function getDbPlaylistId()
	{
		return $this->playlist_id;
	}

	/**
	 * Get the [position] column value.
	 * 
	 * @return     int
	 */
	public function getDbPosition()
	{
		return $this->position;
	}

	/**
	 * Get the [clip_length] column value.
	 * 
	 * @return     string
	 */
	public function getDbClipLength()
	{
		return $this->clip_length;
	}

	/**
	 * Get the [cue_in] column value.
	 * 
	 * @return     string
	 */
	public function getDbCueIn()
	{
		return $this->cue_in;
	}

	/**
	 * Get the [cue_out] column value.
	 * 
	 * @return     string
	 */
	public function getDbCueOut()
	{
		return $this->cue_out;
	}

	/**
	 * Get the [fade_in] column value.
	 * 
	 * @return     string
	 */
	public function getDbFadeIn()
	{
		return $this->fade_in;
	}

	/**
	 * Get the [fade_out] column value.
	 * 
	 * @return     string
	 */
	public function getDbFadeOut()
	{
		return $this->fade_out;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::ID;
		}

		return $this;
	} // setDbId()

	/**
	 * Set the value of [show_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbShowId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->show_id !== $v) {
			$this->show_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::SHOW_ID;
		}

		if ($this->aCcShow !== null && $this->aCcShow->getDbId() !== $v) {
			$this->aCcShow = null;
		}

		return $this;
	} // setDbShowId()

	/**
	 * Set the value of [instance_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbInstanceId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->instance_id !== $v) {
			$this->instance_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::INSTANCE_ID;
		}

		if ($this->aCcShowInstances !== null && $this->aCcShowInstances->getDbId() !== $v) {
			$this->aCcShowInstances = null;
		}

		return $this;
	} // setDbInstanceId()

	/**
	 * Set the value of [file_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbFileId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->file_id !== $v) {
			$this->file_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::FILE_ID;
		}

		if ($this->aCcFiles !== null && $this->aCcFiles->getDbId() !== $v) {
			$this->aCcFiles = null;
		}

		return $this;
	} // setDbFileId()

	/**
	 * Set the value of [stream_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbStreamId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->stream_id !== $v) {
			$this->stream_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::STREAM_ID;
		}

		if ($this->aCcWebstream !== null && $this->aCcWebstream->getDbId() !== $v) {
			$this->aCcWebstream = null;
		}

		return $this;
	} // setDbStreamId()

	/**
	 * Set the value of [block_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbBlockId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->block_id !== $v) {
			$this->block_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::BLOCK_ID;
		}

		if ($this->aCcBlock !== null && $this->aCcBlock->getDbId() !== $v) {
			$this->aCcBlock = null;
		}

		return $this;
	} // setDbBlockId()

	/**
	 * Set the value of [playlist_id] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbPlaylistId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->playlist_id !== $v) {
			$this->playlist_id = $v;
			$this->modifiedColumns[] = CcShowStampPeer::PLAYLIST_ID;
		}

		if ($this->aCcPlaylist !== null && $this->aCcPlaylist->getDbId() !== $v) {
			$this->aCcPlaylist = null;
		}

		return $this;
	} // setDbPlaylistId()

	/**
	 * Set the value of [position] column.
	 * 
	 * @param      int $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbPosition($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->position !== $v) {
			$this->position = $v;
			$this->modifiedColumns[] = CcShowStampPeer::POSITION;
		}

		return $this;
	} // setDbPosition()

	/**
	 * Set the value of [clip_length] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbClipLength($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->clip_length !== $v || $this->isNew()) {
			$this->clip_length = $v;
			$this->modifiedColumns[] = CcShowStampPeer::CLIP_LENGTH;
		}

		return $this;
	} // setDbClipLength()

	/**
	 * Set the value of [cue_in] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbCueIn($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cue_in !== $v || $this->isNew()) {
			$this->cue_in = $v;
			$this->modifiedColumns[] = CcShowStampPeer::CUE_IN;
		}

		return $this;
	} // setDbCueIn()

	/**
	 * Set the value of [cue_out] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbCueOut($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->cue_out !== $v || $this->isNew()) {
			$this->cue_out = $v;
			$this->modifiedColumns[] = CcShowStampPeer::CUE_OUT;
		}

		return $this;
	} // setDbCueOut()

	/**
	 * Set the value of [fade_in] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbFadeIn($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fade_in !== $v || $this->isNew()) {
			$this->fade_in = $v;
			$this->modifiedColumns[] = CcShowStampPeer::FADE_IN;
		}

		return $this;
	} // setDbFadeIn()

	/**
	 * Set the value of [fade_out] column.
	 * 
	 * @param      string $v new value
	 * @return     CcShowStamp The current object (for fluent API support)
	 */
	public function setDbFadeOut($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->fade_out !== $v || $this->isNew()) {
			$this->fade_out = $v;
			$this->modifiedColumns[] = CcShowStampPeer::FADE_OUT;
		}

		return $this;
	} // setDbFadeOut()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->clip_length !== '00:00:00') {
				return false;
			}

			if ($this->cue_in !== '00:00:00') {
				return false;
			}

			if ($this->cue_out !== '00:00:00') {
				return false;
			}

			if ($this->fade_in !== '00:00:00') {
				return false;
			}

			if ($this->fade_out !== '00:00:00') {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->show_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->instance_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->file_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
			$this->stream_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->block_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->playlist_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->position = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->clip_length = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->cue_in = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->cue_out = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->fade_in = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->fade_out = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			return $startcol + 13; // 13 = CcShowStampPeer::NUM_COLUMNS - CcShowStampPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating CcShowStamp object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aCcShow !== null && $this->show_id !== $this->aCcShow->getDbId()) {
			$this->aCcShow = null;
		}
		if ($this->aCcShowInstances !== null && $this->instance_id !== $this->aCcShowInstances->getDbId()) {
			$this->aCcShowInstances = null;
		}
		if ($this->aCcFiles !== null && $this->file_id !== $this->aCcFiles->getDbId()) {
			$this->aCcFiles = null;
		}
		if ($this->aCcWebstream !== null && $this->stream_id !== $this->aCcWebstream->getDbId()) {
			$this->aCcWebstream = null;
		}
		if ($this->aCcBlock !== null && $this->block_id !== $this->aCcBlock->getDbId()) {
			$this->aCcBlock = null;
		}
		if ($this->aCcPlaylist !== null && $this->playlist_id !== $this->aCcPlaylist->getDbId()) {
			$this->aCcPlaylist = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CcShowStampPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aCcShow = null;
			$this->aCcShowInstances = null;
			$this->aCcFiles = null;
			$this->aCcWebstream = null;
			$this->aCcBlock = null;
			$this->aCcPlaylist = null;
		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				CcShowStampQuery::create()
					->filterByPrimaryKey($this->getPrimaryKey())
					->delete($con);
				$this->postDelete($con);
				$con->commit();
				$this->setDeleted(true);
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CcShowStampPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				CcShowStampPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aCcShow !== null) {
				if ($this->aCcShow->isModified() || $this->aCcShow->isNew()) {
					$affectedRows += $this->aCcShow->save($con);
				}
				$this->setCcShow($this->aCcShow);
			}

			if ($this->aCcShowInstances !== null) {
				if ($this->aCcShowInstances->isModified() || $this->aCcShowInstances->isNew()) {
					$affectedRows += $this->aCcShowInstances->save($con);
				}
				$this->setCcShowInstances($this->aCcShowInstances);
			}

			if ($this->aCcFiles !== null) {
				if ($this->aCcFiles->isModified() || $this->aCcFiles->isNew()) {
					$affectedRows += $this->aCcFiles->save($con);
				}
				$this->setCcFiles($this->aCcFiles);
			}

			if ($this->aCcWebstream !== null) {
				if ($this->aCcWebstream->isModified() || $this->aCcWebstream->isNew()) {
					$affectedRows += $this->aCcWebstream->save($con);
				}
				$this->setCcWebstream($this->aCcWebstream);
			}

			if ($this->aCcBlock !== null) {
				if ($this->aCcBlock->isModified() || $this->aCcBlock->isNew()) {
					$affectedRows += $this->aCcBlock->save($con);
				}
				$this->setCcBlock($this->aCcBlock);
			}

			if ($this->aCcPlaylist !== null) {
				if ($this->aCcPlaylist->isModified() || $this->aCcPlaylist->isNew()) {
					$affectedRows += $this->aCcPlaylist->save($con);
				}
				$this->setCcPlaylist($this->aCcPlaylist);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CcShowStampPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$criteria = $this->buildCriteria();
					if ($criteria->keyContainsValue(CcShowStampPeer::ID) ) {
						throw new PropelException('Cannot insert a value for auto-increment primary key ('.CcShowStampPeer::ID.')');
					}

					$pk = BasePeer::doInsert($criteria, $con);
					$affectedRows += 1;
					$this->setDbId($pk);  //[IMV] update autoincrement primary key
					$this->setNew(false);
				} else {
					$affectedRows += CcShowStampPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aCcShow !== null) {
				if (!$this->aCcShow->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcShow->getValidationFailures());
				}
			}

			if ($this->aCcShowInstances !== null) {
				if (!$this->aCcShowInstances->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcShowInstances->getValidationFailures());
				}
			}

			if ($this->aCcFiles !== null) {
				if (!$this->aCcFiles->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcFiles->getValidationFailures());
				}
			}

			if ($this->aCcWebstream !== null) {
				if (!$this->aCcWebstream->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcWebstream->getValidationFailures());
				}
			}

			if ($this->aCcBlock !== null) {
				if (!$this->aCcBlock->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcBlock->getValidationFailures());
				}
			}

			if ($this->aCcPlaylist !== null) {
				if (!$this->aCcPlaylist->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCcPlaylist->getValidationFailures());
				}
			}


			if (($retval = CcShowStampPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CcShowStampPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getDbId();
				break;
			case 1:
				return $this->getDbShowId();
				break;
			case 2:
				return $this->getDbInstanceId();
				break;
			case 3:
				return $this->getDbFileId();
				break;
			case 4:
				return $this->getDbStreamId();
				break;
			case 5:
				return $this->getDbBlockId();
				break;
			case 6:
				return $this->getDbPlaylistId();
				break;
			case 7:
				return $this->getDbPosition();
				break;
			case 8:
				return $this->getDbClipLength();
				break;
			case 9:
				return $this->getDbCueIn();
				break;
			case 10:
				return $this->getDbCueOut();
				break;
			case 11:
				return $this->getDbFadeIn();
				break;
			case 12:
				return $this->getDbFadeOut();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. 
	 *                    Defaults to BasePeer::TYPE_PHPNAME.
	 * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
	 * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
	 *
	 * @return    array an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $includeForeignObjects = false)
	{
		$keys = CcShowStampPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getDbId(),
			$keys[1] => $this->getDbShowId(),
			$keys[2] => $this->getDbInstanceId(),
			$keys[3] => $this->getDbFileId(),
			$keys[4] => $this->getDbStreamId(),
			$keys[5] => $this->getDbBlockId(),
			$keys[6] => $this->getDbPlaylistId(),
			$keys[7] => $this->getDbPosition(),
			$keys[8] => $this->getDbClipLength(),
			$keys[9] => $this->getDbCueIn(),
			$keys[10] => $this->getDbCueOut(),
			$keys[11] => $this->getDbFadeIn(),
			$keys[12] => $this->getDbFadeOut(),
		);
		if ($includeForeignObjects) {
			if (null !== $this->aCcShow) {
				$result['CcShow'] = $this->aCcShow->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcShowInstances) {
				$result['CcShowInstances'] = $this->aCcShowInstances->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcFiles) {
				$result['CcFiles'] = $this->aCcFiles->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcWebstream) {
				$result['CcWebstream'] = $this->aCcWebstream->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcBlock) {
				$result['CcBlock'] = $this->aCcBlock->toArray($keyType, $includeLazyLoadColumns, true);
			}
			if (null !== $this->aCcPlaylist) {
				$result['CcPlaylist'] = $this->aCcPlaylist->toArray($keyType, $includeLazyLoadColumns, true);
			}
		}
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CcShowStampPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setDbId($value);
				break;
			case 1:
				$this->setDbShowId($value);
				break;
			case 2:
				$this->setDbInstanceId($value);
				break;
			case 3:
				$this->setDbFileId($value);
				break;
			case 4:
				$this->setDbStreamId($value);
				break;
			case 5:
				$this->setDbBlockId($value);
				break;
			case 6:
				$this->setDbPlaylistId($value);
				break;
			case 7:
				$this->setDbPosition($value);
				break;
			case 8:
				$this->setDbClipLength($value);
				break;
			case 9:
				$this->setDbCueIn($value);
				break;
			case 10:
				$this->setDbCueOut($value);
				break;
			case 11:
				$this->setDbFadeIn($value);
				break;
			case 12:
				$this->setDbFadeOut($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = CcShowStampPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setDbId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setDbShowId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setDbInstanceId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setDbFileId($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setDbStreamId($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setDbBlockId($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDbPlaylistId($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setDbPosition($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setDbClipLength($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setDbCueIn($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setDbCueOut($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setDbFadeIn($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setDbFadeOut($arr[$keys[12]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CcShowStampPeer::DATABASE_NAME);

		if ($this->isColumnModified(CcShowStampPeer::ID)) $criteria->add(CcShowStampPeer::ID, $this->id);
		if ($this->isColumnModified(CcShowStampPeer::SHOW_ID)) $criteria->add(CcShowStampPeer::SHOW_ID, $this->show_id);
		if ($this->isColumnModified(CcShowStampPeer::INSTANCE_ID)) $criteria->add(CcShowStampPeer::INSTANCE_ID, $this->instance_id);
		if ($this->isColumnModified(CcShowStampPeer::FILE_ID)) $criteria->add(CcShowStampPeer::FILE_ID, $this->file_id);
		if ($this->isColumnModified(CcShowStampPeer::STREAM_ID)) $criteria->add(CcShowStampPeer::STREAM_ID, $this->stream_id);
		if ($this->isColumnModified(CcShowStampPeer::BLOCK_ID)) $criteria->add(CcShowStampPeer::BLOCK_ID, $this->block_id);
		if ($this->isColumnModified(CcShowStampPeer::PLAYLIST_ID)) $criteria->add(CcShowStampPeer::PLAYLIST_ID, $this->playlist_id);
		if ($this->isColumnModified(CcShowStampPeer::POSITION)) $criteria->add(CcShowStampPeer::POSITION, $this->position);
		if ($this->isColumnModified(CcShowStampPeer::CLIP_LENGTH)) $criteria->add(CcShowStampPeer::CLIP_LENGTH, $this->clip_length);
		if ($this->isColumnModified(CcShowStampPeer::CUE_IN)) $criteria->add(CcShowStampPeer::CUE_IN, $this->cue_in);
		if ($this->isColumnModified(CcShowStampPeer::CUE_OUT)) $criteria->add(CcShowStampPeer::CUE_OUT, $this->cue_out);
		if ($this->isColumnModified(CcShowStampPeer::FADE_IN)) $criteria->add(CcShowStampPeer::FADE_IN, $this->fade_in);
		if ($this->isColumnModified(CcShowStampPeer::FADE_OUT)) $criteria->add(CcShowStampPeer::FADE_OUT, $this->fade_out);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(CcShowStampPeer::DATABASE_NAME);
		$criteria->add(CcShowStampPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getDbId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setDbId($key);
	}

	/**
	 * Returns true if the primary key for this object is null.
	 * @return     boolean
	 */
	public function isPrimaryKeyNull()
	{
		return null === $this->getDbId();
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of CcShowStamp (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{
		$copyObj->setDbShowId($this->show_id);
		$copyObj->setDbInstanceId($this->instance_id);
		$copyObj->setDbFileId($this->file_id);
		$copyObj->setDbStreamId($this->stream_id);
		$copyObj->setDbBlockId($this->block_id);
		$copyObj->setDbPlaylistId($this->playlist_id);
		$copyObj->setDbPosition($this->position);
		$copyObj->setDbClipLength($this->clip_length);
		$copyObj->setDbCueIn($this->cue_in);
		$copyObj->setDbCueOut($this->cue_out);
		$copyObj->setDbFadeIn($this->fade_in);
		$copyObj->setDbFadeOut($this->fade_out);

		$copyObj->setNew(true);
		$copyObj->setDbId(NULL); // this is a auto-increment column, so set to default value
	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     CcShowStamp Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     CcShowStampPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CcShowStampPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a CcShow object.
	 *
	 * @param      CcShow $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcShow(CcShow $v = null)
	{
		if ($v === null) {
			$this->setDbShowId(NULL);
		} else {
			$this->setDbShowId($v->getDbId());
		}

		$this->aCcShow = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcShow object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcShow object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcShow The associated CcShow object.
	 * @throws     PropelException
	 */
	public function getCcShow(PropelPDO $con = null)
	{
		if ($this->aCcShow === null && ($this->show_id !== null)) {
			$this->aCcShow = CcShowQuery::create()->findPk($this->show_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcShow->addCcShowStamps($this);
			 */
		}
		return $this->aCcShow;
	}

	/**
	 * Declares an association between this object and a CcShowInstances object.
	 *
	 * @param      CcShowInstances $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcShowInstances(CcShowInstances $v = null)
	{
		if ($v === null) {
			$this->setDbInstanceId(NULL);
		} else {
			$this->setDbInstanceId($v->getDbId());
		}

		$this->aCcShowInstances = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcShowInstances object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcShowInstances object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcShowInstances The associated CcShowInstances object.
	 * @throws     PropelException
	 */
	public function getCcShowInstances(PropelPDO $con = null)
	{
		if ($this->aCcShowInstances === null && ($this->instance_id !== null)) {
			$this->aCcShowInstances = CcShowInstancesQuery::create()->findPk($this->instance_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcShowInstances->addCcShowStamps($this);
			 */
		}
		return $this->aCcShowInstances;
	}

	/**
	 * Declares an association between this object and a CcFiles object.
	 *
	 * @param      CcFiles $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcFiles(CcFiles $v = null)
	{
		if ($v === null) {
			$this->setDbFileId(NULL);
		} else {
			$this->setDbFileId($v->getDbId());
		}

		$this->aCcFiles = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcFiles object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcFiles object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcFiles The associated CcFiles object.
	 * @throws     PropelException
	 */
	public function getCcFiles(PropelPDO $con = null)
	{
		if ($this->aCcFiles === null && ($this->file_id !== null)) {
			$this->aCcFiles = CcFilesQuery::create()->findPk($this->file_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcFiles->addCcShowStamps($this);
			 */
		}
		return $this->aCcFiles;
	}

	/**
	 * Declares an association between this object and a CcWebstream object.
	 *
	 * @param      CcWebstream $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcWebstream(CcWebstream $v = null)
	{
		if ($v === null) {
			$this->setDbStreamId(NULL);
		} else {
			$this->setDbStreamId($v->getDbId());
		}

		$this->aCcWebstream = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcWebstream object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcWebstream object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcWebstream The associated CcWebstream object.
	 * @throws     PropelException
	 */
	public function getCcWebstream(PropelPDO $con = null)
	{
		if ($this->aCcWebstream === null && ($this->stream_id !== null)) {
			$this->aCcWebstream = CcWebstreamQuery::create()->findPk($this->stream_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcWebstream->addCcShowStamps($this);
			 */
		}
		return $this->aCcWebstream;
	}

	/**
	 * Declares an association between this object and a CcBlock object.
	 *
	 * @param      CcBlock $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcBlock(CcBlock $v = null)
	{
		if ($v === null) {
			$this->setDbBlockId(NULL);
		} else {
			$this->setDbBlockId($v->getDbId());
		}

		$this->aCcBlock = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcBlock object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcBlock object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcBlock The associated CcBlock object.
	 * @throws     PropelException
	 */
	public function getCcBlock(PropelPDO $con = null)
	{
		if ($this->aCcBlock === null && ($this->block_id !== null)) {
			$this->aCcBlock = CcBlockQuery::create()->findPk($this->block_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcBlock->addCcShowStamps($this);
			 */
		}
		return $this->aCcBlock;
	}

	/**
	 * Declares an association between this object and a CcPlaylist object.
	 *
	 * @param      CcPlaylist $v
	 * @return     CcShowStamp The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setCcPlaylist(CcPlaylist $v = null)
	{
		if ($v === null) {
			$this->setDbPlaylistId(NULL);
		} else {
			$this->setDbPlaylistId($v->getDbId());
		}

		$this->aCcPlaylist = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the CcPlaylist object, it will not be re-added.
		if ($v !== null) {
			$v->addCcShowStamp($this);
		}

		return $this;
	}


	/**
	 * Get the associated CcPlaylist object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     CcPlaylist The associated CcPlaylist object.
	 * @throws     PropelException
	 */
	public function getCcPlaylist(PropelPDO $con = null)
	{
		if ($this->aCcPlaylist === null && ($this->playlist_id !== null)) {
			$this->aCcPlaylist = CcPlaylistQuery::create()->findPk($this->playlist_id, $con);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aCcPlaylist->addCcShowStamps($this);
			 */
		}
		return $this->aCcPlaylist;
	}

	/**
	 * Clears the current object and sets all attributes to their default values
	 */
	public function clear()
	{
		$this->id = null;
		$this->show_id = null;
		$this->instance_id = null;
		$this->file_id = null;
		$this->stream_id = null;
		$this->block_id = null;
		$this->playlist_id = null;
		$this->position = null;
		$this->clip_length = null;
		$this->cue_in = null;
		$this->cue_out = null;
		$this->fade_in = null;
		$this->fade_out = null;
		$this->alreadyInSave = false;
		$this->alreadyInValidation = false;
		$this->clearAllReferences();
		$this->applyDefaultValues();
		$this->resetModified();
		$this->setNew(true);
		$this->setDeleted(false);
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

		$this->aCcShow = null;
		$this->aCcShowInstances = null;
		$this->aCcFiles = null;
		$this->aCcWebstream = null;
		$this->aCcBlock = null;
		$this->aCcPlaylist = null;
	}

	/**
	 * Catches calls to virtual methods
	 */
	public function __call($name, $params)
	{
		if (preg_match('/get(\w+)/', $name, $matches)) {
			$virtualColumn = $matches[1];
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
			// no lcfirst in php<5.3...
			$virtualColumn[0] = strtolower($virtualColumn[0]);
			if ($this->hasVirtualColumn($virtualColumn)) {
				return $this->getVirtualColumn($virtualColumn);
			}
		}
		throw new PropelException('Call to undefined method: ' . $name);
	}

} // BaseCcShowStamp
