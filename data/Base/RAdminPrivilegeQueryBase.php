<?php

namespace data\Base;

use Repel\Framework;

class RAdminPrivilegeQueryBase extends Framework\RActiveQuery {

	public function findByAdminId($admin_id) {
		return self::findByColumn("admin_id", $admin_id);
	}

	public function findByDate($date) {
		return self::findByColumn("date", $date);
	}

	public function findByDeleted($deleted) {
		return self::findByColumn("deleted", $deleted);
	}

	public function findByPrivilegeId($privilege_id) {
		return self::findByColumn("privilege_id", $privilege_id);
	}

	public function findOneByAdminId($admin_id) {
		return self::findOneByColumn("admin_id", $admin_id);
	}

	public function findOneByDate($date) {
		return self::findOneByColumn("date", $date);
	}

	public function findOneByDeleted($deleted) {
		return self::findOneByColumn("deleted", $deleted);
	}

	public function findOneByPrivilegeId($privilege_id) {
		return self::findOneByColumn("privilege_id", $privilege_id);
	}

	public function filterByAdminId($admin_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("admin_id", $admin_id, $operator);
	}

	public function filterByDate($date, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("date", $date, $operator);
	}

	public function filterByDeleted($deleted, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("deleted", $deleted, $operator);
	}

	public function filterByPrivilegeId($privilege_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("privilege_id", $privilege_id, $operator);
	}

	public function orderByAdminId($order) {
		return self::orderByColumn("admin_id", $order);
	}

	public function orderByDate($order) {
		return self::orderByColumn("date", $order);
	}

	public function orderByDeleted($order) {
		return self::orderByColumn("deleted", $order);
	}

	public function orderByPrivilegeId($order) {
		return self::orderByColumn("privilege_id", $order);
	}

}
