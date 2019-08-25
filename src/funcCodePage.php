<?php
namespace x51\functions;

class funcCodePage {
	public $codepage='Windows-1251';
/*
СТРОКОВЫЕ ФУНКЦИИ
*/
	public static function translit($text) {
		if (!$text) {
			return false;
		}
		if (is_array($text)) {
			$arText=$text;
		} elseif (is_string($text)) {
			$arText=array($text);
		}
		$trans = array(
				"а" => "a",
				"б" => "b",
				"в" => "v",
				"г" => "g",
				"д" => "d",
				"е" => "e",
				"ё" => "e",
				"ж" => "zh",
				"з" => "z",
				"и" => "i",
				"й" => "y",
				"к" => "k",
				"л" => "l",
				"м" => "m",
				"н" => "n",
				"о" => "o",
				"п" => "p",
				"р" => "r",
				"с" => "s",
				"т" => "t",
				"у" => "u",
				"ф" => "f",
				"х" => "kh",
				"ц" => "ts",
				"ч" => "ch",
				"ш" => "sh",
				"щ" => "shch",
				"ы" => "y",
				"э" => "e",
				"ю" => "yu",
				"я" => "ya",
				"А" => "A",
				"Б" => "B",
				"В" => "V",
				"Г" => "G",
				"Д" => "D",
				"Е" => "E",
				"Ё" => "E",
				"Ж" => "Zh",
				"З" => "Z",
				"И" => "I",
				"Й" => "Y",
				"К" => "K",
				"Л" => "L",
				"М" => "M",
				"Н" => "N",
				"О" => "O",
				"П" => "P",
				"Р" => "R",
				"С" => "S",
				"Т" => "T",
				"У" => "U",
				"Ф" => "F",
				"Х" => "Kh",
				"Ц" => "Ts",
				"Ч" => "Ch",
				"Ш" => "Sh",
				"Щ" => "Shch",
				"Ы" => "Y",
				"Э" => "E",
				"Ю" => "Yu",
				"Я" => "Ya",
				"ь" => "",
				"Ь" => "",
				"ъ" => "",
				"Ъ" => ""
		);
		
		array_walk_recursive($arText, function (&$val, $key) use ($trans) {
			if (is_string($val)) {
				$enc = strtolower(mb_detect_encoding($val));
				//echo "$enc \n";
				if ($enc=='utf-8') {
					$val=strtr($val, $trans);
				} else {
					$val=mb_convert_encoding(strtr(mb_convert_encoding($val, 'utf-8', $enc), $trans), $enc, 'utf-8');
				}
			}
		});
		//print_r($arText);
		if (is_array($text)) {
			return $arText;
		} elseif (is_string($text)) {
			reset($arText);
			return current($arText);
		}
	} // function translit
	
	/** транслитирует строку в мнемокод - нижний регистр, без пробелов
	 * 
	 * @param string $str
	 * @param number $maxChar
	 * @return boolean|unknown|string|unknown
	 */
	public static function translitCode($str, $maxChar=100) {
		if (is_string($str)) {
			$res=str_replace(array('.', ',', ':', ';', '#', '$', '*', '?', '!', '%', '№', '~', '`', '"', '\'', '-', '+', "\r", "\n", "\t"), ' ', strtolower(substr($str, 0, $maxChar)));
			return static::translit(
				\x51\functions\funcString::multiReplace(array('  ', ' ', '_'), array(' ', '-', '-'), trim($res))
			);
		} else {
			return $str;
		}
	} // end translitCode
	
	public static function imUtf8ToWin($s){
		// перекодировка из utf8 в win
		if ($s==false) { return false; }
		static $table = array(
				"\xD0\x90"=>"\xC0","\xD0\x91"=>"\xC1","\xD0\x92"=>"\xC2","\xD0\x93"=>"\xC3","\xD0\x94"=>"\xC4",
				"\xD0\x95"=>"\xC5","\xD0\x81"=>"\xA8","\xD0\x96"=>"\xC6","\xD0\x97"=>"\xC7","\xD0\x98"=>"\xC8",
				"\xD0\x99"=>"\xC9","\xD0\x9A"=>"\xCA","\xD0\x9B"=>"\xCB","\xD0\x9C"=>"\xCC","\xD0\x9D"=>"\xCD",
				"\xD0\x9E"=>"\xCE","\xD0\x9F"=>"\xCF","\xD0\xA0"=>"\xD0","\xD0\xA1"=>"\xD1","\xD0\xA2"=>"\xD2",
				"\xD0\xA3"=>"\xD3","\xD0\xA4"=>"\xD4","\xD0\xA5"=>"\xD5","\xD0\xA6"=>"\xD6","\xD0\xA7"=>"\xD7",
				"\xD0\xA8"=>"\xD8","\xD0\xA9"=>"\xD9","\xD0\xAA"=>"\xDA","\xD0\xAB"=>"\xDB","\xD0\xAC"=>"\xDC",
				"\xD0\xAD"=>"\xDD","\xD0\xAE"=>"\xDE","\xD0\xAF"=>"\xDF","\xD0\x87"=>"\xAF","\xD0\x86"=>"\xB2",
				"\xD0\x84"=>"\xAA","\xD0\x8E"=>"\xA1","\xD0\xB0"=>"\xE0","\xD0\xB1"=>"\xE1","\xD0\xB2"=>"\xE2",
				"\xD0\xB3"=>"\xE3","\xD0\xB4"=>"\xE4","\xD0\xB5"=>"\xE5","\xD1\x91"=>"\xB8","\xD0\xB6"=>"\xE6",
				"\xD0\xB7"=>"\xE7","\xD0\xB8"=>"\xE8","\xD0\xB9"=>"\xE9","\xD0\xBA"=>"\xEA","\xD0\xBB"=>"\xEB",
				"\xD0\xBC"=>"\xEC","\xD0\xBD"=>"\xED","\xD0\xBE"=>"\xEE","\xD0\xBF"=>"\xEF","\xD1\x80"=>"\xF0",
				"\xD1\x81"=>"\xF1","\xD1\x82"=>"\xF2","\xD1\x83"=>"\xF3","\xD1\x84"=>"\xF4","\xD1\x85"=>"\xF5",
				"\xD1\x86"=>"\xF6","\xD1\x87"=>"\xF7","\xD1\x88"=>"\xF8","\xD1\x89"=>"\xF9","\xD1\x8A"=>"\xFA",
				"\xD1\x8B"=>"\xFB","\xD1\x8C"=>"\xFC","\xD1\x8D"=>"\xFD","\xD1\x8E"=>"\xFE","\xD1\x8F"=>"\xFF",
				"\xD1\x96"=>"\xB3","\xD1\x97"=>"\xBF","\xD1\x94"=>"\xBA","\xD1\x9E"=>"\xA2"
		);
	
		if (is_array($s)) {
			if (is_array($s) && $s) {
				array_walk_recursive($s, function (&$val, $key) use ($table) {
					if (is_string($val)) $val=strtr($val, $table);
				});
			}
			return $s;
		} else {
			return strtr($s, $table);
		}
	} // imUtf8ToWin
	
	public static function winToUtf8($s){
		// перекодировка из win в utf-8
		if ($s==false) { return false; }
		static $table = array(
				"\xC0"=>"\xD0\x90","\xC1"=>"\xD0\x91","\xC2"=>"\xD0\x92","\xC3"=>"\xD0\x93","\xC4"=>"\xD0\x94",
				"\xC5"=>"\xD0\x95","\xA8"=>"\xD0\x81","\xC6"=>"\xD0\x96","\xC7"=>"\xD0\x97","\xC8"=>"\xD0\x98",
				"\xC9"=>"\xD0\x99","\xCA"=>"\xD0\x9A","\xCB"=>"\xD0\x9B","\xCC"=>"\xD0\x9C","\xCD"=>"\xD0\x9D",
				"\xCE"=>"\xD0\x9E","\xCF"=>"\xD0\x9F","\xD0"=>"\xD0\xA0","\xD1"=>"\xD0\xA1","\xD2"=>"\xD0\xA2",
				"\xD3"=>"\xD0\xA3","\xD4"=>"\xD0\xA4","\xD5"=>"\xD0\xA5","\xD6"=>"\xD0\xA6","\xD7"=>"\xD0\xA7",
				"\xD8"=>"\xD0\xA8","\xD9"=>"\xD0\xA9","\xDA"=>"\xD0\xAA","\xDB"=>"\xD0\xAB","\xDC"=>"\xD0\xAC",
				"\xDD"=>"\xD0\xAD","\xDE"=>"\xD0\xAE","\xDF"=>"\xD0\xAF","\xAF"=>"\xD0\x87","\xB2"=>"\xD0\x86",
				"\xAA"=>"\xD0\x84","\xA1"=>"\xD0\x8E","\xE0"=>"\xD0\xB0","\xE1"=>"\xD0\xB1","\xE2"=>"\xD0\xB2",
				"\xE3"=>"\xD0\xB3","\xE4"=>"\xD0\xB4","\xE5"=>"\xD0\xB5","\xB8"=>"\xD1\x91","\xE6"=>"\xD0\xB6",
				"\xE7"=>"\xD0\xB7","\xE8"=>"\xD0\xB8","\xE9"=>"\xD0\xB9","\xEA"=>"\xD0\xBA","\xEB"=>"\xD0\xBB",
				"\xEC"=>"\xD0\xBC","\xED"=>"\xD0\xBD","\xEE"=>"\xD0\xBE","\xEF"=>"\xD0\xBF","\xF0"=>"\xD1\x80",
				"\xF1"=>"\xD1\x81","\xF2"=>"\xD1\x82","\xF3"=>"\xD1\x83","\xF4"=>"\xD1\x84","\xF5"=>"\xD1\x85",
				"\xF6"=>"\xD1\x86","\xF7"=>"\xD1\x87","\xF8"=>"\xD1\x88","\xF9"=>"\xD1\x89","\xFA"=>"\xD1\x8A",
				"\xFB"=>"\xD1\x8B","\xFC"=>"\xD1\x8C","\xFD"=>"\xD1\x8D","\xFE"=>"\xD1\x8E","\xFF"=>"\xD1\x8F",
				"\xB3"=>"\xD1\x96","\xBF"=>"\xD1\x97","\xBA"=>"\xD1\x94","\xA2"=>"\xD1\x9E"
		);
		if (is_array($s)) {
			if (is_array($s) && $s) {
				array_walk_recursive($s, function (&$val, $key) use ($table) {
					if (is_string($val)) {
						$val=strtr($val, $table);
					}
				});
			}
			return $s;
		} else {
			return strtr($s, $table);
		}
	} // end function winToUtf8
} // class funcCodePage