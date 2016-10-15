<?php

namespace data\Base;

use Repel\Framework;

class RAdminQueryBase extends Framework\RActiveQuery {

	public function findByAdminId($admin_id) {
		return self::findByColumn("admin_id", $admin_id);
	}

	public function findByCompanyId($company_id) {
		return self::findByColumn("company_id", $company_id);
	}

	public function findByDeleted($deleted) {
		return self::findByColumn("deleted", $deleted);
	}

	public function findByEmail($email) {
		return self::findByColumn("email", $email);
	}

	public function findByFirstName($first_name) {
		return self::findByColumn("first_name", $first_name);
	}

	public function findByLastName($last_name) {
		return self::findByColumn("last_name", $last_name);
	}

	public function findByLogin($login) {
		return self::findByColumn("login", $login);
	}

	public function findByPassword($password) {
		return self::findByColumn("password", $password);
	}

	public function findOneByAdminId($admin_id) {
		return self::findOneByColumn("admin_id", $admin_id);
	}

	public function findOneByCompanyId($company_id) {
		return self::findOneByColumn("company_id", $company_id);
	}

	public function findOneByDeleted($deleted) {
		return self::findOneByColumn("deleted", $deleted);
	}

	public function findOneByEmail($email) {
		return self::findOneByColumn("email", $email);
	}

	public function findOneByFirstName($first_name) {
		return self::findOneByColumn("first_name", $first_name);
	}

	public function findOneByLastName($last_name) {
		return self::findOneByColumn("last_name", $last_name);
	}

	public function findOneByLogin($login) {
		return self::findOneByColumn("login", $login);
	}

	public function findOneByPassword($password) {
		return self::findOneByColumn("password", $password);
	}

	public function filterByAdminId($admin_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("admin_id", $admin_id, $operator);
	}

	public function filterByCompanyId($company_id, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("company_id", $company_id, $operator);
	}

	public function filterByDeleted($deleted, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("deleted", $deleted, $operator);
	}

	public function filterByEmail($email, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("email", $email, $operator);
	}

	public function filterByFirstName($first_name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("first_name", $first_name, $operator);
	}

	public function filterByLastName($last_name, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("last_name", $last_name, $operator);
	}

	public function filterByLogin($login, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("login", $login, $operator);
	}

	public function filterByPassword($password, $operator = Framework\ROperator::EQUAL) {
		return self::filterByColumn("password", $password, $operator);
	}

	public function orderByAdminId($order) {
		return self::orderByColumn("admin_id", $order);
	}

	public function orderByCompanyId($order) {
		return self::orderByColumn("company_id", $order);
	}

	public function orderByDeleted($order) {
		return self::orderByColumn("deleted", $order);
	}

	public function orderByEmail($order) {
		return self::orderByColumn("email", $order);
	}

	public function orderByFirstName($order) {
		return self::orderByColumn("first_name", $order);
	}

	public function orderByLastName($order) {
		return self::orderByColumn("last_name", $order);
	}

	public function orderByLogin($order) {
		return self::orderByColumn("login", $order);
	}

	public function orderByPassword($order) {
		return self::orderByColumn("password", $order);
	}

}
