<?php
namespace MrMe\Database;

use MrMe\Util\Util as Util;

interface IConnection 
{
	public function connect(); 

	public function close();
	
	public function getLastInsertId();

	public function query($sql,... $params);

	public function read();

	public function readAll();
}
?>