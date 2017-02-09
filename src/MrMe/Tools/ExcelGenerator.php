<?php
namespace MrMe\Tools;

class ExcelGenerator
{
	public static function export($file_name, $model)
	{
		//require_once("");
		require_once("./vendor/phpoffice/phpexcel/Classes/PHPExcel.php");
		$objPHPExcel = new \PHPExcel();

		// Set properties
		// $objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
		// $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

		// Add data
		$objPHPExcel->setActiveSheetIndex(0);
		$alphabet = 'A';
		$numeric  = 1; 

		foreach($model[0] as $key => $value) 
        {
			$cell = $alphabet.$numeric;
			$objPHPExcel->getActiveSheet()->SetCellValue($cell, $key);
			$alphabet++;
        }

		$alphabet = 'A';
		$numeric++;

		foreach ($model as $key => $value) 
		{
			foreach ($model[$key] as $key2 => $value2) 
			{
				$cell = $alphabet.$numeric;
				$objPHPExcel->getActiveSheet()->SetCellValue($cell, (string)$value2);
				$alphabet++;
			}
			$alphabet = 'A';
			$alphabet++;
		}

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Sheet1');

		// Save Excel 2007 file
		$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save($file_name .".xlsx");

	}
}
?>