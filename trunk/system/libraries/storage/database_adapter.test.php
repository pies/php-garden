<?
/** 
 * Tests for DatabaseAdapter::Filter
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

class DatabaseAdapterTestCase extends PogoTestCase {

	function test_Sanity () {
		$this->assertClassExists('DatabaseAdapter');
		$this->assertAreDefined(
			'ATTR_IS',      'ATTR_IS_NOT',
			'ATTR_HAS',     'ATTR_HAS_NO',
			'ATTR_IN',      'ATTR_NOT_IN',
			'ATTR_BETWEEN', 'ATTR_OUTSIDE');
	}

	function test_Field () {
		$this->assertContains( DatabaseAdapter::Filter('id', ATTR_IS, '5'), "id = '5'");
		$this->assertContains( DatabaseAdapter::Filter(array('this_id','other_id'), ATTR_IS, '3'), "this_id = '3'", " OR ", "other_id = '3'");
	}

	function test_Relation () {
		$this->assertEqual( DatabaseAdapter::Filter('name',   ATTR_IS, 'foobar'), "(name = 'foobar')");
		$this->assertEqual( DatabaseAdapter::Filter('name',   ATTR_IS_NOT, 'foobar'), "(name <> 'foobar')");

		$this->assertEqual( DatabaseAdapter::Filter('name',   ATTR_HAS, 'bcd'),   "(name = '%bcd%')");
		$this->assertEqual( DatabaseAdapter::Filter('name',   ATTR_HAS_NO, 'bcd'), "(name <> '%bcd%')");

		$this->assertEqual( DatabaseAdapter::Filter('type',   ATTR_IN, array('foo','bar','baz')), "(type IN ('foo', 'bar', 'baz'))");
		$this->assertEqual( DatabaseAdapter::Filter('type',   ATTR_NOT_IN, array('foo','bar','baz')), "(type NOT IN ('foo', 'bar', 'baz'))");

		$this->assertEqual( DatabaseAdapter::Filter('volume', ATTR_BETWEEN, array( 1, 5 )), "(volume BETWEEN 1 AND 5)");
		$this->assertEqual( DatabaseAdapter::Filter('volume', ATTR_OUTSIDE, array( 0, 5 )), "(volume NOT BETWEEN 0 AND 5)");
	}

}

?>