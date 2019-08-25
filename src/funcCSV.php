<?php


// ###
namespace x51\functions;

// /###

/*
public static function str_putcsv($array, $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ������ ������ � ���� ������ csv
public static function str_getcsv($string, $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ��������� ������ csv � ������
public static function fgetcsv($strFileName, $firstLineColName=false, $indexName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $escape='\\') // ��������� csv ���� � ���� �������.
public static function fputcsv($strFileName, $arData, $arColName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $terminator="\r\n") // ��������� csv ���� �� �������
public static function outBrowserCSV($csv_body, $filename='out.csv', $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ������ csv ����� � �������

*/

class funcCSV
{
	public static function str_putcsv($array, $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ������ ������ � ���� ������ csv
/*
	$arOrderFields ���������� ������� ������������ �����. 
*/
	{ 
		# First convert associative array to numeric indexed array 
		foreach ($array as $key => $value) $workArray[] = $value; 


		$returnString = '';				# Initialize return string 
		$arraySize = count($workArray);	# Get size of array 
		
		for ($i=0; $i<$arraySize; $i++) { 
			# Nested array, process nest item 
			if (is_array($workArray[$i])) { 
				$returnString .= str_putcsv($workArray[$i], $delimiter, $enclosure, $terminator); 
			} else {
				switch (gettype($workArray[$i])) { 
					# Manually set some strings 
					case "NULL":	$_spFormat = ''; break; 
					case "boolean":  $_spFormat = ($workArray[$i] == true) ? 'true': 'false'; break; 
					# Make sure sprintf has a good datatype to work with 
					case "integer":  $_spFormat = '%d'; break; 
					case "double":   $_spFormat = '%0.2f'; break; 
					case "string":   {
						$workArray[$i]=str_replace($enclosure, $enclosure.$enclosure, $workArray[$i]);
						$_spFormat = '%s';
						break; 
					}
					# Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested 
					case "object": 
					case "resource": 
					default:		$_spFormat = ''; break; 
				}
				
				$returnString .= sprintf('%2$s'.$_spFormat.'%2$s', $workArray[$i], $enclosure); 
				$returnString .= ($i < ($arraySize-1)) ? $delimiter : $terminator; 
			}
		} 
		# Done the workload, return the output information 
		return $returnString;
	} // end str_putcsv
	
	public static function str_getcsv($string, $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ��������� ������ csv � ������
	{
		return str_getcsv($string, $delimiter, $enclosure, $terminator);
	} // end str_getcsv
	
	public static function hgetcsv($handle, $firstLineColName=false, $indexName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $escape='\\') // ��������� csv ���� � ���� �������.
	/* 
	$firstLineColName [false] - true/false - ��������� �� ��, ��� ������ ������ csv-������� �������� �������� ��������
	$indexName [false] - false ��� ��� ������� - ��������� �� ��� �������, ������� ����� ������� �� ������ ����� � �������������� �������. ���� false �� ������ ����� ����� ������ �� 0
	$begin [false] - false ��� ����� - � ����� ������ csv �������� ������������ �������
	$count [false] - false ��� ����� - ���-�� ����� �� csv, ������� ����� �������� � �������������� ������
	$delimiter [';'] - ����������� ������� � csv
	$enclosure = '"'
	$escape='\\'
	*/
	{
		//$handle = fopen($strFileName, 'r');
		$res=new \stdClass();
		$res->key=array();
		$res->data=array();
		
		$ifFirstLine=true;
		$recordIndex=0;
		while (($data = fgetcsv($handle, 0, $delimiter, $enclosure, $escape) ) !== false ) {
			if ($ifFirstLine && $firstLineColName) {
				$res->key=$data;
			} else {
				if (($begin!==false && $count>0 && sizeof($res->data)<$count && $recordIndex>=$begin) || ($begin===false || $count==false)) {
					if ($firstLineColName) {
						$dSize=sizeof($data);
						for ($i=0; $i<$dSize; $i++)
							if (isset($res->key[$i])) {
								$data[$res->key[$i]]=$data[$i];
								unset($data[$i]);
							}
					}
				
					if ($indexName!==false && isset($data[$indexName])) { $newIndex=$data[$indexName]; }
						else $newIndex=sizeof($res->data);
					$res->data[$newIndex]=$data;
				}
				if ($begin!=false && $count>0 && sizeof($res->data)>=$count) break;
				$recordIndex++;
			}
			$ifFirstLine=false;
		} // end while
		//fclose($handle);
		return $res;
	} // end hgetcsv
	
	public static function fgetcsv($strFileName, $firstLineColName=false, $indexName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $escape='\\') // ��������� csv ���� � ���� �������.
	{
		$handle = fopen($strFileName, 'r');
		$res=static::hgetcsv($handle, $firstLineColName, $indexName, $begin, $count, $delimiter, $enclosure, $escape);
		fclose($handle);
		return $res->data;
	} // end fgetcsv
	
	public static function hputcsv($handle, $arData, $arColName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $terminator="\r\n") // ��������� csv ���� �� �������
	{
		if ($handle!=false)
		{
			if ($arColName!=false && is_array($arColName))
			{
				$strLine=static::str_putcsv($arColName, $delimiter, $enclosure, $terminator);
				fwrite($handle, $strLine);
			}
			$counter=0;
			foreach($arData as $mFields)
			{
				if (is_array($mFields)) { $strLine=static::str_putcsv($mFields, $delimiter, $enclosure, $terminator); }
					else $strLine=static::str_putcsv(array($mFields), $delimiter, $enclosure, $terminator);
				fwrite($handle, $strLine);
				$counter++;
			} // end foreach
			return $counter;
		}
		return false;
	} // hputcsv
	
	public static function fputcsv($strFileName, $arData, $arColName=false, $begin=false, $count=false, $delimiter = ';', $enclosure = '"', $terminator="\r\n") // ��������� csv ���� �� �������
	{
		if ($strFileName!=false)
		{
			$handle = fopen($strFileName, 'w');
			$res=hputcsv($handle, $arData, $arColName, $begin, $count, $delimiter, $enclosure, $terminator); 
			fclose($handle);
			return $res;
		} else return false;
	} // fputcsv
	
	public static function outBrowserCSV($csv_body, $filename='out.csv', $delimiter = ';', $enclosure = '"', $terminator = "\r\n") // ������ csv ����� � �������
	/* � �������� $csv_body ����� ���������� ��������� ������ */
	{
		//������� �������� ���������, ��� ����� ������������ ���� 'csv'
		header("Content-type: csv/plain");
		//������� �������� ���������, ��� ����� ���������� ����
		header('Content-Disposition: attachment; filename='.$filename);
		if (is_array($csv_body))
		{
			foreach ($csv_body as $val)
			{
				echo static::str_putcsv($val, $delimiter, $enclosure, $terminator);
			}
		}
		else echo $csv_body;
	} // end outCSV



/*public static function csv_tableExport($connection, $table_name, $colname=false, $sql_param='')
{
	$out='';
	$ssql='SELECT * FROM '.$table_name;
	if ($sql_param!='')
	{ $ssql.=$sql_param; }
	$res_q=mysql_query($ssql,$connection);
	$res_count=mysql_num_rows($res_q);
	if ($res_count>0)
	{
		$col_count=mysql_num_fields($res_q);
		if ($colname)
		{
			$col_count=mysql_num_fields($res_q);
			for ($i=0; $i<$col_count; $i++)
			{
				$out2=self::parseCSVfield(mysql_field_name($res_q,$i));
				if ($i!=($col_count-1)) { $out2.=';';}
				$out.=$out2;
			}
			$out.="\r\n"; // ��������� ������
		}
		for ($i2=0; $i2<$res_count; $i2++)
		{
			$one_line=mysql_fetch_row($res_q);
			$out.=self::parseCSVrow($one_line);
		}
	}
	return $out; 
}
*/



} // end class

?>