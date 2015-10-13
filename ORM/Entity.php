<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
 * @subpackage 	Entity
 * @author 		Eugen Melnychenko
 */
class Entity
{
	private $data;
	private $id;
	private $table;

	/**
	 * FORM ENTITY
	 *
	 * @param array $array
	 *
	 * @return \Berie\ORM\Entity
	 */
	function __construct(array $data, $preferences = [])
	{
		$this->data = new \Berie\ORM\EntityData($data);

		$this->id = isset($preferences['id']) ?
			$preferences['id'] : null;

		$this->table 	= isset($preferences['table']) ?
			$preferences['table'] : null;

		return $this;
	}

	/**
	 * GET VALUE
	 *
	 * @param sting $key
	 */
	function get($key)
	{
		if($key) {
			$this->data->{$key};
		}

		return;
	}

	/**
	 * SET VALUE
	 *
	 * @param sting $key
	 * @param sting $value
	 */
	function set($key, $value)
	{
		$this->data->{$key} = $value;

		return $this;
	}

	/**
	 * GET GLOBAL DATA
	 *
	 * @return array $data
	 */
	function getData()
	{
		$data = get_object_vars($this->data);

		return $data;
	}

	/**
	 * SET GLOBAL DATA
	 *
	 * @param array $data
	 */
	function setData($data)
	{
		foreach ($data as $key => $item) {
			$this->data->{$key} = $item;
		}

		return;
	}

	function getPreference($key)
	{
		return $this->{$key};
	}

	function setPreference($key, $value)
	{
		$this->{$key} = $value;

		return $this->{$key};
	}
}
