<?php
namespace MrMe\Web;

class Validate
{
	public static function isEmpty($obj, $out)
	{
		if (empty($obj))
		{ 
			http_response_code(400);
			$json = array("status" => false, 
						  "message" => $out);
			echo json_encode($json);
			exit;
		}     
		
		return $obj;
	}
	
	public static function isDate($obj, $out)
	{
		if ($tm = strtotime($obj) === false) 
		{
			http_response_code(400);
			$json = array("status" => false, 
						  "message" => $out);
			echo json_encode($json);
			exit;
		}
		
		return $obj;
	}
	
	public static function isEmail($obj, $out)
	{
		if (!filter_var($obj, FILTER_VALIDATE_EMAIL))
		{ 
			http_response_code(400);
			$json = array("status" => false, 
						  "message" => $out);
			echo json_encode($json);
			exit;
		}     
		
		return $obj;
	}
	
	public static function isNumber(&$obj, $out)
	{
		if (!is_numeric ($obj))
		{ 
			http_response_code(400);
			$json = array("status" => false, 
						  "message" => $out);
			echo json_encode($json);
			exit;
		} 
		$obj = (double)$obj;
		return $obj;
	}
}
?>