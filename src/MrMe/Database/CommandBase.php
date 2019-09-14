<?php
namespace MrMe\Database;

use MrMe\Database\ICommand AS ICommand;

abstract class CommandBase implements ICommand
{
	public    $is_group    = false;
	public    $is_order    = false;
	public    $is_logic    = false;

	public    $clause_     = false;

	public    $sql_clause_ = "";
	public    $sql         = "";
	public    $limit_      = "";
	public 	  $group_      = "";
	public    $order_      = "";
	public    $table       = "";
	public    $fields      = [];

	public    $params      = 0;
	public    $connection  = 0;
	public    $logger      = 0;

	public function __construct($connection = null, $logger = null)
	{
		$this->params     = array();
		$this->connection = $connection;
		$this->logger     = $logger;
	}

	public function __destruct()
	{
		unset($this->sql);
		unset($this->params);
	}

	public function getFullQuery()
	{
		$sql = $this->sql.$this->sql_clause_.$this->group_.$this->order_.$this->limit_; 
		return $sql; 
	}

	public function getSql()
	{
		return $this->sql;
	}

	public function wasLogic()
	{
		return $this->is_logic;
	}

	public function wasClause()
	{
		return $this->clause_;
	}

	public function wasGroup()
	{
		return $this->is_group;
	}

	public function wasOrder()
	{
		return $this->is_order;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function setParams($params)
	{
		$this->params = $params;
	}

	public function getClause()
	{
		return $this->sql_clause_ . " " . $this->group_;
	}

	public function setClause($clause)
	{
		// if (!$this->clause_)
		// {
		// 	$this->sql    .= " WHERE ";
		// 	$this->clause_ = true;
		// 	$this->logic_  = true;
		// }

		$this->sql_clause_ = $clause;
		$this->clause_     = true;
		$this->is_logic    = true;
	}

	public function addClause($clause)
	{
		$this->clause_      = true;
		$this->is_logic     = true;
		$this->sql_clause_ .= $clause;
	}

	public function setSql($sql)
	{
		$this->sql = $sql;
	}

	public function addSql($sql)
	{
		$this->sql.= $sql;
	}

	public function setLimit($sql)
	{
		$this->limit_ = $sql;
	}

	public function addOrder($sql)
	{
		$this->order_.= $sql;
		$this->is_order = true;
	}

	public function addGroup($sql)
	{
		$this->group_.= $sql;

		$this->is_group = true;
	}

	public function getLastInsertId()
	{
		return $this->connection->getLastInsertId();
	}

	public function reset()
	{
		$this->is_group    = false;
		$this->is_order    = false;

		$this->clause_     = false;
		$this->is_logic    = false;
		$this->sql_clause_ = "";
		$this->sql         = "";
		$this->limit_      = "";
		$this->group_      = "";
		$this->order_      = "";

		$this->params      = array();
	}


}
?>