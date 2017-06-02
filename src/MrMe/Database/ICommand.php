<?php
namespace MrMe\Database;

use MrMe\Util\Util as Util;

interface ICommand 
{
	
	public function select($table, $fields = "*", $clause = "");

	public function insert($table, $field = [], $value = []);

	public function update($table, $sets = [], $clause = "");

	public function delete($table, $clause = "");	

	public function bindParam($key, $value); 
	
	public function execute();

	public function executeReader();

	public function executeReaderFirst();

	public function where($field, $opt = "", $value = "");

	public function or($field = "", $opt = "", $value = "");

	public function and($field = "", $opt = "", $value = "");

	public function group(... $params);

	public function limit($offset, $size);

	public function order(... $params);

	public function bracket($brkt);

}
?>