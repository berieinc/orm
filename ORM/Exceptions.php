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
		$this->getException($e->getMessage());

		return;
	}

	public function B0020()
	{
		$message = '[B0020] ORM: Can`t call double MySQL operations.';

		$this->getException($message);

		return;
	}

	public function B0025()
	{
		$message = '[B0025] ORM: Can`t call more than one MySQL tables for operation.';

		$this->getException($message);

		return;
	}

	public function B0040()
	{
		$message = '[B0040] ORM: Can`t execute SET in SELECT and DELETE.';

		$this->getException($message);

		return;
	}

	public function B0050()
	{
		$message = '[B0050] ORM: Can`t execute WHERE in INSERT.';

		$this->getException($message);

		return;
	}

	public function B0060()
	{
		$message = '[B0060] ORM: Can`t execute ORDER BY in INSERT and UPDATE or call this function twice';

		$this->getException($message);

		return;
	}

	public function B0065()
	{
		$message = '[B0065] ORM: Can`t execute LIMIT in INSERT and UPDATE or call this function twice';

		$this->getException($message);

		return;
	}

	public function B0070()
	{
		$message = '[B0070] ORM: Can`t execute OFFSET in INSERT and UPDATE or call this function twice';

		$this->getException($message);

		return;
	}

	private function getException($message)
	{
		trigger_error($message);
		error_log($message);
		die;
	}
}
