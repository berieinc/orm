<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
 * @subpackage 	Internal
 * @author 		Eugen Melnychenko
 */
class Internal extends \Berie\ORM
{

	function __construct()
	{
	}

	/**
	 * GET MYSQL PDO CONNECTION FROM CONFIG
	 *
	 * @param array $config
	 *
	 *	$config = array(
	 *	'unix_socket' 	=> '/path/to/socket',
	 *	'host' 			=> 'localhost(127.0.0.1)',
	 *	'username' 		=> 'root',
	 *	'password' 		=> 'root',
	 *	'dbname' 		=> 'name_of_database',
	 *	'charset' 		=> 'utf8',
	 * )
	 *
	 * @return \PDO
	 */
	function getPDO($config)
	{
		$unix_socket = isset($config['unix_socket']) ?
			$config['unix_socket'] : false;

		$host 		= isset($config['host']) ?
			$config['host'] : '';

		$username 	= isset($config['username']) ?
			$config['username'] : '';

		$password 	= isset($config['password']) ?
			$config['password'] : '';

		$database 	= isset($config['dbname']) ?
			$config['dbname'] : '';

		$charset 	= isset($config['charset']) ?
			$config['charset'] : 'utf8';

		try {
			$dsn = 'mysql:dbname=' . $database . ';';

			$dsn .= $unix_socket ?
				'unix_socket=' . $unix_socket : 'host=' . $host;

			$database = new \PDO($dsn, $username, $password);
			$database->exec("set names " . $charset);
			$database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
			$database->setAttribute(\PDO::ATTR_PERSISTENT, true);

		} catch (\PDOException $e) {
			(new \Berie\ORM\Exceptions())->DBConnectionFailure($e);
		}

		return $database;
	}
}
