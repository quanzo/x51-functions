<?php


// ###
namespace x51\functions;

// /###

/* определенные форматы хранения информации в файле */

class funcFileFormats
{
	
	public static function explodeFormat1($text)
	/* разбивает текст в спец формате
	[section]
	текст
	[section2]
	текст 2
	*/
	{
		// разбиваем текст на строки
		$lines=explode("\n", $text);
		$result=array();
		$section_name='';
		$section_counter=0;
	
		foreach ($lines as $key => $val)
		{
			$val=str_replace("\r",'',$val);
			// определяем наличие секции [
			$line1=trim($val);
			if (substr($val, 0, 1)=='[' && substr($val, strlen($line1)-1, 1)==']')
			{
				// секция найдена
				$section_name=substr($val, 1, strlen($line1)-2);
				$section_counter++;
			}
			else
			{
				if ($section_name!='')
				{
					if (isset($result[$section_name]) && $result[$section_name]!='') $result[$section_name].="\r\n";
					$result[$section_name].=$val;
				}
			}
		}
		if ($section_counter>0)
		{
			unset($lines);
			return $result;
		} else return false;
	} // end function
	
	public static function implodeFormat1($arr)
	// из массива формирует текст для записи в файл
	{
		// ключи - это секции. значения массива - содержимое секции
		$text='';
		if (is_array($arr))
		{
			$text='';
			foreach ($arr as $section => $val)
			{
				$text.='['.$section."]\r\n";
				if (is_object($val) || is_array($val))
				{
					$text.=serialize($val)."\r\n";
				}
				else
				{
					$text.=$val."\r\n";
				}
			}
		}
		else
		{
			if (is_object($arr))
			{
				$text=serialize($arr);
			}
			else $text=$arr;
		}
		return $text;
	} // end function 
	
	

} // end class


?>