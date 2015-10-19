<?php

namespace Berie\ORM;

use Berie\ORM\Builder\Select;
use Berie\ORM\Builder\Insert;
use Berie\ORM\Builder\Delete;
use Berie\ORM\Builder\Update;

/**
 * @package 	Berie\ORM
 * @subpackage 	Builder
 * @author 		Eugen Melnychenko
 */
class Builder
	extends \Berie\ORM
{
	private $database;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function __construct(\PDO $connect)
	{
		return $this->database = $connect;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder\Insert
	 */
	public function insert($table)
	{
		return new Insert($this->database, $table);
	}

	/**
	 * @param array $fields
	 *
	 * @return \Berie\ORM\Builder\Select
	 */
	public function select(array $fields = [])
	{
		return new Select($this->database, $fields);
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder\Update
	 */
	public function update($table)
	{
		return new Update($this->database, $table);
	}

	/**
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function delete()
	{
		return new Delete($this->database);
	}
}
