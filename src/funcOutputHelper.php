<?php
/* ����� �������-���������� ��� ������ html

public function htmlSelectOption($arData, $currentValue=false, $fieldname=false, $def='unknown') // ������ ������ ��� �������� <select>
public static function htmlArray($table) // ��������� ���� �� ��������� �������. ������� ����� ���� ����������
public static function txtArray($table) // ��������� ����� �� ��������� �������
public static function formatTextHTML($text) // ������� ��������������� ��������� ������ html � ����������� ������ � ����� ������

*/

namespace x51\functions;

class funcOutputHelper {
	
	public function htmlSelectOption($arData, $currentValue=false, $fieldname=false, $def='unknown') // ������ ������ ��� �������� <select>
	/*
		$arData - ������,
		$currentValue=false - �������� ������� �������� ������� � ������ � ����������,
		$fieldname=false - ���� ����������� �������, �� ���� �������� �������� ��� �������, � ������� ���������� ��������,
		$def='unknown' - ���� �������� �� �������, �� ����� �������� ��� ��������
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
	
	public static function htmlArray($table) // ��������� ���� �� ��������� �������. ������� ����� ���� ����������
	/* ���� ������� - ���� �������, ������ - �������� ����
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

	public static function txtArray($table) // ��������� ����� �� ��������� �������
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
	
	public static function formatTextHTML($text) // ������� ��������������� ��������� ������ html � ����������� ������� � ����� ������
	/* ��� ����������� html <!-- --> ��������� 
	� ����������� ����� �������� �������� @param[]
		@param[delete_emptystring] - �������� ������ �����
		@param[one_line] - ���� ����� � ���� ������. ��� \r\n ����� �������� �� ������
		@param[delete_tab] - ������� ��� ���������
		@param[delete_doublespace] - ������� ������� �������
		@param[tab2space] - ������������� ��������� � ������
		
	*/
	{
		if ($text==false) return $text;
		$comments=funcString::lmv_substr_part_all($text, '<!--', '-->');
		if ($comments!=false)
		{
			$arToReplace=funcArray::selectColumn($comments, 'body2', array('saveValue'=>false, 'saveIndex'=>false));
			$arCommentBody=funcArray::selectColumn($comments, 'body', array('saveValue'=>false, 'saveIndex'=>false));
			//funcDebug::debugPrint('<h1>Test comment</h1>',$arCommentBody);
			
			$text=str_replace($arToReplace, '', $text); // ������� �� ������ ��� �����������
			unset($comments, $arToReplace);
			
			$arCommands=array();
			foreach ($arCommentBody as $comment)
			{
				$commands=funcString::lmv_substr_part_all($comment, '@param[', ']');
				if ($commands!=false)
				{ // ������� ��������
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
				// ����������� �����
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