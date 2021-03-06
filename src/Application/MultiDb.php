<?php
require_once 'Zend/Db.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Application/Resource/Multidb.php';

class Application_MultiDb
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;
    
    /**
     * Instance of a Multidb resource
     * 
     * @var Zend_Application_Resource_Multidb
     */
    protected $_multiDb;
    
    /**
     * Holds the MultiDb database keys orginized to groups (each group contains instances of Zend_Db_Table_Abstract)
     * 
     * @var array
     */
    protected $_dbs;
    
    /**
     * Singleton instance
     *
     * @var Application_MultiDb
     */
    protected static $_instance = null;
    
    
    /**
     * Retrieve a random read database connection (might be a read/write connection)
     * 
     * @return null|Zend_Db_Adapter_Abstract 
     */
    public function getRandomReadAdapter()
    {
        $dbs = $this->_getDbs();
        $key = $dbs['read'][array_rand($dbs['read'])];
        return $this->getDbAdapter($key);
    }
    
    /**
     * Retrieve a random read only database connection (replica connections)
     *  
     * @return null|Zend_Db_Adapter_Abstract
     */
    public function getRandomReadOnlyAdapter()
    {
    	$dbs = $this->_getDbs();
    	$key = $dbs['readonly'][array_rand($dbs['readonly'])];
    	return $this->getDbAdapter($key);
    }

    /**
     * Retrieve a random write database connection
     * 
     * @return null|Zend_Db_Adapter_Abstract 
     */
    public function getRandomWriteAdapter()
    {
        $dbs = $this->_getDbs();
        $key = $dbs['write'][array_rand($dbs['write'])];
        return $this->getDbAdapter($key);
    }
    
    /**
     * Retrieve a specified database connection
     * 
     * @param string $key
     * @return null|Zend_Db_Adapter_Abstract 
     */
    public function getDbAdapter($key)
    {
        return $this->_getMultiDbResource()->getDb($key);
    }
    
    /**
     * @return array
     */
    protected function _getDbs()
    {
        if (null === $this->_dbs) {
            $this->_dbs['read']  = array();
            $this->_dbs['readonly'] = array();
            $this->_dbs['write'] = array();
            foreach ($this->_getMultiDbResource()->getOptions() as $key => $options) {
                if (!isset($options['read']) || $options['read'] != 'false' || $options['read'] != 0) {
                    $this->_dbs['read'][] = $key;
                }
                if ((!isset($options['read']) || $options['read'] != 'false' || $options['read'] != 0) && ($options['write']=='false')) {
                	$this->_dbs['readonly'][] = $key;
                }
                if (!isset($options['write']) || $options['write'] != 'false' || $options['write'] != 0) {
                    $this->_dbs['write'][] = $key;
                }
            }
        }
                
        return $this->_dbs;
    }

    /**
     * Retrieve the MultiDb Application Resource instance
     * 
     * @return Zend_Application_Resource_Multidb
     */
    protected function _getMultiDbResource()
    {
        if (null === $this->_multiDb) {
            $this->_multiDb = $this->_getFrontController()->getParam('bootstrap')->getResource('multidb');
        }
        return $this->_multiDb;
    }
    
    /**
     * set the MultiDb Application Resource instance
     * 
     * @param Zend_Application_Resource_Multidb $resource
     */
    public function setMultiDbResource(Zend_Application_Resource_Multidb $resource)
    {
    	$this->_multiDb = $resource;
    }
    
    /**
     * Retrieve Front Controller instance
     *
     * @return Zend_Controller_Front
     */
    protected function _getFrontController()
    {
        if (null === $this->_front) {
            $this->_front = Zend_Controller_Front::getInstance();
        }
        return $this->_front;
    }
    
    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    protected function __clone()
    {}

    /**
     * Returns an instance of Leek_Multidb
     *
     * Singleton pattern implementation
     *
     * @return Leek_Multidb Provides a fluent interface
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}