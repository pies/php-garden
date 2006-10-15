<?php

define('_ROOT', dirname(dirname(__FILE__)));

define ('USE_DB_CONFIG', 'test');

require_once _ROOT.'/libs/_init.php';
require_once _ROOT.'/tools/simpletest/unit_tester.php';
require_once _ROOT.'/tools/simpletest/reporter.php';
require_once _ROOT.'/tools/pogo_test_case.php';

require(_ROOT.'/libs/items/item.php');

class StoredItemTestCase extends PogoTestCase {

	function setUp () {
		DB::execute('TRUNCATE TABLE page');
	}

	function tearDown () {
		DB::execute('TRUNCATE TABLE page');
	}

	function test_Sanity () {
		$this->assertClassExists('StoredItem');

		$item = new StoredItem('Page');
		$this->assertImplements($item, 'Create');
		$this->assertImplements($item, 'Read');
		$this->assertImplements($item, 'Update');
		$this->assertImplements($item, 'Delete');
	}

	function test_Create () {
		$item = new StoredItem('Page');

		$this->assertTrue( $item->Create() );

		$data = DB::find('page', 'id,created');
		$this->assertEqual( $data['id'], '1' );
		$T = array(time(), time()+1);
		$this->assertTrue( in_array($data['created'], $T) );
	}

	function test_Read () {
		$data = array('id'=>1, 'created'=>time(), 'a'=>123, 'b'=>456);
		DB::insert('page', $data);

		$item = new StoredItem('Page', 1);
		$R = $item->Read(array('id','created','a','b'));

		$this->assertEqual($R, $data);
	}

	function test_Update () {
		DB::insert('page', array('id'=>1, 'created'=>time(), 'a'=>123, 'b'=>456));

		$data = array('a'=>234, 'b'=>345);

		$item = new StoredItem('Page', 1);
		$this->assertTrue( $item->Update($data) );

		$R = DB::find('page', 'a,b', "id='1'");
		$this->assertEqual($R, $data);
	}

	function test_Delete () {
		DB::insert('page', array('id'=>1, 'created'=>time(), 'a'=>123, 'b'=>456));

		$item = new StoredItem('Page', 1);
		$this->assertTrue( $item->Delete() );

		$this->assertEqual(0, DB::count('page', "id='1'"));
	}

}

$test = &new GroupTest('Pogo Item TDD');
$test->addTestCase(new StoredItemTestCase('StoredItem'));
$test->run(new HtmlReporter());

?>