<?php

namespace Berie\ORM\Builder;

use Berie\ORM\Builder\Factory;

use Berie\ORM\Query;

/**
 * @package 	Berie\ORM\Builder
 * @subpackage 	Update
 * @author 		Eugen Melnychenko
 */
class Update
{
	private $database;

	private $table;
	private $set;
	private $where;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder\Update
	 */
	public function __construct(\PDO $connect, $table)
	{
		$this->table = $table;
		return $this->database = $connect;
	}

	/**
	 * @param mixed $column
	 * @param string $value
	 *
	 * @return \Berie\ORM\Builder\Update
	 */
	public function set($column, $value = null)
	{
		$set = !empty($this->set) ?
			$this->set : [];

		if(is_array($column)) {
			$set = $column;
		} elseif(!empty($column)
			&& !empty($value)
		) {
			$set[$column] = $value;
		} else {

		}

		$this->set = $set;

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param string $value
	 *
	 * @return \Berie\ORM\Builder\Update
	 */
	public function where($column, $value = null)
	{
		$data = [
			"where" 	=> $this->where,
			"column" 	=> $column,
			"value" 	=> $value,
		];

		$this->where = (new Factory())->parse(Factory::WHERE, $data);

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
		$query = "UPDATE";
		$query .= " " . $this->table;
		$query .= " SET";
		$query .= $this->generateQuerySet();
		$query .= (new Factory())->generate(Factory::WHERE, $this->where);

		return $this->query = $query;
	}

	private function generateQuerySet()
	{
		$query 	= '';
		$in 	= 0;

		foreach ($this->set as $key => $value) {
			$query .= $in == 0 ?
				" `" . $key . "`='" . $value . "'" :
				", `" . $key . "`='" . $value . "'";

			$in++;
		}

		return $query;
	}
}
