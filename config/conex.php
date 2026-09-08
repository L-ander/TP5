<?php

class Conexion {

	public $driver;
	public $host;
	public $port;
	public $dbname;
	private $user;
	// public $user;
	public $pass;
	protected $db;

	private static function cargarConfig($variable = 'config') {
		require __DIR__ . '/config.php';
		return isset($$variable) ? $$variable : null;
	}

	public static function conectar()	{

		try {
			$config = self::cargarConfig();
			if (!isset($config["database"])) {
				return false;
			}

			$driver = $config["database"]["driver"];
			$host = $config["database"]["host"];
			$port = $config["database"]["port"];
			$dbname = $config["database"]["dbname"];
			$user = $config["database"]["username"];
			$pass = $config["database"]["password"];

			$stmt = new PDO("".$driver.":host=".$host."; port=".$port."; dbname=".$dbname."","".$user."","".$pass."");

			$stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			return $stmt;

		} catch (PDOException $e) {

			return false;
			$stmt = null;
			exit();
		}
	}
	public function conexionVieja()	{

		try {
			$configAnterior = self::cargarConfig('configAnterior');
			if (!isset($configAnterior["database"])) {
				return false;
			}

			$driver = $configAnterior["database"]["driver"];
			$host = $configAnterior["database"]["host"];
			$port = $configAnterior["database"]["port"];
			$dbname = $configAnterior["database"]["dbname"];
			$user = $configAnterior["database"]["username"];
			$pass = $configAnterior["database"]["password"];

			$stmt = new PDO("".$driver.":host=".$host."; port=".$port."; dbname=".$dbname."","".$user."","".$pass."");

			$stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			// var_dump($stmt);
			// exit();

			return $stmt;

		} catch (Exception $e) {

			return false;
			$stmt = null;
			return $pdo;
			exit();
		}
	}
} #Fin De La Clase
