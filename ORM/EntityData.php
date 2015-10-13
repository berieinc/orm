<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
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
	 * @return \Berie\ORM\Entity
	 */
	function __construct(array $data)
	{
		foreach ($data as $key => $item) {
			$this->{$key} = $item;
		}

		return $this;
	}
}
