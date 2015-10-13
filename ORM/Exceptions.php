<?php

namespace Berie\ORM;

/**
 * @package 	Berie\ORM
 * @subpackage 	Exceptions
 * @author 		Eugen Melnychenko
 */
class Exceptions extends \Berie\ORM
{
	function __construct()
	{

	}

	public function DBConnectionFailure(\PDOException $e)
	{
		echo "Error!: " . $e->getMessage() . "<br/>";
		die();
	}

	public function BuilderDoubleOperationError()
	{
		echo "Error!: Can`t call double MySQL operations<br/>";
		die();
	}

	public function BuilderDoubleTableError()
	{
		echo "Error!: Can`t call more than one MySQL tables for operation<br/>";
		die();
	}
}
