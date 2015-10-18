<?php

namespace Berie\ORM\DataType;

use Berie\ORM\Exceptions\Exceptions;

/**
 * @package 	Berie\ORM\DataType
 * @subpackage 	Factory
 * @author 		Eugen Melnychenko
 */
class Factory
{
	private $types = [
		"INTEGER" => [
			"INT", "TINYINT",
			"SMALLINT", "MEDIUMINT",
		],
		"FLOAT" => [
			"FLOAT",
		],
		"STRING" => [
			"CHAR", "VARCHAR",
			"TINYTEXT", "TEXT",
			"MEDIUMTEXT", "LONGTEXT",
		],
		"DATETIME" => [
			"DATE", "DATETIME",
			"TIMESTAMP", "TIME",
			"YEAR",
		],
	];

	public function parseToSelect($data)
	{
		foreach ($data["data"] as $num => $item) {
			foreach ($item as $column => $value) {
				$glob 	= $data["pref"][$column]["type"]["glob"];
				$def 	= $data["pref"][$column]["def"];

				switch ($glob) {
					case "INTEGER":
						$value = !empty($value) ?
							(int) $value : (int) $def;
						break;

					case "FLOAT":
						$value = !empty($value) ?
							(float) $value : (float) $def;
						break;

					case "STRING":
						if(@unserialize($value)) {
							$value = @unserialize($value);
						} else {
							$value = !empty($value) ?
								(string) $value : (string) $def;
						}
						break;

					case "DATETIME":
						$value = !empty($value) ?
							new \DateTime($value) : new \DateTime("now");
						break;
				}

				$data["data"][$num][$column] = $value;
			}
		}

		return $data;
	}

	public function managerDataFilter(\Berie\ORM\Entity $entity, $key, $value)
	{
		$data = $entity->getData();

		switch ($entity->getPref()["dataType"][$key]["type"]["glob"]) {
			case "INTEGER":
				if(is_int($value)) {
					$data[$key] = (int) $value;
				}
				break;

			case "FLOAT":
				if(is_float($value)) {
					$data[$key] = (float) $value;
				}
				break;

			case "STRING":
				if(is_string($value)) {
					$data[$key] = (string) $value;
				} elseif(is_array($value)) {
					$data[$key] = (array) $value;
				}
				break;

			case "DATETIME":
				if(is_object($value)
					&& get_class($value) === "DateTime"
				) {
					$data[$key] = $value;
				} elseif(\DateTime::createFromFormat('Y-m-d', $value)
					|| \DateTime::createFromFormat('Y-m-d H:i:s', $value)
				) {
					$def = $entity->getPref()["dataType"][$key]['def'];

					$data[$key] = !empty($def) ?
						new \DateTime($def) : new \DateTime($value);
				}
				break;
		}

		return $data;
	}

	public function managerPreSave(\Berie\ORM\Entity $entity)
	{
		$data 		= $entity->getData();
		$dataType 	= $entity->getPref()["dataType"];

		foreach ($data as $key => $value) {
			switch ($dataType[$key]["type"]["glob"]) {
				case "STRING":
					if (is_array($value)) {
						if($dataType[$key]["type"]["sub"] === "TINYTEXT"
						|| $dataType[$key]["type"]["sub"] === "TEXT"
						|| $dataType[$key]["type"]["sub"] === "MEDIUMTEXT"
						|| $dataType[$key]["type"]["sub"] === "LONGTEXT") {
							$data[$key] = @serialize($value);
						} else {
							// exception serialize array
						}
					}
					break;

				case "DATETIME":
					if(is_object($value)
						&& get_class($value) === "DateTime"
					) {
						if($dataType[$key]["type"]["sub"] === "DATE") {
							$data[$key] = $value->format("Y-m-d");
						} elseif($dataType[$key]["type"]["sub"] === "DATETIME") {
							$data[$key] = $value->format("Y-m-d H:i:s");
						} elseif($dataType[$key]["type"]["sub"] === "TIMESTAMP") {
							$data[$key] = $value->format("U");
						} elseif($dataType[$key]["type"]["sub"] === "TIME") {
							$data[$key] = $value->format("H:i:s");
						} elseif($dataType[$key]["type"]["sub"] === "YEAR") {
							$data[$key] = $value->format("Y");
						}
					} else {
						if(\DateTime::createFromFormat("Y-m-d", $value)
							|| \DateTime::createFromFormat("Y-m-d H:i:s", $value)
							|| \DateTime::createFromFormat("U", $value)
							|| \DateTime::createFromFormat("H:i:s", $value)
							|| \DateTime::createFromFormat("Y", $value)
						) {
							$data[$key] = $value;
						} else {
							// exception not datetime
						}
					}
					break;
			}
		}

		return $data;
	}

	/**
	 * @param string $table
	 *
	 * @return \Berie\ORM\Builder
	 */
	public function parseData($data)
	{
		$return = [];

		foreach ($data as $key => $value) {
			$return[$value['Field']] = [
				'name'	=> $value['Field'],
				'type' 	=> $this->parseDATAType($value['Type']),
				'null'	=> $this->parseDATANull($value['Null']),
				'def'	=> $value['Default'],
			];
		}

		return $return;
	}

	private function parseDATAType($value)
	{
		$return = [];

		$types = [
			"INTEGER" => [
				"INT", "TINYINT", "SMALLINT", "MEDIUMINT",
			],
			"FLOAT" => [
				"FLOAT",
			],
			"STRING" => [
				"CHAR", "VARCHAR", "TINYTEXT", "TEXT",
				"MEDIUMTEXT", "LONGTEXT",
			],
			"DATETIME" => [
				"DATE", "DATETIME", "TIMESTAMP", "TIME",
				"YEAR",
			],
		];

		foreach ($types as $globtype => $subtypes) {
			foreach ($subtypes as $subtype) {
				if(substr(strtoupper($value), 0, strlen($subtype)) === $subtype) {
					$return = [
						"glob" 	=> $globtype,
						"sub"	=> $subtype,
					];
				}
			}
		}

		return $return;
	}

	private function parseDATANull($value)
	{
		return $value === "NO" ?
			false : true;
	}
}
