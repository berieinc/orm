<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
 * @subpackage 	Manager
 * @author 		Eugen Melnychenko
 */
class Manager extends \Berie\ORM
{
	function __construct($database, $table)
	{
		$this->table 		= $table;
		$this->database 	= $database;
	}

	function create()
	{
		$table = $this->table;

		$query 	= "SHOW COLUMNS FROM " . $table;
		$prepare = (new \Berie\ORM\Query($this->database, $query))
			->getPrepare();

		$data = [];

		foreach ($prepare->fetchAll(\PDO::FETCH_ASSOC) as $value) {
			$data[$value['Field']] = null;
		}

		$preferences['table'] = $table;

		return new \Berie\ORM\Entity($data, $preferences);
	}

	function find($identifier)
	{
		$object = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->where('id', $identifier)
			->getEntity();

		return $object[0];
	}

	function findAll()
	{
		$objects = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->getEntity();

		return $objects;
	}

	function findOneBy($condition = [])
	{
		$object = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->where($condition)
			->getEntity();

		return $object[0];
	}

	function findBy($condition = [])
	{
		$object = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->where($condition)
			->getEntity();

		return $object;
	}

	function countAll()
	{
		$count = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->getCount();

		return $count;
	}

	function countBy($condition = [])
	{
		$count = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->where($condition)
			->getCount();

		return $count;
	}

	function exist($identifier)
	{
		$query = (new \Berie\ORM\Builder($this->database))
			->select()
			->from($this->table)
			->where('id', $identifier)
			->queryRequest();

		$query = "SELECT EXISTS(" . $query . ")";

		$prepare = (new \Berie\ORM\Query($this->database, $query))
			->getPrepare();

		return (array_values($prepare->fetchAll(\PDO::FETCH_ASSOC)[0])[0] === '1') ?
			true : false;
	}

	private function findRelationship($table)
	{
		$query = "SELECT "
			. "`TABLE_SCHEMA`, `TABLE_NAME`, `COLUMN_NAME`, "
			. "`REFERENCED_TABLE_SCHEMA`, `REFERENCED_TABLE_NAME`, "
			. "`REFERENCED_COLUMN_NAME` "
			. "FROM "
			. "`INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` "
			. "WHERE "
			. "`TABLE_SCHEMA` = SCHEMA() "
			. "AND `REFERENCED_TABLE_NAME` IS NOT NULL";
	}

	function saveEntity(\Berie\ORM\Entity $entity)
	{
		$id 	= $entity->getPreference('id');
		$table 	= $entity->getPreference('table');
		$data 	= $entity->getData();

		if(!empty($id)) {
			(new \Berie\ORM\Builder($this->database))
				->update($table)
				->set($data)
				->where('id', $id)
				->getQuery();
		} else {
			unset($data['id']);

			(new \Berie\ORM\Builder($this->database))
				->insert($table)
				->set($data)
				->getQuery();

			$id = $this->database->lastInsertId();

			$entity->set('id', $id);
			$entity->setPreference('id', $id);
		}

		return $entity;
	}

	function removeEntity(\Berie\ORM\Entity $entity)
	{
		$id 	= $entity->getPreference('id');
		$table 	= $entity->getPreference('table');
		$data 	= $entity->getData();

		if(!empty($id)) {
			(new \Berie\ORM\Builder($this->database))
				->delete()
				->from($table)
				->where('id', $id)
				->getQuery();
		}

		return;
	}
}
