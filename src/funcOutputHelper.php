<?php
/* Ќабор функций-помошников дл€ вывода html

public function htmlSelectOption($arData, $currentValue=false, $fieldname=false, $def='unknown') // выдает массив как элементы <select>
public static function htmlArray($table) // формирует хтмл на основании массива. массивы могут быть вложенными
public static function txtArray($table) // формирует текст на основании массива
public static function formatTextHTML($text) // функци€ предварительной обработки текста html с применением команд в самом тексте

*/

namespace x51\functions;

class funcOutputHelper {
	
	public function htmlSelectOption($arData, $currentValue=false, $fieldname=false, $def='unknown') // выдает массив как элементы <select>
	/*
		$arData - массив,
		$currentValue=false - значение которое €вл€етс€ текущим в списке и выдел€етс€,
		$fieldname=false - если примен€етс€ таблица, то этот параметр содержит им€ колонки, в которой содержитс€ значение,
		$def='unknown' - если значение не найдено, то будет выведено это значение
	*/
	{
		$res='';
		foreach ($arData as $key => $arItem)
		{
			$val=$arItem;
			if (is_array($arItem))
			{
				if ($fieldname && isset($arItem[$fieldname])) { $val=$arItem[$fieldname]; }
					else $val=$def;
			} else $val=$arItem;
			
			$res.='<option value="'.$key.'"';
			if ($currentValue!==false && $key==$currentValue) $res.=' selected="selected"';
			$res.='>'.$val.'</option>';
		} // end foreach
		return $res;
	}
	
	public static function htmlArray($table) // формирует хтмл на основании массива. массивы могут быть вложенными
	/* одна колонка - ключ массива, втора€ - значение пол€
	*/
	{
		$out='';
		foreach ($table as $name => $value)
		{
			$out.='<tr><td>'.$name.'</td><td>';
			if (is_array($value)) { $out.=self::order_html($value); }
			else { $out.=$value; }
			$out.='</td></tr>'."\r\n";
		}
		if ($out!='') { return '<table border="1">'.$out.'</table>'."\r\n"; }
	} // end func

	public static function txtArray($table) // формирует текст на основании массива
	{
		$out='';
		foreach ($table as $name => $value)
		{
			$out.='['.strtoupper($name)."]\r\n\r\n";
			if (is_array($value)) { $out.=print_r($value,true); }
				else { $out.=$value; }
			$out.="\r\n\r\n\r\n";
		}
		return $out;
	} // end func
	
	public static function formatTextHTML($text) // функци€ предварительной обработки текста html с применением комманд в самом тексте
	/* все комментарии html <!-- --> удал€ютс€ 
	в комментарии можно задавать комманды @param[]
		@param[delete_emptystring] - удаление пустых строк
		@param[one_line] - весь текст в одну строку. все \r\n будут заменены на пробел
		@param[delete_tab] - удалить все табул€ции
		@param[delete_doublespace] - удалить двойные пробелы
		@param[tab2space] - преобразовать табул€цию в пробел
		
	*/
	{
		if ($text==false) return $text;
		$comments=funcString::lmv_substr_part_all($text, '<!--', '-->');
		if ($comments!=false)
		{
			$arToReplace=funcArray::selectColumn($comments, 'body2', array('saveValue'=>false, 'saveIndex'=>false));
			$arCommentBody=funcArray::selectColumn($comments, 'body', array('saveValue'=>false, 'saveIndex'=>false));
			//funcDebug::debugPrint('<h1>Test comment</h1>',$arCommentBody);
			
			$text=str_replace($arToReplace, '', $text); // убираем из текста все комментарии
			unset($comments, $arToReplace);
			
			$arCommands=array();
			foreach ($arCommentBody as $comment)
			{
				$commands=funcString::lmv_substr_part_all($comment, '@param[', ']');
				if ($commands!=false)
				{ // найдены комманды
					$com=funcArray::selectColumn($commands, 'body', array('saveValue'=>false, 'saveIndex'=>false));
					foreach ($com as $str)
						if (isset(static::$arModifyCommand[$str]))
						{
							
							foreach (static::$arModifyCommand[$str][0] as $key) $arCommands[0][]=$key;
							foreach (static::$arModifyCommand[$str][1] as $key) $arCommands[1][]=$key;
						}
				}
			} // end foreach
			if ($arCommands!=false)
			{
				// преобразуем текст
				return str_replace($arCommands[0], $arCommands[1], $text);
			}
		}
		return $text;
	} // formatTextHTML
	
	public static function cssBorderRadius($border_radius)
	{
		return '-webkit-border-radius: '.$border_radius.';
-moz-border-radius: '.$border_radius.';
border-radius: '.$border_radius.';';
	} // end cssBorderRadius
	
	public static function cssBoxShadow($box_shadow)
	{
		return '-webkit-box-shadow: '.$box_shadow.';
-moz-box-shadow: '.$box_shadow.';
box-shadow: '.$box_shadow.';';
	} // end cssBorderRadius

} // end class