<?

/** 
 * Facilitates an object-oriented way of working with strings.
 * 
 * Uses the {@link Enum} class.
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 *
 * @package Extensions
 * @subpackage Strings
 * @version $Revision: 13 $
*/
class Str {
	
	private $value = '';
	
	function __construct($args=array()) {
		$this->value = join($args);		
	}
	
	public function get() {
		return $this->value;
	}
	
	public function explode ($by) {
		return explode($by, $this->value);
	}
	
	/**
	 * Returns a camelized sentence, ie. under_scored_name becomes UnderScoredName.
	 *
	 * @return string
	 */
	public function camelize () {
		return enum($this->explode('_'))->map('ucfirst')->join();
	}

	/**
	 * Returns an underscored sentence, ie. CameLizedName becomes came_lized_name.
	 *
	 * @return string
	 */
	function underscore () {
		$OUT = array();
		foreach ($this->explode('/') as $part) {
			if (!$part) continue;

			if (preg_match_all('/[A-Z][a-z]+/', $part, $R))
				$OUT[] = join('_', array_shift($R));
			else
				$OUT[] = $part;
		}
		return strtolower(join('/', $OUT));
	}

}

/** 
 * Creates a new Str object in flight from a string or a list of arguments
 *
 * @return Enum
 */
function str () {
	$args = func_get_args();
	return new Str($args);
}
	
?>