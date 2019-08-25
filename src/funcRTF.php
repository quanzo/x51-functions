<?php


// ###
namespace x51\functions;

// /###




class funcRTF
{

	// переводит руские символы для записи в RTF
	public static function Rus2Rtf($str)
	{
		$rus_chars='абвгдежзийклмнопрстуфхцчшщъыьэюяАБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯЁё';
		$result='';
		$strsize=strlen($str);
		for ($i=0; $i<$strsize; $i++)
		{
			$char1=substr($str,$i,1);
			if (strpos($rus_chars,$char1)!==false)
			{
				$result.="\'".dechex(ord($char1));
			}
			else
			{
				$result.=$char1;
			}
		}
		return $result;
	} // end function convertRus2Rtf
	
	// переводит руские символы для записи в RTF
	public static function convertRus2Rtf_all($data)
	{
		if (is_array($data))
		{
			$result=array();
			// переведем индексы и значения
			foreach ($data as $key => $val)
			{
				$new_key=self::Rus2Rtf($key);
				$new_val=self::Rus2Rtf($val);
				$result[$new_key]=$new_val;
			}
			return $result;
		}
		else
		{
			return self::Rus2Rtf($str);
		}
	}
	
	public static function outRTF($rtf_body, $filename='out.rtf')
	{
		header( 'Content-Type: application/msword' );
		header( 'Content-Disposition: inline, filename='.$filename);
		echo $rtf_body;
	}

} // end class
?>