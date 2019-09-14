<?php
namespace MrMe\Database\MsSql;

use Exception;
use MrMe\Util\Util as Util;
use MrMe\Util\StringFunc as StringFunc;
use MrMe\Database\CommandBase as CommandBase;

class MsSqlCommand extends CommandBase
{
	public function __construct($connection = null, $logger = null)
	{
		parent::__construct($connection, $logger);
	}

	public function select($table, $fields = "*", $clause = "")
	{
		if (gettype($fields) === "array")
		{
			$fields = implode(',', $fields);
		}

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
		}

		$this->table = $table;
		$this->field = $fields;
		$this->setSql($sql);

		return $this;
	}

	public function insert($table, $fields = [], $values = [])
	{
		if (count($fields) > 0 && count($values) > 0)
		{
			foreach ($values as $i => $val)
			{
				if (empty($val))
				{
					unset($fields[$i]);
					unset($values[$i]);
					continue;
				}

				//$fields[$i] = "[" . $fields[$i] . "]";
			}
		}

		$fields = implode(",", $fields);
		$values = implode(",", $values);
		$sql = "INSERT INTO $table ($fields) VALUES ($values) ";
		
		$this->table = $table;
		$this->field = $fields;

		$this->setSql($sql);
	}

	public function update($table, $sets = [], $clause = "")
	{
		foreach ($sets as $i => $s) 
		{
			if (empty($s))
			{
				unset($sets[$i]);
			}
		}

		$sets = implode(",", $sets);
		$sql  = "UPDATE $table SET $sets ";
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
		$this->table = $table;
		$this->setSql($sql);
	}

	public function delete($table, $clause = "")
	{
		$sql = "DELETE FROM $table $clause";
		$this->table = $table;
		$this->setSql($sql);
	}

	public function bindParam($key, $value)
	{
		preg_match_all("/@.[\w]*/", $this->getFullQuery(), $keywords);
		if (count($keywords) == 0) return;

		//$sql = preg_replace("/@$key*/", "?", $this->getFullQuery());
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
			$value = empty($value) ? "" : $value;

			$params[$index] = $value;
			//var_dump($this->params);
		}
		ksort($params);
		$this->setParams($params);
	}

	public function execute()
	{
		if (!$this->connection) 
		{
			return array("message" => "No connection!");
		}

		$prepareStatement = "";
		$prepareStatement = preg_replace("/@.[\w]*/", "?", $this->getFullQuery());

		if (empty($prepareStatement))
			$prepareStatement = $this->getFullQuery();

		$params = array();
		$params[0] = $prepareStatement;
		if (count($this->params) > 0)
			$params = array_merge($params, $this->params);

		//var_dump($prepareStatement);
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
		// //echo $this->clause_;
		// if (!$this->clause_)
		// {
		// 	//$this->sql_clause_.= " WHERE ";
		// 	$this->clause_ = true;
		// }
		// $this->sql_clause_.=" $brkt ";
		// if (StringFunc::startWith($brkt, "("))
		// 	$this->logic_ = false;
		// return $this;
	}

	public function limit($offset, $size)
	{
		$first_field = explode(",", $this->field);
		if (count($first_field) == 0)
			$first_field = $this->field;
		else
			$first_field = $first_field[0];

		if ($offset + $size > 0)
		{
			$sql = "SELECT {$this->field} ";
			$sql.= "FROM (";
			$sql.= "SELECT *, ROW_NUMBER() OVER (ORDER BY $first_field ASC) AS rownumber ";
			$sql.= "FROM {$this->table} ) AS tmp ";
			$sql.= "WHERE rownumber >= $offset AND rownumber <= $size ";

			//$this->setLimit(" LIMIT $offset, $size ");
			//$this->limit_ .= " LIMIT $offset, $size ";
		}
		$this->setSql($sql);
		$this->is_logic = true;

		return $this;
	}
}
?>