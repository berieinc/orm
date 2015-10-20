<?php

namespace Berie\ORM\Builder;

use Berie\ORM\Builder\Factory;
use Berie\ORM\Exceptions\Exceptions;

use Berie\ORM\Query;

/**
 * @package 	Berie\ORM\Builder
 * @subpackage 	Select
 * @author 		Eugen Melnychenko
 */
class Select
{
	private $database;

	private $table;
	private $join;
	private $fields;
	private $where;
	private $alias;
	private $order;
	private $limit;
	private $offset;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder\Select
	 */
	public function __construct(\PDO $connect, $fields)
	{
		$this->database = $connect;
		$this->fields 	= $fields;

		return;
	}

	/**
	 * @param string $table
	 * @param string $alias
	 *
	 * @return \Berie\ORM\Builder\Select
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
	 * @return \Berie\ORM\Builder\Select
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
	 * @return \Berie\ORM\Builder\Select
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
	 * @return \Berie\ORM\Builder\Select
	 */
	public function join($table, $alias, $on = [], $type = "INNER")
	{
		if(!empty($table)
			&& !empty($on)
		) {
			$this->join = !empty($this->join) ?
				$this->join : '';

			foreach ($on as $key => $value) {
				$query = $type . " JOIN " . $table . " " . $alias
					. " ON " . $key . " = " . $value;
				break;
			}

			$this->join .= !empty($this->join) ?
				" " . $query : $query;
		}

		return $this;
	}

	/**
	 * @param integer $number
	 *
	 * @return \Berie\ORM\Builder\Select
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
	 * @return \Berie\ORM\Builder\Select
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

		$this->queryClass = new Query($this->database, $this->query);

		return;
	}

	/**
	 * @return array
	 */
	public function getArray()
	{
		empty($this->queryClass) ?
			$this->runQuery() : null;

		$prepare = $this->queryClass
			->getPrepare();

		return $prepare->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getEntity()
	{
		$entity = [];

		$data = $this->getArray();

		if(!empty($data)) {
			$data = [
				'data' => $data,
				'pref' => $this->columnsMETA(),
			];

			$data = (new \Berie\ORM\DataType\Factory())
				->parseToSelect($data);


			$preferences = [
				'table'	=> $this->table,
			];

			if(count($data) >= 1) {
				foreach ($data["data"] as $key => $value) {
					$preferences = [
						"dataType" => $data["pref"],
						"params" => [
							"table"	=> $this->table,
							"id"	=> $value["id"],
						]
					];

					// $relationships = $this->findRelationship();
					//
					// if(!empty($relationships)) {
					// 	foreach ($relationships as $relationship) {
					// 		var_dump($relationship['COLUMN_NAME']);
					// 		var_dump($relationship['REFERENCED_TABLE_NAME']);
					// 		var_dump($relationship['REFERENCED_COLUMN_NAME']);
					// 	}
					// }
					//
					// var_dump($value); die;

					$entity[] = new \Berie\ORM\Manager\Entity($value, $preferences);
				}
			} else {
			}

			return $entity;
		}

		return;
	}

	private function findRelationship()
	{
		$return  = [];

		$query = "SELECT "
			. "`TABLE_SCHEMA`, `TABLE_NAME`, `COLUMN_NAME`, "
			. "`REFERENCED_TABLE_SCHEMA`, `REFERENCED_TABLE_NAME`, "
			. "`REFERENCED_COLUMN_NAME` "
			. "FROM "
			. "`INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` "
			. "WHERE "
			. "`TABLE_SCHEMA` = SCHEMA() "
			. " AND `REFERENCED_TABLE_NAME` IS NOT NULL";

		$prepare = (new \Berie\ORM\Query($this->database, $query))
			->getPrepare();

		$relationships = $prepare->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($relationships as $relationship) {
			if($relationship['TABLE_NAME'] === 'users') {
				$return[] = $relationship;
			}
		}

		return $return;
	}

	/**
	 * @return integer
	 */
	public function getCount()
	{
		empty($this->queryClass) ?
			$this->runQuery() : null;

		$prepare = $this->queryClass
			->getPrepare();

		return $prepare->rowCount();
	}

	private function columnsMETA()
	{
		$query 		= "SHOW COLUMNS FROM " . $this->table;
		$queryClass = new Query($this->database, $query);
		$prepare 	= $queryClass->getPrepare();

		return (new \Berie\ORM\DataType\Factory())
			->parseData($prepare->fetchAll(\PDO::FETCH_ASSOC));
	}

	private function generateQuery()
	{
		$query = "SELECT";

		if(empty($this->fields)) {
			$query .= empty($this->alias) ?
				" *" : " " . $this->alias . ".*";
		} else {
			$query .= $this->generateQueryFields();
		}

		$query .= $this->generateQueryFrom();
		$query .= (new Factory())->generate(Factory::JOIN, $this->join);
		$query .= (new Factory())->generate(Factory::WHERE, $this->where);
		$query .= (new Factory())->generate(Factory::ORDER, $this->order);
		$query .= (new Factory())->generate(Factory::LIMIT, $this->limit);
		$query .= (new Factory())->generate(Factory::OFFSET, $this->offset);

		return $this->query = $query;
	}

	private function generateQueryFields()
	{
		$query = '';

		for($i = 0; $i < count($this->fields); $i++) {
			$field = $this->fields[$i];

			$query .= $i == 0 ?
				" " . $field . "" : ", " . $field . "";
		}

		return $query;
	}

	private function generateQueryFrom()
	{
		$query = " FROM";
		$query .= empty($this->alias) ?
			" " . $this->table : " " . $this->table . " " . $this->alias;

		return $query;
	}
}
