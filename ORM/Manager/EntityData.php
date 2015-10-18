<?php

namespace Berie\ORM\Manager;

/**
 * @package 	Berie\ORM\Manager
 * @subpackage 	EntityData
 * @author 		Eugen Melnychenko
 */
class EntityData
{
	/**
	 * SET ENTITY DATA
	 *
	 * @param array $array
	 *
	 * @return \Berie\ORM\Manager\Entity
	 */
	function __construct(array $data)
	{
		foreach ($data as $key => $item) {
			$this->{$key} = $item;
		}

		return $this;
	}
}
