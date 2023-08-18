<?php

// Entry menu case
define('GLPI_ROOT', '../..');
include (GLPI_ROOT . "/inc/includes.php");

Session::checkRight("config", "w");

Html::header("TITLE", $_SERVER['PHP_SELF'], "plugins");

echo "This is the plugin stat page";

Html::footer();
