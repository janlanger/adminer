Enable drivers in this directory like this:

<?php
function adminer_object() {
	include ROOT_DIR . "/./plugins/drivers/simpledb.php"; // the driver is enabled just by including
	return new Adminer; // or return AdminerPlugin if you want to use other plugins
}

// include original Adminer
include ROOT_DIR . "/./adminer.php";
?>
