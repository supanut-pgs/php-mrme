<?php
namespace MrMe\Database;

use Exception;
use MrMe\Util\Logger;
use PDO; 

class DBBase 
{  
    public static $ORDER_DESC   = "DESC";
    public static $ORDER_ASC    = "ASC";

    public static $JOIN         = "JOIN";

    protected $DatabaseConnection;
    protected $Logger; 
    protected $sql; 

    private $_pdo;
    
    //private $_pdo;
    private $_count_query_time;

    protected static $__instance; 

    protected function __construct()
    {
        // get default database config 
        $config = getJsonConfig();
        $config = $config->database[0];

        $dbcon  = $config->connection;
        $dbhost = $config->host;
        $dbname = $config->name; 
        $dbuser = $config->username;
        $dbpass = $config->password;

        $connection_string = "$dbcon:dbname={$dbname};host={$dbhost}";
      
        // var_dump($this);
        // self::$__count_query_time = 0;
        // self::$__pdo = new PDO($connection_string, $dbuser, $dbpass);
        $this->_count_query_time = 0;

        $this->_pdo = new PDO($connection_string, $dbuser, $dbpass);
    }

    protected function __destruct()
    {

    }

    public static function query($sql)
    {
        // self::$__count_query_time++;

        // if($logger = Logger::get('DUMP_DATABASE_LOG'))
        // {
        //     $param_text = json_encode($params);
        //     $logger->info("query('$sql', $param_text)");
        // }

        $class = get_called_class();
        self::$__instance = new $class; 
        self::$__instance->sql = $sql;
        
        // self::$__pdo = self::$__pdo->prepare($sql);

        return self::$__instance;
    }

    public function execute($params = [])
    {
        // dd(self::$__pdo);
        $sql = self::$__instance->sql;
        self::$__instance->_pdo = self::$__instance->_pdo->prepare($sql); 

        if($logger = Logger::get('DUMP_DATABASE_LOG'))
        {
            $logger->info("query('$sql')");
        }

        if($logger = Logger::get('DUMP_DATABASE_LOG'))
        {
            $params_text = json_encode($params);
            $logger->info("execute('$params_text')");
        }

        if (count($params) > 0)
            self::$__instance->_pdo->execute($params);
            //self::$__pdo->execute($params);
        else 
            self::$__instance->_pdo->execute();
            //self::$__pdo->execute();
           
        if (env('DEBUG_MODE') == 'true' && self::$__instance->_pdo->errorInfo()[1])
        {
            $error = json_encode(self::$__instance->_pdo->errorInfo());
            if($logger = Logger::get('DUMP_DATABASE_LOG'))
            {
                $params_text = json_encode($params);
                $logger->error($error);
            }
            throw new Exception($error);
        }
                    
        return self::$__instance; 
    }

    public function fetch($type = PDO::FETCH_ASSOC) 
    {
        return self::$__instance->_pdo->fetch($type);
    }

    public function first()
    {
        return json_decode(json_encode($this->fetch()));
    }

    public function last()
    {
        $model = $this->all();
        return $model ? $model[count($model)-1] : false;
    }

    public function all()
    {
        $result = [];
        //dd(self::$__instance->_pdo->fetch());
        while($rs = self::$__instance->_pdo->fetch(PDO::FETCH_ASSOC))
        {
            $result[] = json_decode(json_encode($rs)); 
        }

        return $result;
    }

    public static function lastInsertedId()
    {
        return self::$__instance->_pdo->lastInsertedId();
    }
}

class DB extends DBBase
{
    private $table; 
    private $_cause;
    
    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public static function table($table)
    {
        self::$__instance = new self();
        self::$__instance->table = $table; 

        return self::$__instance;
    }

    public function all()
    {
        if (!empty($this->sql)) return parent::all();

        $table = $this->table; 
        $sql = "SELECT * FROM $table ";
      
        return self::query($sql)->execute()->all();
    }

    public function first()
    {
        if (!empty($this->sql)) return parent::first();

        $table = $this->table; 
        $sql = "SELECT * FROM $table ";
      
        return self::query($sql)->execute()->first();
    }

    public function last()
    {
        if (!empty($this->sql)) return parent::last();

        $table = $this->table; 
        $sql = "SELECT * FROM $table ";
      
        return self::query($sql)->execute()->last();
    }

    public function insert($data)
    {
        $table = $this->table;
       
        $field = [];
        $value = [];
        foreach ($data as $key => $val)
        {
            $field[] = "`$key`";

            if (is_numeric($val)) $value[] = $val;
            else if ($val[0] == ':') $value[] = $val;
            else $value[] = "'$val'";
           
        }
            
        
        $field = implode(',', $field);
        $value = implode(',', $value);

        $this->sql = "INSERT INTO `$table` ($field) VALUES ($value) ";

        self::query($this->sql);
        return self::$__instance;
    }

    public function update($data)
    {
        $table = $this->table;
        $set = [];
        foreach ($data as $key => $val)
        {
            if ($val[0] == ':') $val = $val;
            else $val = "'$val'";

            $set[] = "`$key` = $val";
        }
        $set = implode(',', $set);

        $this->sql = "UPDATE `$table` SET $set ";
        self::query($this->sql);
        
        return self::$__instance;
    }

    public function select($field = "*")
    {
        $table = $this->table;
        if (is_array($field))
        {
            $tmp = [];
            foreach ($field as $key => $val) 
            {
                $tmp[] = is_numeric($key) ? $val : "$key AS $val"; 
            }
            $field = implode(',', $tmp);
        }
           
        $this->sql = "SELECT $field FROM $table ";

        self::query($this->sql);
        
        return self::$__instance;
    }

    public function as($alias)
    {
        $this->table .= " AS $alias ";
    }

    public function moreQuery($sql)
    {
        $this->sql.= $sql;

        self::query($this->sql);
        
        return self::$__instance;
    }

    public function delete($table)
    {
        $table = $this->table;

        $this->sql = "DELETE FROM `$table` ";

        self::query($this->sql);
        
        return self::$__instance;
    }

    public function where($cause)
    {
        $this->cause = $cause;
        $this->sql.= "WHERE $cause ";
        self::query($this->sql);
        
        // dd ($this->sql);
        return self::$__instance;
    }

    public function and($cause)
    {
        if (empty($this->$cause)) 
            return $this->where($cause);

        $this->cause = $cause;
        $this->sql.= "AND $cause ";
        self::query($this->sql);
        
        return self::$__instance;
            
    }

    public function or($cause)
    {
        if (empty($this->$cause)) 
            return $this->where($cause);

        $this->cause = $cause;
        $this->sql.= "OR $cause ";
        self::query($this->sql);
        
        return self::$__instance;
    }

    public function order($field, $type = DB::ORDER_ASC)
    {
        $this->sql.= "ORDER BY $field $type ";
        
        self::query($this->sql);
        
        return self::$__instance;
    }

}
