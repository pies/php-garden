<?
/** 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

class Condition {

	private function is ($field, $value) {
		return sprintf("%s = %s", $field, $this->prepare($value));
	}
	
	private function isNot ($field, $value) {
		return sprintf("%s <> %s", $field, $this->prepare($value));
	}
	
	private function has ($field, $value) {
		return $this->is($field, "%$value%");
	}
		
	private function hasNo ($field, $value) {
		return $this->isNot($field, "%$value%");
	}
	
	private function in ($field, $array) {
		enum($array)->map(array($this, 'prepare'))->join(', ')->format('');

		$params = array(join(", ", array_collect($value, create_function('$value', 'return "\'$value\'";'))));
	}

define('ATTR_IN',      "%s IN (%s)");
define('ATTR_NOT_IN',  "%s NOT IN (%s)");
define('ATTR_BETWEEN', "%s BETWEEN %d AND %d");
define('ATTR_OUTSIDE', "%s NOT BETWEEN %d AND %d");
		
	
	
}



/**
 * Defines used to create the actual database queries
 */
define('ATTR_IS',      "%s = '%s'");
define('ATTR_IS_NOT',  "%s <> '%s'");
define('ATTR_HAS',     "%s = '%%%s%%'");
define('ATTR_HAS_NO',  "%s <> '%%%s%%'");
define('ATTR_IN',      "%s IN (%s)");
define('ATTR_NOT_IN',  "%s NOT IN (%s)");
define('ATTR_BETWEEN', "%s BETWEEN %d AND %d");
define('ATTR_OUTSIDE', "%s NOT BETWEEN %d AND %d");

class DatabaseAdapter extends Adapter {

	public static function Filter ($filters) {
		if (func_num_args()==3) $filters = array(func_get_args());

		$OUT = array();
		foreach ($filters as $F) {
			$OUT[] = self::SqlFilter($F[0], $F[1], $F[2]);
		}
		return join(' AND ', $OUT);
	}

	/**
	 * Returns an SQL query condition statement for any given parameters.
	 *
	 * @param mixed $field Field name or an array of field names to check
	 * @param string $relation An sprintf() formatting string with the check
	 * @param mixed $value A value or an array to check against
	 * @return string An SQL query fragment
	 */
	private static function SqlFilter ($field, $relation, $value) {

		$fields = is_array($field)? $field: array($field);

		switch ($relation) {
			case ATTR_IN:
			case ATTR_NOT_IN:
				$params = array(join(", ", array_collect($value, create_function('$value', 'return "\'$value\'";'))));
				break;
			case ATTR_BETWEEN:
			case ATTR_OUTSIDE:
				$params = array(array_shift($value), array_shift($value));
				break;
			default:
				$params = array(addslashes($value));
				break;
		}

		$OUT = array();
		foreach ($fields as $field) {
			$OUT[] = call_user_func_array('sprintf',
				array_merge(array($relation, $field), $params)
			);
		}

		return '('.join(' OR ', $OUT).')';
	}

}

?>