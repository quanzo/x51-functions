<?php


// ###
namespace x51\functions;

// /###

/* функции шифрования/дешифрования

public static function encode_type1($String, $Password)
public static function decode_type1($String, $Password)


*/

class funcCrypt
{
	public static function encode_type1($String, $Password)
	{
	    //Author: Vladimir Kim (www.vkim.ru) 2010
	    //Free for use
	
	    if (!$Password) die ("Не задан пароль шифрования");
	
	    $Salt='BGuxLWQtKweKEMV4';
	    $String = substr(pack("H*",sha1($String)),0,1).$String;
	    $StrLen = strlen($String);
	    $Seq = $Password;
	    $Gamma = '';
	    while (strlen($Gamma)< $StrLen)
	    {
	        $Seq = pack("H*",sha1($Seq.$Gamma.$Salt));
	        $Gamma.=substr($Seq,0,8);
	    }
	
	    return base64_encode($String^$Gamma);
	}
	
	public static function decode_type1($String, $Password)
	{
	    //Author: Vladimir Kim (www.vkim.ru) 2010
	    //Free for use
	
	    if (!$Password) die ("Не задан пароль для расшифровки");
		
	    $Salt='BGuxLWQtKweKEMV4';
	    $StrLen = strlen($String);
	    $Seq = $Password;
	    $Gamma = '';
	    while (strlen($Gamma)<$StrLen)
	    {
	        $Seq = pack("H*",sha1($Seq.$Gamma.$Salt));
	        $Gamma.=substr($Seq,0,8);
	    }
	
	    $String = base64_decode($String);
	    $String = $String^$Gamma;
	
	    $DecodedString = substr($String, 1);
	    $Error = ord(substr($String, 0, 1)
	             ^ substr(pack("H*",sha1($DecodedString)),0,1)); 
	
	    //проверяем
	    if ($Error) return false;
	    else return $DecodedString;
	}
} // end class
?>