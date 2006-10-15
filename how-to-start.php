<?php
/**
 * Database package loader
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Database
 * @version $Revision: 15 $
 * @license Public Domain (see system/licence.txt)
*/

require('system/init.php');

class Website extends Garden {};



interface DatabaseDriverApi
{
	public function query ($sql);
};

interface DatabaseResultsetApi
{
	function getCount();
	function getRow();
};





try {

	class Database1 extends Database {
		protected 
		$config = array(
			'driver'   => 'mysql',
			'host'     => 'localhost',
			'login'    => 'www',
			'password' => 'www',
			'database' => 'domkultury',
			);
	}
	
	$DB = new Database1();

} 
catch (Exception $E) { 
	print_r($E);
}


/*

class Users extends DataItems {};

$users = Users::having($data);
$user = Data::user( new Rule('id', IS, $id) );
$user = Data::User( Data::Condition('id', IS, $id) )

*/

?>