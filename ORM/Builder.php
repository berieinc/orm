<?php

namespace Berie\ORM;

use Berie\ORM\Exceptions;

/**
 * @package 	Berie\ORM
 * @subpackage 	Builder
 * @author 		Eugen Melnychenko
 */
class Builder
	extends \Berie\ORM
{
	private $database;

	private $operation;
	private $table;
	private $fields;
	private $set;
	private $where;
	private $alias;
	private $order;
	private $limit;
	private $offset;

	const SELECT = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const DELETE = 4;

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
	 * @return \Berie\ORM\Builder
	 */
	public function insert($table)
	{
		$this->operation = empty($this->operation) ?
			self::INSERT : (new Exceptions())->B0020();

		$this->table = $table;

		return $this;
	}

	/**
	 * @param array $fields
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function select(array $fields = [])
	{
		$this->operation = empty($this->operation) ?
			self::SELECT : (new Exceptions())->B0020();

		$this->fields = $fields;

		return $this;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function update($table)
	{
		$this->operation = empty($this->operation) ?
			self::UPDATE : (new Exceptions())->B0020();

		$this->table = $table;

		return $this;
	}

	/**
	 * @return \Berie\ORM\Builder
	 */
	public function delete()
	{
		$this->operation = empty($this->operation) ?
			self::DELETE : (new Exceptions())->B0020();

		return $this;
	}

	/**
	 * @param string $table
	 * @param string $alias
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function from($table, $alias = null)
	{
		if(empty($this->table)
			&& $this->operation != self::INSERT
			&& $this->operation != self::UPDATE
		) {
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
	 * @return \Berie\ORM\Builder
	 */
	public function set($column, $value = null)
	{
		if($this->operation == self::INSERT
			|| $this->operation == self::UPDATE
		) {
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
		} else {
			(new Exceptions())->B0040();
		}

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param string $value
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function where($column, $value = null)
	{
		if($this->operation == self::SELECT
			|| $this->operation == self::UPDATE
			|| $this->operation == self::DELETE
		) {
			$where = !empty($this->where) ?
				$this->where : '';

			if(is_array($column)) {
				foreach ($column as $key => $val) {
					$where .= empty($where) ?
						"`" . $key . "`='" . $val . "'" :
						" AND `" . $key . "`='" . $val . "'";
				}
			} elseif(!empty($column)
				&& empty($value)
			) {
				$where .= empty($where) ?
					$column : " AND " . $column;

			} elseif(!empty($column)
				&& !empty($value)
			) {
				$where .= empty($where) ?
					"`" . $column . "`='" . $value . "'" :
					" AND `" . $column . "`='" . $value . "'";
			} else {

			}

			$this->where = $where;
		} else {
			(new Exceptions())->B0050();
		}

		return $this;
	}

	/**
	 * @param string $column
	 * @param string $type
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function order($column = 'id', $type = 'ASC')
	{
		if(($this->operation == self::SELECT
			|| $this->operation == self::DELETE)
			&& empty($this->order)
			&& ($type == 'ASC' || $type == 'DESC')
		) {
			$this->order = "ORDER BY " . $column . " " . $type;
		} else {
			(new Exceptions())->B0060();
		}

		return $this;
	}

	/**
	 * @param integer $number
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function limit($number = 0)
	{
		if(($this->operation == self::SELECT
			|| $this->operation == self::DELETE)
			&& empty($this->limit)
		) {
			$this->limit = "LIMIT " . $number;
		} else {
			(new Exceptions())->B0065();
		}

		return $this;
	}

	/**
	 * @param integer $number
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function offset($number = 0)
	{
		if(($this->operation == self::SELECT
			|| $this->operation == self::DELETE)
			&& empty($this->offset)
		) {
			$this->offset = "OFFSET " . $number;
		} else {
			(new Exceptions())->B0070();
		}

		return $this;
	}

	public function getQuery()
	{
		$this->query = !empty($this->query) ?
			$this->query : $this->generateQuery();

			
		$this->queryClass = new Query($this->database, $this->query);

		return;
	}

	/**
	 * @return array
	 */
	public function getArray()
	{
		!empty($this->queryClass) ?
			$this->queryClass : $this->getQuery();

		$prepare = $this->queryClass->getPrepare();

		return $prepare->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getEntity()
	{
		$entity = [];

		$resultData = $this->getArray();

		$preferences = [
			'table'	=> $this->table,
		];

		if(count($resultData) >= 1) {
			foreach ($resultData as $key => $value) {
				$preferences['id'] = $value['id'];

				$entity[] = new \Berie\ORM\Entity($value, $preferences);
			}
		} else {

		}

		return $entity;
	}

	/**
	 * @return integer
	 */
	public function getCount()
	{
		$this->queryClass = !empty($this->queryClass) ?
			$this->queryClass : $this->getQuery();

		$prepare = $this->queryClass->getPrepare();

		return $prepare->rowCount();
	}

	private function generateQuery()
	{
		$query = '';

		if($this->operation == self::SELECT) {
			$query = $this->generateQuerySelect();
 		}

		if($this->operation == self::INSERT
			&& !empty($this->set)
		) {
			$query .= $this->generateQueryInsert();
		}

		if($this->operation == self::UPDATE
			&& !empty($this->table)
			&& !empty($this->set)
			&& !empty($this->where)
		) {
			$query = $this->generateQueryUpdate();
		}

		if($this->operation == self::DELETE
			&& !empty($this->table)
			&& !empty($this->where)
		) {
			$query = $this->generateQueryDelete();
		}

		return $this->query = $query;
	}

	private function generateQuerySelect()
	{
		$query = "SELECT";

		if(empty($fields)) {
			$query .= empty($this->alias) ?
				" *" : " " . $this->alias . ".*";
		} else {
			$query .= $this->generateQuerySelectFields();
		}

		$query .= $this->generateQueryFrom();
		$query .= $this->generateQueryWhere();
		$query .= $this->generateQueryOrder();
		$query .= $this->generateQueryLimit();
		$query .= $this->generateQueryOffset();

		return $query;
	}

	private function generateQueryInsert()
	{
		$query = "INSERT INTO";
		$query .= " " . $this->table;
		$query .= $this->generateQueryInsertSet();

		return $query;
	}

	private function generateQueryUpdate()
	{
		$query = "UPDATE";
		$query .= " " . $this->table;
		$query .= " SET";
		$query .= $this->generateQueryUpdateSet();
		$query .= $this->generateQueryWhere();

		return $query;
	}

	private function generateQueryDelete()
	{
		$query = "DELETE";
		$query .= $this->generateQueryFrom();
		$query .= $this->generateQueryWhere();
		$query .= $this->generateQueryOrder();
		$query .= $this->generateQueryLimit();
		$query .= $this->generateQueryOffset();

		return $query;
	}

	private function generateQuerySelectFields()
	{
		$query = '';

		for($i = 0; $i < count($this->fields); $i++) {
			$field = $this->fields[$i];

			$query .= $i == 0 ?
				" " . $field . "" : ", " . $field . "";
		}

		return $query;
	}

	private function generateQueryUpdateSet()
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

	private function generateQueryInsertSet()
	{
		$query 		= '';
		$in 		= 0;
		$columns 	= '';
		$values 	= '';

		foreach ($this->set as $col => $val) {
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

	private function generateQueryFrom()
	{
		$query = " FROM";
		$query .= empty($this->alias) ?
			" " . $this->table : " " . $this->table . " " . $this->alias;

		return $query;
	}

	private function generateQueryWhere()
	{
		return !empty($this->where) ?
			" WHERE " . $this->where : "";
	}

	private function generateQueryOrder()
	{
		return !empty($this->order) ?
			" " . $this->order : "";
	}

	private function generateQueryLimit()
	{
		return !empty($this->limit) ?
			" " . $this->limit : "";
	}

	private function generateQueryOffset()
	{
		return !empty($this->limit) && !empty($this->offset) ?
			" " . $this->offset : "";
	}
}
