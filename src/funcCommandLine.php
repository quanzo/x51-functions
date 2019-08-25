<?php


// ###
namespace x51\functions;

// /###


// функции обработки входных параметров
/*
public static function getNpost() // делает $_GET и $_POST одинаковыми по ключам
function protectGET($fieldnamearray) // функция для защиты от несуществующих параметров /продажные ссылки/

*/

class funcCommandLine
{
	
	/** приводит входные данные $_GET и $_POST в состояние при котором массивы становятся эквивалентны по ключам
	 * 
	 */
	public static function getNpost() {
		$not_post=array_diff_key($_GET, $_POST);
		$not_get=array_diff_key($_POST, $_GET);
		foreach ($not_post as $key => $val) $_POST[$key]=$val;
		foreach ($not_get as $key => $val) $_GET[$key]=$val;
	}
	
	/** функция для защиты от несуществующих параметров /продажные ссылки/
	 * 
	 * @param array $fieldnamearray - список разрешенных полей
	 */
	public static function protectGET(array $fieldnamearray) {
		// функция удаляет несуществующие параметры из массива $_GET
		$getfields=array_keys($_GET); // все параметры
		$lishnee=array_diff($getfields, $fieldnamearray); // возвращает лишние ключи
		foreach ($lishnee as $val) {
			unset($_GET[$val]);
			unset($_REQUEST[$val]);
		}
		$count_unset=sizeof($lishnee);
		unset($getfields);
		
		// сортируем параметры в $_GET по алфавиту - защита от перестановки параметров
			ksort($_GET);
			reset($_GET);
		// ---
		
		if ($count_unset>0) {
			// соберем заново $_SERVER['REQUEST_URI']
			// найдем начало ?
			$buff1=$_SERVER['REQUEST_URI'];
			$bg1=strpos($buff1,'?');
			if ($bg1!==false) {
				$res_name=substr($buff1,0,$bg1+1); // копируем вместе с вопросом
				$get_count=sizeof($_GET);
				$i=0;
				foreach ($_GET as $param_name => $param_value) {
					$i++;
					if (is_array($param_value)) {
						$s=sizeof($param_value);
						$c=0;
						foreach ($param_value as $i => $x) {
							if ($c>0) {
								$res_name.='&';
							}
							$res_name.=$param_name.'[]='.rawurlencode($x);
						}
					} else {
						$res_name.=$param_name.'='.rawurlencode($param_value);
					}
					if ($i<$get_count) {
						$res_name.='&';
					}
				}
			} else {
				// GET нет ничего не меняем
			}
		}
	} // end function protectGET

} // end class