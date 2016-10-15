<?php

namespace data\Base;

use Repel\Framework;

class RCompanyQueryBase extends Framework\RActiveQuery {

	public function findByCompanyId($company_id) {
		return self::findByColumn("company_id", $company_id);
	}

	public function findByDeleted($deleted) {
		return self::findByColumn("deleted", $deleted);
	}

	public function findByFullName($full_name) {
		return self::findByColumn("full_name", $full_name);
	}

	public function findByName($name) {
		return self::findByColumn("name", $name);
	}

	public function findOneByCompanyId($company_id) {
		return self::findOneByColumn("company_id", $company_id);
	}

	public function findOneByDeleted($deleted) {
		return self::findOneByColumn("deleted", $deleted);
	}

	public function findOneByFullName($full_name) {
		return self::findOneByColumn("full_name", $full_name);
	}

	public function findOneByName($name) {
		return self::findOneByColumn("name", $name);
	}

	public function filterByCompanyId($company_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("company_id", $company_id, $operator);
	}

	public function filterByDeleted($deleted, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("deleted", $deleted, $operator);
	}

	public function filterByFullName($full_name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("full_name", $full_name, $operator);
	}

	public function filterByName($name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("name", $name, $operator);
	}

	public function orderByCompanyId($order) {
		return self::orderByColumn("company_id", $order);
	}

	public function orderByDeleted($order) {
		return self::orderByColumn("deleted", $order);
	}

	public function orderByFullName($order) {
		return self::orderByColumn("full_name", $order);
	}

	public function orderByName($order) {
		return self::orderByColumn("name", $order);
	}

}
