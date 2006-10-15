<?php
/**
 * Database
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Database
 * @version $Revision: 16 $
 * @license Public Domain (see system/licence.txt)
*/

class Database extends DatabaseDriver 
{
}

interface DatabaseDriverApi
{
	public function query ($sql);
};

class DatabaseError extends GardenError
{
	protected $module   = Garden::MODULE_DATABASE;
	protected $priority = Garden::PRIORITY_HIGH;
};

?>