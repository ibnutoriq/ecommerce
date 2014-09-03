<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'libraries/AutoLoader.php';

use libraries\Bootstrap as Bootstrap;

$bootstrap = new Bootstrap();
ob_start();
?>