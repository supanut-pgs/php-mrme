<?php
namespace MrMe\Database\MySql;

use Exception;
use MrMe\Util\Util as Util;
use MrMe\Util\StringFunc as StringFunc;
use MrMe\Database\CommandBase as CommandBase;

class MySqlCommand extends CommandBase
{
	// private $clause_     = false;
	// private $logic       = false;
	// private $sql_clause_ = "";
	// private $sql         = "";
	// private $limit_      = "";
	// private $group_      = "";
	// private $order_      = "";

	// public  $params;
	// public  $connection;
	// public  $logger;

	public function __construct($connection = null, $logger = null)
	{
		parent::__construct($connection, $logger);
		// $this->params = array();
		// $this->connection = $connection;
		// $this->logger = $logger;
	}

	// public function __destruct()
	// {
	// 	unset($this->sql);
	// 	unset($this->params);
	// }

	// public function getLastInsertId()
	// {
	// 	return $this->connection->getLastInsertId();
	// }

	public function execute()
	{
		if (!$this->connection) return array("message" => "No connection !");

		//$this->sql.=$this->sql_clause_.$this->group_.$this->order_.$this->limit_;

		$prepareStatement = "";
		$prepareStatement = preg_replace("/@.[\w]*/", "?", $this->getFullQuery());

		if (empty($prepareStatement))
			$prepareStatement = $this->getFullQuery();

		$params = array();
		$params[0] = $prepareStatement;
		if (count($this->params) > 0)
			$params = array_merge($params, $this->params);


		//var_dump($params);
		$err = Util::callFunction($this->connection, 'query', $params);
		$this->reset();
		return $err;
	}

	public function executeReader()
	{
		if (!$this->connection) return array("message" => "No connection !");

		$prepareStatement = "";
		$prepareStatement = preg_replace("/@.[\w]*/", "?", $this->getFullQuery());

		if (empty($prepareStatement))
			$prepareStatement = $this->getFullQuery();

		$params = array();
		$params[0] = $prepareStatement;
		if (count($this->params) > 0)
			$params = array_merge($params, $this->params);

		$err = Util::callFunction($this->connection, 'query', $params);
		if ($err) die(json_encode($err));

		$model = $this->connection->readAll();

		$model = json_encode($model);
		$model = json_decode($model);
		$this->reset();
		return $model;
	}

	public function executeReaderFirst()
	{
		if (!$this->connection) return array("message" => "No connection !");

		$prepareStatement = "";
		$prepareStatement = preg_replace("/@.[\w]*/", "?", $this->getFullQuery());

		if (empty($prepareStatement))
			$prepareStatement = $this->getFullQuery();

		$params = array();
		$params[0] = $prepareStatement;
		if (count($this->params) > 0)
			$params = array_merge($params, $this->params);

		$err = Util::callFunction($this->connection, 'query', $params);
		if ($err) die(json_encode($err));

		$model = $this->connection->read();

		$model = json_encode($model);
		$model = json_decode($model);
		$this->reset();
		return $model;
	}

	public function select($table, $fields = "*", $clause = "")
	{
		if (gettype($fields) === "array")
		{
			$fields = implode(",", $fields);
		}

		//if (count($clause) == 0) $clause = null;
		$sql = "SELECT $fields FROM $table ";
		if (gettype($clause) === "array")
		{
			foreach ($clause as $i => $c)
			{
				if ((!empty($clause[$i + 1]) OR $i % 2 == 1) AND StringFunc::startWith("ORDER", $clase[$i+1]))
					$sql.= "$c ";

			}
		}
		else if (!empty($clause))
		{
			$this->setClause($clause);
			//$this->sql_clause_ .= $clause;
			//$sql.= $clause;
		}

		// if ($size > 0)
		// 	$sql.= "LIMIT $offset, $size ";
		$this->setSql($sql);
		//$this->sql = $sql;

		return $this;
	}

	public function insert($table, $field = [], $value = [])
	{
		if (count($field) > 0 && count($value) > 0)
		{
			if (is_array($value[0]))
			{
				// insert sets 
				foreach ($value as $i => $val)
					$value[$i] = "(".implode($val, ",").")";
				
				$field = implode($field, ",");
				$value = implode($value, ",");
			}
			else 
			{
				// insert single 
				foreach ($value as $i => $val)
				{
					if (empty($val))
					{
						unset($field[$i]);
						unset($value[$i]);
					}
				}
	
				$field = implode($field, ",");
				$value = "(".implode($value, ",").")";
			}
			
		}
		$sql = "INSERT INTO $table ($field) VALUES $value ";
		$this->setSql($sql);
		// $this->sql = $sql;
	}

	public function update($table, $sets = [], $clause = "")
	{
		foreach ($sets as $i => $s)
			if (empty($s)) unset($sets[$i]);

		$sets = implode($sets, ',');
		$sql = "UPDATE $table SET $sets ";

		if (gettype($clause) === "array")
		{
			foreach ($clause as $i => $c)
			{
				if (!empty($clause[$i + 1]) || $i % 2 == 1)
					$sql.= "$c ";

			}
		}
		else if (!empty($clause))
		{

			//$this->sql_clause_ .= $clause;
			$this->setClause($clause);
		}

		$this->setSql($sql);
		//$this->sql = $sql;
	}

	public function delete($table, $clause = "")
	{
		$sql = "DELETE FROM $table $clause";
		$this->setSql($sql);
	}

	public function bindParam($key, $value)
	{
		//echo $this->sql;
		preg_match_all("/@.[\w]*/", $this->getFullQuery(), $keywords);
		if (count($keywords) == 0) return;

		//$sql = preg_replace("/@.$key*/", "?", $this->getFullQuery());
		//var_dump($sql);
		//$this->setSql($sql);
		//var_dump($keywords);
		$index = array_search($key, $keywords[0]);
		// echo "<br>-------index------"; var_dump($index);
		// echo "<br>-------key----------"; var_dump($key);
		// echo "<br>-------value-----------"; var_dump($value);
		$params = $this->getParams();
		if (gettype($index) != "boolean")
		{
			$value = $value == 0 ? 0 : empty($value) ? "" : $value;

			$params[$index] = $value;
			//var_dump($this->params);
		}
		ksort($params);
		$this->setParams($params);

	}


	public function where($field, $opt = "", $value = "")
	{
		$sql = "";
		if (!empty($value) || is_numeric($value))
		{
			// if (!$this->wasClause())
			// {
				
			// 	$sql.= "WHERE ";
			// }
			$sql.= "WHERE $field $opt $value ";
		}
		
		$this->setClause($sql);
		//$this->sql_clause_.= $sql;

		return $this;
	}

	public function or($field = "", $opt = "", $value = "")
	{
		if (!empty($value) || is_numeric($value))
		{
			if ($this->wasLogic())
				$this->addClause(" OR $field $opt $value ");
			else
				$this->where($field, $opt, $value);
		}
		//$this->logic_ = true;
		return $this;
	}

	public function and($field = "", $opt = "", $value = "")
	{
		if (!empty($value) || is_numeric($value))
		{
			if ($this->wasLogic())
				$this->addClause(" AND $field $opt $value ");
			else
				$this->where($field, $opt, $value);
		}
		//$this->logic_ = true;
		return $this;
	}


	public function group(... $params)
	{
		if ($params)
		{
			$fields = implode(",", $params);
			if (!$this->wasGroup())
			{
				$this->addGroup(" GROUP BY $fields ");
			}
			else
			{
				$this->addGroup(",$fields ");
			}
		}
		return $this;
	}	

	public function order(... $params)
	{
		if ($params)
		{
			$params = implode(" ", $params);
			if (!$this->wasOrder())
			{
				$this->addOrder(" ORDER BY $params ");
			}
			else
			{
				$this->addOrder(", $params ");
			}
		}
		
		// $this->order_.= " ORDER BY $params ";
		return $this;
	}

	public function bracket($brkt)
	{
		//echo $this->clause_;
		if (!$this->clause_)
		{
			//$this->sql_clause_.= " WHERE ";
			$this->clause_ = true;
		}
		$this->sql_clause_.=" $brkt ";
		if (StringFunc::startWith($brkt, "("))
			$this->is_logic= false;
		return $this;
	}

	public function limit($offset, $size)
	{
		if ($offset + $size > 0)
		{
			$this->setLimit(" LIMIT $offset, $size ");
			//$this->limit_ .= " LIMIT $offset, $size ";
		}
		return $this;
	}

	
	

	// public function getClause()
	// {
	// 	return $this->sql_clause_ . " " . $this->group_;
	// }

	// public function setClause($clause)
	// {
	// 	$this->sql_clause_ = $clause;
	// }

	

	

	
	// private function reset()
	// {
	// 	$this->sql = 0;
	// 	$this->sql_clause_ = "";
	// 	$this->limit_ = "";
	// 	$this->params = array();
	// 	$this->clause_ = false;
	// 	$this->logic_  = false;
	// 	$this->group_ = "";
	// 	$this->order_ = "";
	// }

}
?>
