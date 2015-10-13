<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
 * @subpackage 	Query
 * @author 		Eugen Melnychenko
 */
class Query extends \Berie\ORM
{
	private $database;
	private $executed;

	/**
	 * RUN QUERY
	 *
	 * @param \PDO $connect
	 * @param string $query
	 *
	 * @return \Berie\ORM\Query
	 */
	function __construct(\PDO $connect, $query)
	{
		$this->database = $connect;

		if(!empty($query)
			&& $this->database
		) {
			$prepare = $this->database->prepare($query);
			$execute = $prepare->execute();

			$this->query   = $query;
			$this->prepare = $prepare;
			$this->execute = $execute;
		}

		return;
	}

	/**
	 * RETURN PDO EXECUTE
	 *
	 * @return \Berie\ORM\Query
	 */
	public function getExecute()
	{
		return $this->execute;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getPrepare()
	{
		return $this->prepare;
	}
}
