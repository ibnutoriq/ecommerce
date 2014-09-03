<?php
namespace libraries;

use libraries\IConfig as IConfig;

class Model implements IConfig
{
	private static $dbHost = IConfig::DB_HOST;
	private static $dbUser = IConfig::DB_USER;
	private static $dbPassword = IConfig::DB_PASSWORD;
	private static $dbName = IConfig::DB_NAME;
	private static $connect;

	public function __construct()
	{
		
	}

	public function connect()
	{
		$dbHost = self::$dbHost;
		$dbUser = self::$dbUser;
		$dbPassword = self::$dbPassword;
		$dbName = self::$dbName;
		$connect = self::$connect;

		try {
			$connect = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
			$connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
		
		return $connect;
	}
}
?>