<?php
namespace MrMe\Database\MySql;

use MrMe\Util\Util as Util;
use MrMe\Database\IConnection as IDatabaseConnection;

class MySqlConnection implements IDatabaseConnection
{
	public  $_CONFIG = 0;

	private $connection = 0;
	private $statement  = 0;
	private $logger     = 0;
	private $charset    = 0;

	public function __construct($_CONFIG, $charset = "utf8", $logger = null)
	{
		if (empty($_CONFIG)) return null;

		$this->_CONFIG = $_CONFIG;
		
		$this->charset = $charset;

		$this->logger = $logger;

		//$this->connect();
	}

	public function __destruct()
	{
		//var_dump($this->connection);
		// if ($this->connection)
		// 	$this->connection->close();
		// if (gettype($this->connection) == "boolean")
		//$this->close();
		// return true;
	}

	public function connect()
	{
		if (empty($this->_CONFIG)) return 0;

		$host     = $this->_CONFIG['DB']['HOST'];
		$username = $this->_CONFIG['DB']['USERNAME'];
		$password = $this->_CONFIG['DB']['PASSWORD'];
		$name     = $this->_CONFIG['DB']['NAME'];

		$this->connection = mysqli_connect($host, $username, $password, $name);
		$this->connection->set_charset($this->charset);

		return $this->connection;
	}

	public function close()
	{
		if (!empty($this->connection) && strcmp(gettype($this->connection), "boolean") != 0)
			 unset($this->connection);

		unset($this->_CONFIG);
		unset($this->statement);
		return true;
	}

	public function getLastInsertId()
	{
		return mysqli_insert_id($this->connection);
	}

	public function query($sql,... $params)
	{
		if ($this->logger)
		{

			$this->logger->addInfo("Query Command : " . $sql);
			$this->logger->addInfo("Query Params : " . json_encode($params));
		}
		
		$statement = $this->connection->prepare($sql);
		
		if (!$statement)
		{
			$err = array("code" => $this->connection->errno, 
                         "message" => $this->connection->error);
			return $err;
		}

		if (count($params) > 0)
		{
			//var_dump($params);
			$type = $this->getTypeFromParams($params);
			$params = array_merge(array($type), $params);
			Util::callFunction($statement, "bind_param", $params);
		}

		$result = $statement->execute();
	
        $err = 0;
        if (!$result)
        {
            $err = array("code" => $statement->errno, 
                         "message" => $statement->error);
        }

        $this->statement = $statement;
		
        return $err;

	}

	public function read()
	{
		$stmt = $this->statement;
        $meta = $stmt->result_metadata();
        $result = array();
        while ($field = $meta->fetch_field())
        {
            $result[$field->name] = NULL;
            $params[] = &$result[$field->name];
        }

        Util::callFunction($stmt, 'bind_result', $params);

        // call_user_func_array(array($stmt, 'bind_result'), $params);
        
        $fetch = $stmt->fetch();
        return $fetch ? $result : 0;
	}

	public function readAll()
	{
		$results = array();
		while ($result = $this->read())
			array_push($results, $result);
		return $results;
	}

	private function getTypeFromParams($params)
	{
		
		$type = "";
		foreach ($params as $p)
		{
			switch(gettype($p))
			{
				case "string":
					$type.="s";
					break;
				case "object":
					$type.="b";
					break;
				case "double":
					$type.="d";
					break;
				case "integer":
				case "boolean":
					$type.="i";
					break;
			}
		}
		return $type;
	}
}
?>