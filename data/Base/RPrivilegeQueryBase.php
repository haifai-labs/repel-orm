<?php

namespace data\Base;

use Repel\Framework;

class RPrivilegeQueryBase extends Framework\RActiveQuery {

	public function findByDescription($description) {
		return self::findByColumn("description", $description);
	}

	public function findByName($name) {
		return self::findByColumn("name", $name);
	}

	public function findByPrivilegeId($privilege_id) {
		return self::findByColumn("privilege_id", $privilege_id);
	}

	public function findOneByDescription($description) {
		return self::findOneByColumn("description", $description);
	}

	public function findOneByName($name) {
		return self::findOneByColumn("name", $name);
	}

	public function findOneByPrivilegeId($privilege_id) {
		return self::findOneByColumn("privilege_id", $privilege_id);
	}

	public function filterByDescription($description, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("description", $description, $operator);
	}

	public function filterByName($name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("name", $name, $operator);
	}

	public function filterByPrivilegeId($privilege_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("privilege_id", $privilege_id, $operator);
	}

	public function orderByDescription($order) {
		return self::orderByColumn("description", $order);
	}

	public function orderByName($order) {
		return self::orderByColumn("name", $order);
	}

	public function orderByPrivilegeId($order) {
		return self::orderByColumn("privilege_id", $order);
	}

}
