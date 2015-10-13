<?php

namespace Berie\ORM;

use Berie\ORM\Exceptions;

/**
 * @package 	Berie\ORM
 * @subpackage 	Builder
 * @author 		Eugen Melnychenko
 */
class Builder extends \Berie\ORM
{
	private $database;
	private $preferences;

	const SELECT = 1;

	const INSERT = 2;
	const UPDATE = 3;
	const DELETE = 4;

	const R_ARRAY 	= 10;
	const R_ENTITY 	= 11;
	const R_COUNT 	= 12;

	/**
	 * @param \PDO $connect
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function __construct(\PDO $connect)
	{
		$this->database = $connect;

		return;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function insert($table)
	{
		if(empty($this->preferences['operation'])) {
			$this->preferences = [
				'operation' => self::INSERT,
				'global'	=> [
					'table'		=> $table,
				],
			];
		} else {
			(new Exceptions())->BuilderDoubleOperationError();
		}

		return $this;
	}

	/**
	 * @param array $fields
	 * @param string $alias
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function select(array $fields = [], $alias = null)
	{
		if(empty($this->preferences['operation'])) {
			$this->preferences = [
				'operation' => self::SELECT,
				'global'	=> [
					'fields'	=> $fields,
					'alias' 	=> $alias,
				],
			];
		} else {
			(new Exceptions())->BuilderDoubleOperationError();
		}

		return $this;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function update($table)
	{
		if(empty($this->preferences['operation'])) {
			$this->preferences = [
				'operation' => self::UPDATE,
				'global'	=> [
					'table'		=> $table,
				],
			];
		} else {
			(new Exceptions())->BuilderDoubleOperationError();
		}

		return $this;
	}

	/**
	 * @return \Berie\ORM\Builder
	 */
	public function delete()
	{
		if(empty($this->preferences['operation'])) {
			$this->preferences['operation'] = self::DELETE;
		} else {
			(new Exceptions())->BuilderDoubleOperationError();
		}

		return $this;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function from($table, $alias = null)
	{
		if(empty($this->preferences['table'])
			&& $this->preferences['operation'] != self::INSERT
			&& $this->preferences['operation'] != self::UPDATE
		) {
			$this->preferences['global']['table'] = $table;
		} else {
			(new Exceptions())->BuilderDoubleTableError();
		}

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param mixed $value
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function set($column, $value = null)
	{
		if($this->preferences['operation'] == self::INSERT
			|| $this->preferences['operation'] == self::UPDATE
		) {
			$set = !empty($this->preferences['global']['set']) ?
				$this->preferences['global']['set'] : [];

			if(is_array($column)) {
				$set = $column;
			} elseif(!empty($column)
				&& !empty($value)
			) {
				$set[$column] = $value;
			} else {

			}

			$this->preferences['global']['set'] = $set;
		} else {

		}

		return $this;
	}

	/**
	 * @param mixed $column
	 * @param mixed $value
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function where($column, $value = null)
	{
		if($this->preferences['operation'] == self::SELECT
			|| $this->preferences['operation'] == self::UPDATE
			|| $this->preferences['operation'] == self::DELETE
		) {
			$where = !empty($this->preferences['global']['where']) ?
				$this->preferences['global']['where'] : '';

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

			$this->preferences['global']['where'] = $where;
		} else {

		}

		return $this;
	}

	public function getQuery()
	{
		$query = !empty($this->query) ?
			$this->query : $this->formOperation();

		$this->queryObj = new Query($this->database, $this->query);
		return $this->queryObj;
	}

	public function getArray()
	{
		$this->queryObj = !empty($this->queryObj) ?
			$this->queryObj : $this->getQuery();

		$prepare = $this->queryObj->getPrepare();

		return $prepare->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getEntity()
	{
		$entity = [];
		$array 	= $this->getArray();
		$preferences = [
			'table'	=> $this->preferences['global']['table'],
		];

		if(count($array) >= 1) {
			foreach ($array as $key => $value) {
				$preferences['id'] = $value['id'];

				$entity[] = new \Berie\ORM\Entity($value, $preferences);
			}
		} else {

		}

		return $entity;
	}

	public function getCount()
	{
		$this->queryObj = !empty($this->queryObj) ?
			$this->queryObj : $this->getQuery();

		$prepare = $this->queryObj->getPrepare();
		return $prepare->rowCount();
	}

	private function formOperation()
	{
		$query = '';
		$global = $this->preferences['global'];

		$fields = !empty($global['fields']) ?
			$global['fields'] : null;

		$count = !empty($global['count']) ?
			$global['count'] : false;

		$table  = !empty($global['table']) ?
			$global['table'] : null;

		$alias = !empty($global['alias']) ?
			$global['alias']: null;

		$alias_dot = !empty($alias) ?
			$alias . "." : null;

		$set = !empty($global['set']) ?
			$global['set'] : null;

		$where = !empty($global['where']) ?
			$global['where'] : null;

		if($this->preferences['operation'] == self::SELECT) {
			if($count == true) {
				$query .= "SELECT count(*)";
				$query .= " FROM `" . $table . "`";
			} elseif(empty($fields)) {
				$query .= "SELECT *";
				$query .= " FROM `" . $table . "`";

			} else {
				$query .= "SELECT";

				for($i = 0; $i < count($fields); $i++) {
					$field = $fields[$i];

					$field = !empty($alias) ?
						$alias_dot . $field : $field;

					$query .= $i == 0 ?
						" `" . $field . "`" : ", `" . $field . "`";
				}

				$query .= " FROM";
				$query .= !empty($alias) ?
					" `" . $table . "` AS `" . $alias . "`" : " `" . $table . "`";
			}

			if(isset($where)) {
				$query .= " WHERE " . $where;
			}
 		}

		if($this->preferences['operation'] == self::INSERT
			&& !empty($set)
		) {
			$query .= "INSERT INTO `" . $table . "`";

			$keyLine = '';
			$valLine = '';

			$index = 0;
			foreach ($set as $key => $val) {
				$keyLine .= $index == 0 ?
					"`" . $key . "`" : ", `" . $key . "`";

				$valLine .= $index == 0 ?
					"'" . $val . "'" : ", '" . $val . "'";

				$index++;
			}

			$query .= " (" . $keyLine . ")";
			$query .= " VALUES(" . $valLine . ")";
		}

		if($this->preferences['operation'] == self::UPDATE
			&& !empty($set)
			&& !empty($where)
		) {
			$query .= "UPDATE `" . $table . "`";
			$query.= " SET";

			$index = 0;
			foreach ($set as $key => $value) {
				$query .= $index == 0 ?
					" `" . $key . "`='" . $value . "'" :
					", `" . $key . "`='" . $value . "'";

				$index++;
			}

			$query .= " WHERE " . $where;
		}

		if($this->preferences['operation'] == self::DELETE
			&& !empty($where)
		) {
			$query .= "DELETE";
			$query .= " FROM `" . $table . "`";
			$query .= " WHERE " . $where;
		}

		return $this->query = $query;
	}
}
