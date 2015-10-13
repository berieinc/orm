<?php

namespace Berie;

use Berie\ORM\Internal;
use Berie\ORM\Query;
use Berie\ORM\Builder;
use Berie\ORM\Manager;

/**
 * @package Berie\ORM
 * @author Eugen Melnychenko
 */
class ORM
{
	private $connect;

	/**
	 * @param array $config
	 *
	 * @return \PDO
	 */
	public function __construct($config)
	{
		return $this->connect = (new Internal())->getPDO($config);
	}

	/**
	 * RUN QUERY REQUEST
	 *
	 * @param string $query
	 *
	 * $query = "SELECT * FROM `database.table` WHERE `id` = 1"
	 *
	 * @return \Berie\ORM\Query
	 */
	public function runQuery($query)
	{
		return new Query($this->connect, $query);
	}

	/**
	 * RUN QUERY BUILDER MANAGER
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function getBuilder()
	{
		return new Builder($this->connect);
	}

	/**
	 * RUN OBJECT MANAGER FROM EXIST DB TABLE
	 *
	 * @param string $table
	 *
	 * @return \Berie\ORM\Manager
	 */
	public function getManager($table)
	{
		return new Manager($this->connect, $table);
	}

	/**
	 * SAVE ENTITY
	 *
	 * @param \Berie\ORM\Entity $entity
	 *
	 * @return \Berie\ORM\Entity
	 */
	public function save(\Berie\ORM\Entity $entity)
	{
		$table = $entity->getPreference('table');

		$manager = new Manager($this->connect, $table);
		$entity  = $manager->saveEntity($entity);

		return $entity;
	}

	/**
	 * REMOVE ENTITY
	 *
	 * @param \Berie\ORM\Entity $entity
	 *
	 * @return \Berie\ORM\Entity
	 */
	public function remove(\Berie\ORM\Entity $entity)
	{
		$table = $entity->getPreference('table');

		$manager = new Manager($this->connect, $table);
		$manager->removeEntity($entity);
		
		return $entity;
	}
}
