<?php
function adminer_errors($errno, $errstr) {
	return !!preg_match('~^(Trying to access array offset on value of type null|Undefined array key)~', $errstr);
}

error_reporting(0); // errors and warnings
set_error_handler('adminer_errors', E_WARNING);

include ROOT_DIR . "/../adminer/include/coverage.inc.php";

// disable filter.default
$filter = !preg_match('~^(unsafe_raw)?$~', ini_get("filter.default"));
if ($filter || ini_get("filter.default_flags")) {
	foreach (array('_GET', '_POST', '_COOKIE', '_SERVER') as $val) {
		$unsafe = filter_input_array(constant("INPUT$val"), FILTER_UNSAFE_RAW);
		if ($unsafe) {
			$$val = $unsafe;
		}
	}
}

if (function_exists("mb_internal_encoding")) {
	mb_internal_encoding("8bit");
}

include ROOT_DIR . "/../adminer/include/functions.inc.php";

// used only in compiled file
if (isset($_GET["file"])) {
	include ROOT_DIR . "/../adminer/file.inc.php";
}

if ($_GET["script"] == "version") {
	$fp = file_open_lock(get_temp_dir() . "/adminer.version");
	if ($fp) {
		file_write_unlock($fp, serialize(array("signature" => $_POST["signature"], "version" => $_POST["version"])));
	}
	exit;
}

global $adminer, $connection, $driver, $drivers, $edit_functions, $enum_length, $error, $functions, $grouping, $HTTPS, $inout, $jush, $LANG, $langs, $on_actions, $permanent, $structured_types, $has_token, $token, $translations, $types, $unsigned, $VERSION; // allows including Adminer inside a function

if (!$_SERVER["REQUEST_URI"]) { // IIS 5 compatibility
	$_SERVER["REQUEST_URI"] = $_SERVER["ORIG_PATH_INFO"];
}
if (!strpos($_SERVER["REQUEST_URI"], '?') && $_SERVER["QUERY_STRING"] != "") { // IIS 7 compatibility
	$_SERVER["REQUEST_URI"] .= "?$_SERVER[QUERY_STRING]";
}
if ($_SERVER["HTTP_X_FORWARDED_PREFIX"]) {
	$_SERVER["REQUEST_URI"] = $_SERVER["HTTP_X_FORWARDED_PREFIX"] . $_SERVER["REQUEST_URI"];
}
$HTTPS = ($_SERVER["HTTPS"] && strcasecmp($_SERVER["HTTPS"], "off")) || ini_bool("session.cookie_secure"); // session.cookie_secure could be set on HTTP if we are behind a reverse proxy

@ini_set("session.use_trans_sid", false); // protect links in export, @ - may be disabled
if (!defined("SID")) {
	session_cache_limiter(""); // to allow restarting session
	session_name("adminer_sid"); // use specific session name to get own namespace
	$params = array(0, preg_replace('~\?.*~', '', $_SERVER["REQUEST_URI"]), "", $HTTPS);
	if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
		$params[] = true; // HttpOnly
	}
	call_user_func_array('session_set_cookie_params', $params); // ini_set() may be disabled
	session_start();
}

// disable magic quotes to be able to use database escaping function
remove_slashes(array(&$_GET, &$_POST, &$_COOKIE), $filter);
if (function_exists("get_magic_quotes_runtime") && get_magic_quotes_runtime()) {
	set_magic_quotes_runtime(false);
}
@set_time_limit(0); // @ - can be disabled
@ini_set("zend.ze1_compatibility_mode", false); // @ - deprecated
@ini_set("precision", 15); // @ - can be disabled, 15 - internal PHP precision

include ROOT_DIR . "/../adminer/include/lang.inc.php";
include ROOT_DIR . "/../adminer/lang/$LANG.inc.php";
include ROOT_DIR . "/../adminer/include/pdo.inc.php";
include ROOT_DIR . "/../adminer/include/driver.inc.php";
include ROOT_DIR . "/../adminer/drivers/sqlite.inc.php";
include ROOT_DIR . "/../adminer/drivers/pgsql.inc.php";
include ROOT_DIR . "/../adminer/drivers/oracle.inc.php";
include ROOT_DIR . "/../adminer/drivers/mssql.inc.php";
include ROOT_DIR . "/../adminer/drivers/mongo.inc.php";
include ROOT_DIR . "/../adminer/drivers/elastic.inc.php";
include ROOT_DIR . "/./include/adminer.inc.php";
$adminer = (function_exists('adminer_object') ? adminer_object() : new Adminer);
include ROOT_DIR . "/../adminer/drivers/mysql.inc.php"; // must be included as last driver

$config = driver_config();
$possible_drivers = $config['possible_drivers'];
$jush = $config['jush'];
$types = $config['types'];
$structured_types = $config['structured_types'];
$unsigned = $config['unsigned'];
$operators = $config['operators'];
$functions = $config['functions'];
$grouping = $config['grouping'];
$edit_functions = $config['edit_functions'];
if ($adminer->operators === null) {
	$adminer->operators = $operators;
}

define("SERVER", $_GET[DRIVER]); // read from pgsql=localhost
define("DB", $_GET["db"]); // for the sake of speed and size
define("ME", preg_replace('~\?.*~', '', relative_uri()) . '?'
	. (sid() ? SID . '&' : '')
	. (SERVER !== null ? DRIVER . "=" . urlencode(SERVER) . '&' : '')
	. (isset($_GET["username"]) ? "username=" . urlencode($_GET["username"]) . '&' : '')
	. (DB != "" ? 'db=' . urlencode(DB) . '&' . (isset($_GET["ns"]) ? "ns=" . urlencode($_GET["ns"]) . "&" : "") : '')
);

include ROOT_DIR . "/../adminer/include/version.inc.php";
include ROOT_DIR . "/../adminer/include/design.inc.php";
include ROOT_DIR . "/../adminer/include/xxtea.inc.php";
include ROOT_DIR . "/../adminer/include/auth.inc.php";
include ROOT_DIR . "/./include/editing.inc.php";
include ROOT_DIR . "/./include/connect.inc.php";

$on_actions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT"; ///< @var string used in foreign_keys()
