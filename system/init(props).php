<?
/**
 * Prepare the environment
 *
 * @id $Id: init.php 757 2006-08-17 07:56:58Z Michał $
 * @copyright Copyright (c) 2006, Michal Tatarynowicz
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

if (version_compare(PHP_VERSION, '5.0.0') >= 0) define('IS_PHP_5', true);

/**
 * Define paths to system directories
 */
define('DIR_ROOT',  dirname(dirname(__FILE__)));
define('DIR_APPS',  DIR_ROOT.'/apps');
define('DIR_DATA',  DIR_ROOT.'/data');
define('DIR_LIBS',  DIR_ROOT.'/libs');
define('DIR_LOGS',  DIR_ROOT.'/logs');
define('DIR_VIEWS', DIR_ROOT.'/views');


/**
 * Define standard URLs
 */
define('THIS',  $_SERVER['SCRIPT_NAME']);
define('HERE',  dirname(THIS));
define('ROOT',  '/'==dirname(HERE)?'':dirname(HERE));
$protocol = strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS')===false? 'http': 'https';
define('FULL_HERE', "$protocol://{$_SERVER['HTTP_HOST']}".HERE);

/**
 * Deployment stages
 */
if (!defined('E_STRICT')) define('E_STRICT', 2047);
define ('STAGE_DEBUG', E_ALL | E_STRICT);
define ('STAGE_DEV',   E_ALL ^ E_NOTICE);
define ('STAGE_FIX',   E_ALL ^ E_NOTICE ^ E_WARNING);
define ('STAGE_PROD',  0);

/**
 * Error messaging constans
 */
define('MSG_LVL_NOTIFY',     'NOTIFICATION');
define('MSG_LVL_WARN',       'WARNING');
define('MSG_LVL_ALERT',      'ALERT');

define('MSG_AREA_CONTEXT',   'CONTEXT');
define('MSG_AREA_INIT',      'INIT');
define('MSG_AREA_MANAGE',    'MANAGE');
define('MSG_AREA_PERSIST',   'PERSIST');
define('MSG_AREA_TRANSFORM', 'TRANSFORM');
define('MSG_AREA_GENERATE',  'GENERATE');

/**
 * Set the timezone (otherwise we get warnings from PHP)
 */
date_default_timezone_set('Europe/Warsaw');

$_REQUEST['_LOADED'] = array();

class FASE {

	public static function requires ($name) {
		if ( '.' == @$name[0] ) {
			FASE::warn(MSG_AREA_INIT, "Component name can't start with a dot.");
			return false;
		}

		if (in_array($name, $_REQUEST['_LOADED'])) {
			return true;
		}

		//$filename = Lang::underscore(str_replace('.', '/', $name));
		$filename = strtolower($name);

		/** Try to find library */
		foreach (array(
			DIR_LIBS."/$filename.php",
			DIR_APPS."/$filename.php",
			DIR_ROOT."/$filename.php"
		) as $path) {
			if (is_file($path) && require_once($path)) return true;
		}

		/** Library not in path */
		FASE::alert( MSG_AREA_INIT, "Could not load $name, application error." );

		return false;
	}

	public static function provides ($name) {
		if (in_array($name, $_REQUEST['_LOADED'])) {
			FASE::warn( MSG_AREA_INIT, "Library $name already provided, please resolve conflict." );
			return false;
		}
		else {
			$_REQUEST['_LOADED'][] = $name;
			return true;
		}
	}

	public static function humanize_place ($place) {
		return preg_match('/^(.+):([0-9]+)$/', $place, $R)?
			str_replace(array(DIR_ROOT, '\\'), array('', '/'), $R[1]).', line '.$R[2]:
			$place;
	}

	public static function output ($title, $msg, $color='#994') {
		print system_window($title, $msg, $color);
	}

	public static function error_handler ($level, $str, $file, $line) {
		switch ($level) {
			case E_USER_NOTICE:  { FASE::notify(MSG_AREA_CONTEXT, $str, "$file:$line" ); break; }
			case E_USER_ERROR:   { FASE::alert(MSG_AREA_CONTEXT, $str, "$file:$line" ); break; }
			case E_USER_WARNING:
			default:             { FASE::warn(MSG_AREA_CONTEXT, $str, "$file:$line" ); break; }
		}
	}

	private static function send ($level, $args) {
		$date  = date('Y-m-d');
		$time  = date('H:i:s');
		$area  = $args[0];
		$msg   = @$args[1]? $args[1]: null;
		$data  = @$args[2]? $args[2]: array();
		$place = @$args[3]? $args[3]: FASE::humanize_place(caller_place(3));

//		if (strpos($msg, 'Non-static method') !== false) return;

		if (DEBUG) {
			$color =
				MSG_LVL_ALERT==$level? '#FA9': (
				MSG_LVL_WARN==$level? '#ED4':
				'#DDD');
			FASE::output("<u>$level</u> at $place", $msg, $color);
		}

		switch ($level) {
			case MSG_LVL_NOTIFY:
				error_log("$time $place $area: $msg\n", 3, DIR_LOGS.'/'.$date.'_note.log'); break;
			case MSG_LVL_WARN:
				error_log("$time $place $area: $msg\n", 3, DIR_LOGS.'/'.$date.'_warning.log'); break;
			case MSG_LVL_ALERT:
			default:
				error_log("$time $place $area: $msg\n", 3, DIR_LOGS.'/'.$date.'_ALERT.log'); break;

		}
	}

	public static function notify ($area, $msg=null, $data=array(), $place=null) {
		return FASE::send( MSG_LVL_NOTIFY, array($area, $msg, $data, $place));
	}

	public static function warn ($area, $msg=null, $data=array(), $place=null) {
		return FASE::send( MSG_LVL_WARN, array($area, $msg, $data, $place));
	}

	public static function alert ($area, $msg=null, $data=array(), $place=null) {
		return FASE::send( MSG_LVL_ALERT, array($area, $msg, $data, $place));
	}
}

/** Debugging (introspection, debugging messages display */
FASE::requires('std/meta');

/** Simple language extensions, such as call(), export() or func() */
FASE::requires('std/lang');


set_error_handler(array('FASE','error_handler'));

/** Application configuration */
FASE::requires('/config');

/** Compatibility with both php.ini "magic quotes" setting values */
Lang::handle_magic_quotes();

/** Initialize the session only if it was already started by loging in */
if (isset($_COOKIE['PHPSESSID'])) {
	FASE::requires('web/session');
	Session::init();
}

/** Database connection is defined in config.php */
if (defined('FASE_DATABASE_CFG')) {
	FASE::requires('data/db');
	DB_Base::connect(FASE_DATABASE_CFG);
}

?>