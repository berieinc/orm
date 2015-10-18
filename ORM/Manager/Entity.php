<?php

namespace Berie\ORM\Manager;

/**
 * @package 	Berie\ORM\Manager
 * @subpackage 	Entity
 * @author 		Eugen Melnychenko
 */
class Entity
{
	private $data;
	private $pref;

	/**
	 * FORM ENTITY
	 *
	 * @param array $array
	 *
	 * @return \Berie\ORM\Entity
	 */
	function __construct(array $data, $preferences = [])
	{
		$this->data = new \Berie\ORM\Manager\EntityData($data);

		$this->pref = $preferences;

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
			return $this->data->{$key};
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
		$this->data = (new \Berie\ORM\DataType\Factory())
			->managerDataFilter($this, $key, $value);

		$this->data = new \Berie\ORM\Manager\EntityData($this->data);

		return $value;
	}

	/**
	 * GET GLOBAL DATA
	 *
	 * @return array $data
	 */
	function getData()
	{
		return get_object_vars($this->data);
	}

	/**
	 * SET GLOBAL DATA
	 *
	 * @param array $data
	 */
	function setData($data)
	{
		foreach ($data as $key => $item) {
			$this->data = (new \Berie\ORM\DataType\Factory())
				->managerDataFilter($this, $key, $item);
		}

		$this->data = new \Berie\ORM\Manager\EntityData($this->data);

		return;
	}

	function getPref()
	{
		return $this->pref;
	}

	function setPref($key, $value)
	{
		$this->{$key} = $value;

		return $this->{$key};
	}
}
