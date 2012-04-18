<?php
abstract class Application_DbTable_Row extends Zend_Db_Table_Row
{
	/**
	 * @var Application_MultiDb
	 */
	protected $_multiDb;
	
	/**
	 * Saves the properties to the database.
	 *
	 * This performs an intelligent insert/update, and reloads the
	 * properties with fresh data from the table on success.
	 *
	 * @return mixed The primary key value(s), as an associative array if the
	 *     key is compound, or a scalar if the key is single-column.
	 */
	public function save()
	{
		$writer = $this->_getMultiDb()->getRandomWriteAdapter();
		$this->_getTable()->setAdapter($writer);
		return parent::save();
	}
	
	/**
	 * Retrieve the mutiple db adapter
	 *
	 * @return Application_MultiDb
	 */
	protected function _getMultiDb()
	{
		if (null === $this->_multiDb) {
			$this->_multiDb = Application_MultiDb::getInstance();
		}
		return $this->_multiDb;
	}
	
}