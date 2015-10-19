<?php

namespace Berie\ORM\Builder;

/**
 * @package 	Berie\ORM\Builder
 * @subpackage 	Factory
 * @author 		Eugen Melnychenko
 */
class Factory
{
	const FROM 		= 1;
	const WHERE 	= 2;
	const ORDER 	= 3;
	const LIMIT 	= 4;
	const OFFSET 	= 5;

	const JOIN 		= 7;

	public function parse($func, $data)
	{
		switch ($func) {
			case self::FROM:
				# code...
				break;

			case self::WHERE:
				$where = !empty($data["where"]) ?
					$data["where"] : '';

				if(is_array($data["column"])) {

					foreach ($data["column"] as $key => $val) {
						$where .= empty($where) ?
							"`" . $key . "`='" . $val . "'" :
							" AND `" . $key . "`='" . $val . "'";
					}

				} elseif(!empty($data["column"])
					&& empty($data["value"])
				) {

					$where .= empty($where) ?
						$data["column"] : " AND " . $data["column"];

				} elseif(!empty($data["column"])
					&& !empty($data["value"])
				) {

					$where .= empty($where) ?
						"`" . $data["column"] . "`='" . $data["value"] . "'" :
						" AND `" . $data["column"] . "`='" . $data["value"] . "'";
				} else {

				}

				return $where;
				break;

			case self::ORDER:
				if($data["type"] === "ASC"
					|| $data["type"] === "DESC"
				) {
					return "ORDER BY " . $data["column"] . " " . $data["type"];
				} else {
					return '';
				}
				break;

			case self::LIMIT:
				if(is_int($data["number"])
					|| !empty($data["number"])
				) {
					return "LIMIT " . $data["number"];
				} else {
					return '';
				}
				break;

			case self::OFFSET:
				if(is_int($data["number"])
					|| !empty($data["number"])
				) {
					return "OFFSET " . $data["number"];
				} else {
					return '';
				}
				break;
		}
	}

	public function generate($func, $data)
	{
		switch ($func) {
			case self::FROM:
				# code...
				break;

			case self::JOIN:
				return !empty($data) ?
					" " . $data : "";
				break;

			case self::WHERE:
				return !empty($data) ?
					" WHERE " . $data : "";
				break;

			case self::ORDER:
				return !empty($data) ?
					" " . $data : "";
				break;

			case self::LIMIT:
				return !empty($data) ?
					" " . $data : "";
				break;

			case self::OFFSET:
				return !empty($data) ?
					" " . $data : "";
				break;
		}
	}
}
