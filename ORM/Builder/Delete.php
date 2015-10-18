<?php

namespace Berie\ORM\Builder;

use Berie\ORM\Builder\Factory;
use Berie\ORM\Exceptions\Exceptions;

/**
 * @package 	Berie\ORM\Builder
 * @subpackage 	Delete
 * @author 		Eugen Melnychenko
 */
class Delete
{
	private $database;

	private $table;
	private $where;
	private $alias;
	private $order;
	private $limit;
	private $offset;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function __construct(\PDO $connect)
	{
		$this->database = $connect;

		return;
	}

	/**
	 * @param string $table
	 * @param string $alias
	 *
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function from($table, $alias = null)
	{
		if(empty($this->table)) {
			$this->table = $table;
			$this->alias = $alias;
		} else {
			(new Exceptions())->B0025();
		}

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param string $value
	 *
	 * @return \Berie\ORM\Builder\Delete
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

	/**
	 * @param string $column
	 * @param string $type
	 *
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function order($column = "id", $type = "ASC")
	{
		$data = [
			"column" 	=> $column,
			"type"		=> $type,
		];

		$this->order = empty($this->order) ?
			(new Factory())->parse(Factory::ORDER, $data) :
			(new Exceptions())->B0065();

		return $this;
	}

	/**
	 * @param integer $number
	 *
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function limit($number = 0)
	{
		$this->limit = empty($this->limit) ?
			(new Factory())->parse(Factory::LIMIT, ['number' => $number]) :
			(new Exceptions())->B0065();

		return $this;
	}

	/**
	 * @param integer $number
	 *
	 * @return \Berie\ORM\Builder\Delete
	 */
	public function offset($number = 0)
	{
		$this->offset = empty($this->offset) && !empty($this->limit) ?
			(new Factory())->parse(Factory::OFFSET, ['number' => $number]) :
			(new Exceptions())->B0065();

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
		$query = "DELETE";
		$query .= $this->generateQueryFrom();
		$query .= (new Factory())->generate(Factory::WHERE, $this->where);
		$query .= (new Factory())->generate(Factory::ORDER, $this->order);
		$query .= (new Factory())->generate(Factory::LIMIT, $this->limit);
		$query .= (new Factory())->generate(Factory::OFFSET, $this->offset);

		return $this->query = $query;
	}

	private function generateQueryFrom()
	{
		$query = " FROM";
		$query .= empty($this->alias) ?
			" " . $this->table : " " . $this->table . " " . $this->alias;

		return $query;
	}
}
