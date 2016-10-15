<?php
use PHPUnit\Framework\TestCase;

use Repel\Framework;
use PHPUnit_Extensions_Database_DataSet_QueryDataSet as QueryDataSet;
use PHPUnit_Extensions_Database_DataSet_YamlDataSet as YamlDataSet;
use PHPUnit_Extensions_Database_DataSet_DataSetFilter as DataSetFilter;
class Collection_TestCase extends \PHPUnit_Extensions_Database_TestCase {


protected function getSetUpOperation() {
  $sth = $this->pdo->query("
  SELECT 'SELECT SETVAL(' ||
  quote_literal(quote_ident(PGT.schemaname) || '.' || quote_ident(S.relname)) ||
  ', COALESCE(MAX(' ||quote_ident(C.attname)|| '), 1) ) FROM ' ||
  quote_ident(PGT.schemaname)|| '.'||quote_ident(T.relname)|| ';'
  FROM pg_class AS S,
  pg_depend AS D,
  pg_class AS T,
  pg_attribute AS C,
  pg_tables AS PGT
  WHERE S.relkind = 'S'
  AND S.oid = D.objid
  AND D.refobjid = T.oid
  AND D.refobjid = C.attrelid
  AND D.refobjsubid = C.attnum
  AND T.relname = PGT.tablename
  AND PGT.schemaname = 'public'
  ORDER BY S.relname;"
);
$result = $sth->fetchAll();
foreach ($result as $res) {
  print_r($res[0]);
  $sth2 = $this->pdo->query($res[0]);
  $result = $sth->fetchAll();
}
  return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT(TRUE);
  //                                                                 ⬆⬆⬆
}

/**
* @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
*/
public function getConnection() {
  $config =  require('../src/Repel/Config/db_config.php');
  if (!empty($config)){
    $config = $config['databases']['primary'];
    $this->pdo = new PDO($config['driver'],$config['username'],$config['password']);
    return $this->createDefaultDBConnection($this->pdo, $config['database']);
  }
  return null;
}

/**
* @return PHPUnit_Extensions_Database_DataSet_IDataSet
*/
public function getDataSet()  {
  return new YamlDataSet(
  dirname(__FILE__)."/DataSets/initial.yml"
);
}

public function testAddEntry()
{
  $expected = new YamlDataSet(
  dirname(__FILE__)."/DataSets/company.yml"
);

$company = new data\DCompany();
$company2 = new data\DCompany();

$company->name = "Aplinet";
$company->full_name = "Piotr Dziurla";
$company->save();

$company2->name = "Sanpo";
$company2->full_name = "Konrad Frysiak";
$company2->save();

//  $expected = new DataSetFilter($expected);
//  $expected->setExcludeColumnsForTable('companies', ['company_id']);

$actual = new QueryDataSet($this->getConnection());
$actual->addTable('companies');
//  $actual = new DataSetFilter($actual);
//  $actual->setExcludeColumnsForTable('companies', ['company_id']);

$this->assertDataSetsEqual($expected, $actual);

}

public function testConstructCollection()  {
  $collection = new Framework\RCollection();
  $this->assertEquals($collection instanceof Framework\RCollection,true);
}


public function testAddAndGetItem()  {
  $company = new data\DCompany();
  $collection = new Framework\RCollection();

  $collection->add($company);
  $get = $collection->get(0);

  $this->assertEquals($company,$get);
}


public function testCountItems()  {
  $company = new data\DCompany();
  $company2 = new data\DCompany();

  $collection = new Framework\RCollection();

  $collection->add($company);
  $collection->add($company2);

  $this->assertEquals(count($collection),2);
}

/**
* @depends testCountItems
*/
public function testAddExistingItem()  {
  $company = new data\DCompany();
  $collection = new Framework\RCollection();

  $result1 = $collection->add($company);
  $result2 = $collection->add($company);

  $this->assertNotFalse($result1);
  $this->assertFalse($result2);

  $this->assertEquals(1,count($collection));
}

/**
* @depends testCountItems
*/
public function testAddWrongItem()  {
  $company = new \stdClass();
  $collection = new Framework\RCollection();

  $this->expectException("Repel\Exceptions\InvalidTypeException");
  $result1 = $collection->add($company);
  $this->assertEquals(0,count($collection));
}



public function testGetItem()  {
  $company = new data\DCompany();
  $company2 = new data\DCompany();
  $company3 = new data\DCompany();


  $collection = new Framework\RCollection();

  $collection->add($company);
  $collection->add($company2);
  $collection->add($company3);

  $get_company2 = $collection->get($company2);
  $get_company3 = $collection->get(2);

  $this->assertEquals($company2,$get_company2);
  $this->assertEquals($company3,$get_company3);

}

public function testRemoveItemByIndex()  {
  $company = new data\DCompany();
  $company2 = new data\DCompany();

  $collection = new Framework\RCollection();

  $collection->add($company);
  $collection->add($company2);
  $removed = $collection->remove(0);

  $this->assertEquals($removed,$company);
  $this->assertEquals($collection->get(0),$company2);
  $this->assertEquals(1,count($collection));
}

public function testRemoveItemByReference()  {
  $company = new data\DCompany();
  $company2 = new data\DCompany();

  $collection = new Framework\RCollection();

  $collection->add($company);
  $collection->add($company2);

  $removed = $collection->remove($company);

  $this->assertEquals($company,$removed);
  $this->assertEquals($company2,$collection->get(0));
  $this->assertEquals(1,count($collection));
}

public function testRemoveItemWrongIndex()  {
  $collection = new Framework\RCollection();
  $company = new data\DCompany();

  $removed = $collection->remove(0);
  $this->assertEquals($removed,null);
  $removed = $collection->remove($company);
  $this->assertEquals($removed,null);

}

public function testForeach()  {
  $company = new data\DCompany();
  $company2 = new data\DCompany();

  $collection = new Framework\RCollection();

  $collection->add($company);
  $collection->add($company2);

  $index = 0;
  foreach ($collection as $item) {
    $this->assertEquals($item,$collection->get($index));
    $index++;
  }
  $this->assertEquals(count($collection),$index);
  // $this->assertEquals($collection instanceof Framework\RCollection,true);
}

}
