<?php
abstract class Application_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * Classname for row
	 *
	 * @var string
	 */
	protected $_rowClass = 'Application_Db_Table_Row';
	
	/**
	 * @var Application_MultiDb
	 */
	protected $_multiDb;
	
	/**
	 * Returns an instance of a Zend_Db_Table_Select object.
	 *
	 * @param bool $withFromPart Whether or not to include the from part of the select based on the table
	 * @return Zend_Db_Table_Select
	 */
	public function slaveSelect($withFromPart = self::SELECT_WITHOUT_FROM_PART)
	{
		$reader = $this->_getMultiDb()->getRandomReadOnlyAdapter();
		$this->_setAdapter($reader);
		return parent::select($withFromPart);
	}
	
	/**
	 * Retrieve Front Controller instance
	 *
	 * @return Zend_Controller_Front
	 */
	protected function _getMultiDb()
	{
		if (null === $this->_multiDb) {
			$this->_multiDb = Application_MultiDb::getInstance();
		}
		return $this->_multiDb;
	}
	
}