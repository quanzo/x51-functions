<?php


// ###
namespace x51\functions;

// /###

/*function order_get() // функция для разборки данных формы. на выходе массив
public static function order_cleanArray($arr) // чистит массив формируемый order_get от пустых полей
function order_html($table) // функция для формирования html на основании cart_order
public static function order_txt($table) // формирует текст на основании массива
*/

class funcOrder
{

public static function order_get($param=array()) // функция для разборки данных формы. на выходе массив
	/* форма задается в виде html. имена всех элементов уникальны.
	к каждому полю для вывода данных можно прикрепить постфикс:
	_desc - содержит нормальное название входных данных
	_attr - дополнительные атрибуты
	все данные переданные формой через POST помещаются в массив. если задано desc - то оно евляется ключом массива, иначе имя заданное полю в форме
	на выходе получаем массив: ключи - названия полей, содержимое - значение полей
	
	Параметры:
	only_desc - выборка только полей имеющих поле_desc
	*/
	{
		if (!isset($param['only_desc'])) $param['only_desc']=false; // выборка только полей имеющих поле_desc
		
		$pk=array_keys($_POST);
		$ps=sizeof($pk);
		$ignore=array('_attr', '_desc');
		$out=array();
		for ($i=0; $i<$ps; $i++) // перебираем ключи
		{
			$is_ignore=false;
			for ($i2=0; $i2<sizeof($ignore); $i2++)
			{
				$f=strpos($pk[$i],$ignore[$i2]);
				if ($f!==false) // строка игнорирования найдена
				{
					// определим что стоит в конце
					if (($f+strlen($ignore[$i2]))==strlen($pk[$i])) { $is_ignore=true; }
				}
			}
			if (!$is_ignore)
			{
				// данные
				$desc_name=$pk[$i].'_desc';
				$name=$pk[$i];
				if (isset($_POST[$desc_name]))
				{
					$out[$_POST[$desc_name]]=$_POST[$pk[$i]];
				}
				else
				{
					if (!$param['only_desc'])
					{
						$out[$pk[$i]]=$_POST[$pk[$i]];
					}
				}
			}
		}
		return $out;
	} // end cart_order

public static function order_cleanArray($arr) // чистит массив формируемый order_get от пустых полей
{
	$key=array_keys($arr);
	$k_count=sizeof($key);
	for ($i=0; $i<$k_count; $i++) if ($arr[$key[$i]]=='') unset($arr[$key[$i]]);
	return $arr;
}
	

} // end class funcOrder