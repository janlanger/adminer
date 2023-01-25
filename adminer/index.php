<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/

const ROOT_DIR = __DIR__;

include ROOT_DIR . "/./include/bootstrap.inc.php";
include ROOT_DIR . "/./include/tmpfile.inc.php";

$enum_length = "'(?:''|[^'\\\\]|\\\\.)*'";
$inout = "IN|OUT|INOUT";


if (isset($_GET["select"]) && ($_POST["edit"] || $_POST["clone"]) && !$_POST["save"]) {
	$_GET["edit"] = $_GET["select"];
}
if (isset($_GET["callf"])) {
	$_GET["call"] = $_GET["callf"];
}
if (isset($_GET["function"])) {
	$_GET["procedure"] = $_GET["function"];
}

if (isset($_GET["download"])) {
	include ROOT_DIR . "/./download.inc.php";
} elseif (isset($_GET["table"])) {
	include ROOT_DIR . "/./table.inc.php";
} elseif (isset($_GET["schema"])) {
	include ROOT_DIR . "/./schema.inc.php";
} elseif (isset($_GET["dump"])) {
	include ROOT_DIR . "/./dump.inc.php";
} elseif (isset($_GET["privileges"])) {
	include ROOT_DIR . "/./privileges.inc.php";
} elseif (isset($_GET["sql"])) {
	include ROOT_DIR . "/./sql.inc.php";
} elseif (isset($_GET["edit"])) {
	include ROOT_DIR . "/./edit.inc.php";
} elseif (isset($_GET["create"])) {
	include ROOT_DIR . "/./create.inc.php";
} elseif (isset($_GET["indexes"])) {
	include ROOT_DIR . "/./indexes.inc.php";
} elseif (isset($_GET["database"])) {
	include ROOT_DIR . "/./database.inc.php";
} elseif (isset($_GET["scheme"])) {
	include ROOT_DIR . "/./scheme.inc.php";
} elseif (isset($_GET["call"])) {
	include ROOT_DIR . "/./call.inc.php";
} elseif (isset($_GET["foreign"])) {
	include ROOT_DIR . "/./foreign.inc.php";
} elseif (isset($_GET["view"])) {
	include ROOT_DIR . "/./view.inc.php";
} elseif (isset($_GET["event"])) {
	include ROOT_DIR . "/./event.inc.php";
} elseif (isset($_GET["procedure"])) {
	include ROOT_DIR . "/./procedure.inc.php";
} elseif (isset($_GET["sequence"])) {
	include ROOT_DIR . "/./sequence.inc.php";
} elseif (isset($_GET["type"])) {
	include ROOT_DIR . "/./type.inc.php";
} elseif (isset($_GET["trigger"])) {
	include ROOT_DIR . "/./trigger.inc.php";
} elseif (isset($_GET["user"])) {
	include ROOT_DIR . "/./user.inc.php";
} elseif (isset($_GET["processlist"])) {
	include ROOT_DIR . "/./processlist.inc.php";
} elseif (isset($_GET["select"])) {
	include ROOT_DIR . "/./select.inc.php";
} elseif (isset($_GET["variables"])) {
	include ROOT_DIR . "/./variables.inc.php";
} elseif (isset($_GET["script"])) {
	include ROOT_DIR . "/./script.inc.php";
} else {
	include ROOT_DIR . "/./db.inc.php";
}

// each page calls its own page_header(), if the footer should not be called then the page exits
page_footer();
