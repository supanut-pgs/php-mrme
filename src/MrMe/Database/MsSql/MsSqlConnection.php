<?php
namespace MrMe\Database\MsSql;

use Exception;
use MrMe\Database\IConnection as IDatabaseConnection;

class MsSqlConnection implements IDatabaseConnection
{
	public  $_CONFIG    = 0;

	private $connection = 0;
	private $statement  = 0;
	private $logger     = 0;
	private $charset    = 0;
	private $collection = 0;

	public function __construct($_CONFIG, $charset = "UTF-8", $logger = null)
	{
		if (empty($_CONFIG)) return null;

		$this->_CONFIG = $_CONFIG;

		$this->charset = $charset;

		$this->logger  = $logger;

		if ($charset == "UTF-8")
			$this->setCollection("tis-620");
	}

	public function __destruct()
	{
		if (gettype($this->connection) == "boolean")
			$this->close();
	}

	public function setCollection($collection = "tis-620")
	{
		$this->collection = $collection;
	}

	public function connect()
	{
		if (empty($this->_CONFIG)) return 0;

		$host     = $this->_CONFIG['DB']['HOST'];
		$username = $this->_CONFIG['DB']['USERNAME'];
		$password = $this->_CONFIG['DB']['PASSWORD'];
		$name     = $this->_CONFIG['DB']['NAME'];

		if (!empty($this->_CONFIG['DB']['CONNECTION_STRING']))
			$constr   = $this->_CONFIG['DB']['CONNECTION_STRING'];

		$charset  = $this->charset;

		if (empty($constr))
			$constr = "odbc:Driver={SQL Server};Server=$host;Database=$name;Uid=$username;Pwd=$password;charset=$charset";

		$this->connection = new \PDO($constr);

		return $this->connection;
	}

	public function close()
	{
		if ($this->connection)
			$this->connection = null;
		return true;
	}

	

	public function query($sql,... $params)
	{
		if ($this->logger)
		{

			$this->logger->addInfo("Query Command : " . $sql);
			$this->logger->addInfo("Query Params : " . json_encode($params));
		}

		if (!$this->connection)
		{

			$err = array();
			$err["code"]    = "00001";
			$err["message"] = "No connection!";
			return $err;
			
		}

		$statement = $this->connection->prepare($sql);
		$this->statement = $statement;

		if (!$statement)
		{
			$err = array();
			$err["code"]    = $this->connection->errorCode();
			$err["message"] = $this->connection->errorInfo();
			return $err;
		}

		$success = $statement->execute($params);

		$err = 0;
		if (!$success)
		{
			$err = array();
			$err["code"]    = $statement->errorCode();
			$err["message"] = $statement->errorInfo();
		}
		return $err;
	}

	public function read()
	{
		
		$statement = $this->statement;

		$row = $statement->fetch();
		if (!$row) return 0;

		foreach ($row as $key => $r)
		{
			
			if (is_numeric($key))
			{
				unset($row[$key]);
			}
			else
			{
				if ($this->collection)
				{
					$row[$key] = iconv($this->collection, $this->charset, trim($row[$key]));
				}
				else
				{
					$row[$key] = trim($row[$key]);
				}
				
			}
		}

		$result = $row;
	
		return $row ? $result : 0;
	}

	public function readAll()
	{
		$results = array();

		while ($result = $this->read())
		{
			if (empty($result)) break;
			array_push($results, $result);
		}
		return $results;
	}

	public function getLastInsertId()
	{
		$statement = $this->statement;

		$result = $statement->fetch(\PDO::FETCH_ASSOC);

		return $result;
	}
}

?>