<?php


// ###
namespace x51\functions;
// /###

// **********************************************************************************
// ПОИСК
// подготавливает строку для поиска (обрезка у слов окончания для более полного поиска)
/*
public static function 
public static function searchFulltextTable($tablename, $strSearch, $indexName=false) // поиск в таблице БД по fulltext ключу
function prepare_str2search(&$str)// подготавливает строку для поиска (обрезка у слов окончания для более полного поиска)
function prepare_word2search(&$word, &$cut_count) // подготовка слова для поиска (обрезка окончания)
function search_str2preg($search_str, $first_strogo=false, $end_strogo=false) // формирует строку (шаблон) для использования в регулярных выражениях для определения соответствия
function rsearch($connector, $tablename, $search_str, $search_fieldnames, $return_fieldname) // поиск в таблице БД
*/
class funcSearch
{
	public static function convertLikeToRegex($strLike) // конвертирует поисковую строку из LIKE SQL в регулярное выражение PREG
	{
		$com=str_replace(array('%'), array('\%'), preg_quote($strLike));
		$r=array(
			'\%'=>'(.*?)',
			'\?'=>'(.{1})',
			'\\\\(.*?)'=>'%',
			'\\(.{1})'=>'?'
		);
		$com2=str_ireplace(array_keys($r) , array_values($r), $com);
		return "/^".$com2."$/s";
	} // end convertLikeToRegex
// ---------------------------	
	public static function searchFulltextTable($tablename, $strSearch, $indexName=false) // поиск в таблице БД по fulltext ключу
	{
		$sql='SHOW INDEX FROM '.$tablename.' WHERE Index_type = "FULLTEXT"';
		if ($indexName!=false) $sql.=' AND key_name="'.$indexName.'"';
		funcDebug::debugPrint($sql);
		$arFTIndex=getProviderDB()->querySelect($sql, array('multi'=>true, 'assoc'=>false)); // MATCH (title,body) AGAINST ('MySQL')
		funcDebug::debugPrint($arFTIndex);
		if ($arFTIndex!=false)
		{
			foreach ($arFTIndex as &$arVal) $arVal=array_change_key_case($arVal, CASE_LOWER);
			$arIndexs=funcArray::groupResult($arFTIndex, 'key_name');
			funcDebug::debugPrint($arIndexs);
			$arOneIndex=array_shift($arIndexs);
			funcDebug::debugPrint($arOneIndex);
			// поля идекса
			$strIndexFields=funcSQL::queryParamAdaptation(funcArray::selectColumn($arOneIndex, 'column_name', array('saveIndex'=>false, 'saveValue'=>false)));
			
			$strFS=' MATCH('.$strIndexFields.') AGAINST("'.funcSQL::escapeString($strSearch).'") ';
			
			$sql='SELECT *, '.$strFS.' as searchIndexValue  FROM '.$tablename.' WHERE '.$strFS;
			funcDebug::debugPrint($sql);
			return getProviderDB()->querySelect($sql, array('multi'=>true, 'assoc'=>false));
			
			
		} else return false;
	} // end searchFulltextTable
	
	
	public static function prepare_str2search(&$str)
	{
		$words=explode(' ',$str);
		$words_count=sizeof($words);
		$zam_count=0;
		for ($i=0; $i<$words_count; $i++)
		{
			$c_cut=0;
			self::prepare_word2search($words[$i],$c_cut);
			if ($c_cut)
			{
				$zam_count++;
			}
		}
		if ($zam_count>0)
		{
			// переформируем поисковую строку
			$str=implode(' ',$words);
		}
	}
	// подготовка слова для поиска (обрезка окончания)
	public static function prepare_word2search(&$word, &$cut_count)
	{
	/*
		при поиске по слову - отсекаем окончание
		если длинна слова
		<4 не трогаем
		>3 но меньше 7 - 1 букву в конце
		>7 - 2 буквы в конце
	*/
		$lss=strlen($word);
		if ($lss>4 && $lss<7)
		{
			$cut_count=1;
		}
		else
		{
			if ($lss>6)
			{
				$cut_count=2;
			}
			else
			{
				$cut_count=0;
			}
		}
				
		if ($cut_count>0)
		{
			$word=substr($word, 0,$lss-$cut_count);
		}
	}
	
	public static function search_str2preg($search_str, $first_strogo=false, $end_strogo=false) // формирует строку (шаблон) для использования в регулярных выражениях для определения соответствия
	{
		$es=explode(' ', trim($search_str));
		$es_count=sizeof($es);
		for ($i=0; $i<$es_count; $i++)
		{
			$first_b=substr($es[$i],0,1);
			// первая буква слова должна быть и большой и маленькой
			$es[$i]=substr_replace($es[$i],'('.mb_strtolower($first_b).'|'.mb_strtoupper($first_b).')',0,1);
			if ($es[$i]{0}!='*' && !$first_strogo)
			{
				$es[$i]='*'.$es[$i];
			}
			
			if ($es[$i]{strlen($es[$i])-1}!='*' && !$end_strogo)
			{
				$es[$i].='*';
			}
		}
		
		$search_preg='/';
		if ($first_strogo)
		{
			$search_preg.='\s+'; 
		}
		$search_preg.=str_replace(array('*', '?', "\\", ' '),array('[а-яА-Яa-zA-Z]*','[а-яА-Яa-zA-Z]',"\\\\", '\s*'),implode(' ',$es));	
		$search_preg.='/i';
		//echo "PREG=$search_preg<br>";
		return $search_preg;
	}
	
	public static function rsearch($connector, $tablename, $search_str, $search_fieldnames, $return_fieldname)
	{
		/*
		connector   соединение с БД
		tablename   имя таблицы в которой ищем
		search_str  строка поиска
		search_fieldnames     в каких полях таблицы искать   массив имен
		
		*/
		
		// предварительная подготовка строки
		$str2search=trim($search_str);
		$es=explode(' ', $str2search);
		$es_count=sizeof($es);
		for ($i=0; $i<$es_count; $i++)
		{
			if ($es[$i]{0}!='*')
			{
				$es[$i]='*'.$es[$i];
			}
			if ($es[$i]{strlen($es[$i])-1}!='*')
			{
				$es[$i].='*';
			}
		}
		//print_r($es);
		$str2search=implode(' ',$es);
		
	
		$toSQL1=array('*','?');
		$toSQL2=array('%','_');
		$toPreg1=array('\\', ' ', '*', '?');
		//$toPreg2=array("[а-яА-Яa-zA-Z]*","[а-яА-Яa-zA-Z]","\\\\", "\s*");
		$toPreg2=array("\\\\", '\s*', '\w*','\w');
		$query='SELECT DISTINCT '.$return_fieldname.', ';
		$sfn_count=sizeof($search_fieldnames);
		for($i=0; $i<$sfn_count; $i++)
		{
			$query.=$search_fieldnames[$i];
			if ($i!=$sfn_count-1)
			{
				$query.=',';
			}
		}
		$query.=' FROM '.$tablename.' WHERE ';
		for($i=0; $i<$sfn_count; $i++)
		{
			$query.='LOWER('.$search_fieldnames[$i].') LIKE LOWER("'.str_replace($toSQL1,$toSQL2,$str2search).'")';
			if ($i!=$sfn_count-1)
			{
				$query.=' OR ';
			}
		}
		//echo "<br>QUERY=$query<br>";
		$res_q=mysql_query($query,$connector);
		$res_count=@mysql_num_rows($res_q);
		//echo "Найдено по селекту: $res_count<br>";
		$ress=array();
		if ($res_count>0)
		{
			// ====
			$str2search2='';
			$str2search_len=strlen($str2search);
			for ($cb=0; $cb<$str2search_len; $cb++)
			{
				$one_sym=$str2search{$cb};
				$one_sym2=mb_strtolower($one_sym);
				$one_sym3=mb_strtoupper($one_sym);
				if ($one_sym2!=$one_sym3)
				{
					$str2search2.='['.$one_sym2.$one_sym3.']';
				}
				else
				{
					$str2search2.=$one_sym;
				}
			}
			
			// ====
			$search_preg='/'.str_replace($toPreg1,$toPreg2,$str2search2).'/i';
			
			
			//echo "SEARCH PREG2=$search_preg<br>$str2search2<br>";
			// записи найдены проводим дополнительный поиск
			$ok_count=0;
			// обрабатываем результаты и заносим в массив
			for ($i=0; $i<$res_count; $i++)
			{
				$ok_count=0;
				$itfind=array();
				$r=mysql_fetch_assoc($res_q);
				foreach($r as $key => $val)
				{
					if ($key!=$return_fieldname) // если поле не для возврата
					{
						preg_match_all($search_preg,$val,$subbpat, PREG_PATTERN_ORDER);
						$subbpat0_count=sizeof($subbpat[0]);
						$ok_count+=$subbpat0_count;
						for ($i2=0; $i2<$subbpat0_count; $i2++)
						{
							$itfind[]=strtolower($subbpat[0][$i2]);
						}
					}
				}
				if ($ok_count>0)
				{
					$ress[$r[$return_fieldname]]=array($ok_count, array_unique($itfind));
				}
			}
		}
		return $ress;
		/*
		$ress[значение ключевого поля - $return_fieldname] -> [1] кол-во совпадений, [2] - список слов массив
		*/
	}
	
	} // end class funcSearch
	?>