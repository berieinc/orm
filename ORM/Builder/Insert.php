<?php

namespace Berie\ORM\Builder;

use Berie\ORM\Exceptions\Exceptions;
use Berie\ORM\Query;

/**
 * @package 	Berie\ORM\Builder
 * @subpackage 	Insert
 * @author 		Eugen Melnychenko
 */
class Insert
{
	private $database;

	private $table;
	private $values;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder\Insert
	 */
	public function __construct(\PDO $connect, $table)
	{
		$this->database = $connect;
		$this->table 	= $table;

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param string $value
	 *
	 * @return \Berie\ORM\Builder\Insert
	 */
	public function values($column, $value = null)
	{
		$values = !empty($this->values) ?
			$this->values : [];

		if(is_array($column)) {
			$values = $column;
		} elseif(!empty($column)
			&& !empty($value)
		) {
			$values[$column] = $value;
		} else {
			//exception
		}

		$this->values = $values;

		return $this;
	}

	public function getQuery()
	{
		$this->query = !empty($this->query) ?
			$this->query : $this->generateQuery();

		return $this->query;
	}

	public function runQuery()
	{
		$this->getQuery();

		new Query($this->database, $this->query);

		return;
	}

	private function generateQuery()
	{
		$query = "INSERT INTO";
		$query .= " " . $this->table;
		$query .= $this->generateQueryValues();

		return $this->query = $query;
	}

	private function generateQueryValues()
	{
		$query 		= '';
		$in 		= 0;
		$columns 	= '';
		$values 	= '';

		foreach ($this->values as $col => $val) {
			if(!empty($val)) {
				$columns .= $in == 0 ?
					"`" . $col . "`" : ", `" . $col . "`";

				$values .= $in == 0 ?
					"'" . $val . "'" : ", '" . $val . "'";
			}

			$in++;
		}

		$query .= " (" . $columns . ")";
		$query .= " VALUES(" . $values . ")";

		return $query;
	}
}
