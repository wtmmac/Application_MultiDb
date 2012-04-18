<?php 
require_once 'Zend/Loader/Autoloader.php';
require_once 'Application/MultiDb.php';

class Application_MultiDbTest extends PHPUnit_Framework_TestCase
{

	protected $_dbOptions = array(
		'db_master1' => array('adapter' => 'pdo_mysql', 'dbname' => 'db1', 'password' => 'XXXX', 'username' => 'webuser', 'write' => 'true', 'read' => 'true'),
		'db_master2' => array('adapter' => 'pdo_mysql', 'dbname' => 'db2', 'password' => 'XXXX', 'username' => 'webuser', 'write' => 'true', 'read' => 'true'),
		'db_slave1' => array('adapter' => 'pdo_pgsql', 'dbname' => 'db3', 'password' => 'notthatpublic', 'username' => 'dba', 'write' => 'false', 'read' => 'true')
	);
	
	
	/**
	 * the multidb adapter
	 * @var Application_MultiDb
	 */
	private $_multidb;
	
	
	public function setUp()
	{
		parent::setUp();
		
        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();
        $this->application = new Zend_Application('testing');
        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);
        $this->controller = Zend_Controller_Front::getInstance();
        
        $multidb_resource = new Zend_Application_Resource_Multidb(array());
        $multidb_resource->setBootstrap($this->bootstrap);
        $multidb_resource->setOptions($this->_dbOptions);
        $res = $multidb_resource->init();
        
        
        $this->_multidb = Application_MultiDb::getInstance();
        $this->_multidb->setMultiDbResource($multidb_resource);

	}
	
	public function tearDown()
	{
		unset($this->_multidb);
	}
	
	
	public function testInstanceType()
	{
		$this->assertInstanceOf('Application_MultiDb', $this->_multidb);
	}
	
	public function testGetDbAdapter()
	{
		$adapter = $this->_multidb->getDbAdapter('db_master1');
	
		$this->assertInstanceOf('Zend_Db_Adapter_Abstract', $adapter);
	
		$original_config = $adapter->getConfig();
		$this->assertEquals('db1', $original_config['dbname']);
	}
	
	
	public function testGetWriteAdapters()
	{
		$read = $this->_multidb->getRandomWriteAdapter();
		
		$this->assertInstanceOf('Zend_Db_Adapter_Abstract', $read);
		
		$original_config = $read->getConfig();
		$this->assertEquals('true', $original_config['write']);
	}
	
	
	public function testGetReadAdapters()
	{
		$read = $this->_multidb->getRandomReadAdapter();

		$this->assertInstanceOf('Zend_Db_Adapter_Abstract', $read);
		
		$original_config = $read->getConfig();
		$this->assertEquals('true', $original_config['read']);
	}
	
	
	public function testGetReadOnlyAdapters()
	{
		$read = $this->_multidb->getRandomReadOnlyAdapter();
	
		$this->assertInstanceOf('Zend_Db_Adapter_Abstract', $read);
	
		$original_config = $read->getConfig();
		$this->assertEquals('true', $original_config['read']);
		$this->assertEquals('false', $original_config['write']);
	}
	
}