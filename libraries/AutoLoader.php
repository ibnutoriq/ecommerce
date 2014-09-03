<?php
spl_autoload_register(function($className) {
	$className = ltrim($className);
	$fileName = '';
	$nameSpace = '';

	if($lastNsPos = strrpos($className, '\\')) {
		$nameSpace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $nameSpace) . DIRECTORY_SEPARATOR;
	}

	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
	if(is_readable($fileName)) {
		require_once $fileName;
	}
});