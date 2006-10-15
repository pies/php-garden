<?

class Session {

	public static function init() {
		session_set_save_handler(
			array('Session', 'open'),
			array('Session', 'close'),
			array('Session', 'read'),
			array('Session', 'write'),
			array('Session', 'destroy'),
			array('Session', 'cleanup')
		);
		register_shutdown_function('session_write_close');
		if (!session_id()) session_start();
		return true;
	}

	public static function open () {
		// connect
		$error_msg = null;
		define('SESSION_DB_LINK', sqlite_open(DIR_DATA.'/sessions.db', 0666, $error_msg));
		if (!SESSION_DB_LINK) {
			warning("Can't open session database: $error_msg");
			return false;
		}
		// check if required table exists, create it if necessary
		$R = sqlite_query("SELECT name FROM sqlite_master WHERE type='table' AND name='sess'", SESSION_DB_LINK);
		if (!sqlite_num_rows($R)) sqlite_exec("CREATE TABLE sess (id TEXT, data TEXT, expire INT)", SESSION_DB_LINK);

		return true;
	}

	public static function read ($id) {
		$t = time();
		$sql = "SELECT data FROM sess WHERE expire > $t AND id = '".sqlite_escape_string($id)."'";
		$r = sqlite_query($sql, SESSION_DB_LINK);
		$d = sqlite_fetch_array($r);
		return ''.is_array($d)? array_shift($d): false;
	}

	public static function write ($id, $data) {
		$id = "'".sqlite_escape_string($id)."'";
		$expire = time() + 2 * WEEK;

		$R = sqlite_query("SELECT id FROM sess WHERE id = $id", SESSION_DB_LINK);
		sqlite_query((sqlite_num_rows($R)?
			"UPDATE sess SET data = '$data', expire = $expire WHERE id = $id":
			"INSERT INTO sess (id, data, expire) VALUES ($id, '$data', $expire)"
		), SESSION_DB_LINK);

		return sqlite_changes(SESSION_DB_LINK)? true: false;
   }

	public static function destroy ($id) {
		sqlite_query("DELETE FROM sess WHERE id = '".sqlite_escape_string($id)."'", SESSION_DB_LINK);
		return sqlite_changes(SESSION_DB_LINK)? true: false;
	}

	public static function close () {
		// garbage collect once in a hundred page requests
		if (rand(1,100) == 100) Session::cleanup();
		return @sqlite_close(SESSION_DB_LINK);
	}

	public static function cleanup () {
		sqlite_query("DELETE FROM sess WHERE expire < ".time(), SESSION_DB_LINK);
		return sqlite_changes(SESSION_DB_LINK);
	}
}

?>