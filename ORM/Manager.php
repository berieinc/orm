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

	function generate()
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
