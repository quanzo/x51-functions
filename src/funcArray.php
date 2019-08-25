<?php
// ###
namespace x51\functions;

// /###
/* simpleFilter - формат
значение фильтра - массив с условиями:
	для чисел:
	lo - нижняя граница числового значения
	hi - верхняя граница числового значения
		если число больше lo и меньше hi, условие выполняется
	eq-lo - больше или равно
	eq-hi - меньше или равно
	для всех:
		like - строка в формате LIKE SQL для определения строки
	Дополнительно:
		зарезервированные имена полей
		@include - дополнительные условия в формате simpleFilter для включения
		@exclude - дополнительные условия в формате simpleFilter для исключения строки из выборки 
Пример:
	array(
		'id'=>array(10, 15, 20), // фиксированный список значений
		'point'=>array(
			'like'=>'%лес%' // поле содержит слово лес
		),
		'size'=>array(
			'lo'=>10 // больше 10
		),
		'population'=>array(
			'hi'=> 1000 // меньше 1000
		),
		'@include'=>array(
			'size'=>1, // дополнительно включить в выборку
		),
		'@exclude'=>array(
			'population'=>700 // исключить из выборки
		),
	);

simpleFilterLine - формат
	не ассоциативный массив
	значения вида:
		<имя поля>_<значение> - id_100 или id_element_100 (id_element - имя поля)

*/
// функции для работы с массивами
/*
public static function callableArray($ar) на основании смешанных данных в $ar (часть элементов может быть функцией, котрая возвращает либо элемент либо массив) формирует результирующий массив
public static function eqTable(array $ar_1, array $ar_2, array $arIgnoredField = array(), $orderStrict=false) {
public static function eqRow(array $row_1, array $row_2, array $arIgnoredField = array()) {

public static function selectColumn($arr, $col_name, $param=array()) // из многомерного ассоциативного массива выбирается одна колонка и возвращается одномерный массив
public static function groupResult(array $arr, $col_name, array $param=array()) // переформирует результат - группирует по заданной колонке - рез. в видеarr[col_name][номер]=<строка данных> (парам non_uniqueness=false по умолч) или arr[col_name]=<строка данных> (парам non_uniqueness=true по умолч)

public static function simpleSortTable(&$arTable, $arSort) // сортирует таблицу

public static function simpleFilterOneRow($arRow, $arFilterValue)
public static function simpleFilterTable(&$table, $arFilterValue)
//public static function simpleFilterTableVariant(array &$arTable, array ...$arFilterValues) { закомментировано
public static function mergeSimpleFilter($arFilter1, $arFilter2) // объединяет несколько simpleFilter в один
public static function completeTable(array & $arTblPrim, $fnTblPrim, array & $arTblSec, $fnTblSec, $rewrite=false) // дополняет первую таблицу данными из второй
!!! public static function advancedFilterRow($arRow, $arFilterValue) // расширенный фильтр одной строки таблицы. формат
public static function table2Html(&$in_data, $param=array())// печатает таблицу
public static function check2DArray(&$table); // определяет двумерность массива по первому значению
public static function limitArrayKeys(&$table, $arKeyList) // ограничивает ключи массива только списком $arKeyList
public static function sumColumn(&$table, $col_name) // суммирует элементы с определенными именами
public static function fillColumn(array &$table, $col_name, $value) // заполнить колонку $col_name массива $table значением $value
public static function inTableCallable(array $table, $func) // функция ищет первый элемент (возвращает ключ), который соответствует условию, которое определяется $func
public static function inTable(array $table, $col_name, $value) // Ищет значение $value в колонке $col_name таблицы $table 

public static function sf_bitrix(array $arSF) // преобразование simpleFilter в формат фильтра битрикса

*/
class funcArray
{
	protected static $simpleFilterSpec=array(
		'lo'=>'',
		'hi'=>'',
		'eq-lo'=>'',
		'eq-hi'=>'',
		'like'=>'',
		'@include'=>'',
		'@exclude'=>'',
	);
	/** на основании смешанных данных в $ar (часть элементов может быть функцией, котрая возвращает либо элемент либо массив) формирует результирующий массив
	 * 
	 * @param array $ar
	 * @return array[]
	 */
	public static function callableArray($ar) {
		$res=array();
		array_walk($ar, function ($val) use (&$res) {
			if (is_callable($val)) {
				if ($d=$val()) {
					if (is_array($d)) { foreach ($d as $e) $res[]=$e; }
						else $res[]=$d;
				}
			} else {
				$res[]=$val;
			}
		});
		return $res;
	} // end callableArray
	
	//public static function eqTable(array $ar_1, array $ar_2, array $arIgnoredField = array(), bool $orderStrict=false): bool{
	/** Сравнивает две таблицы
	 * 
	 * @param array $ar_1
	 * @param array $ar_2
	 * @param array $arIgnoredField - список полей, которые будут игнорированы в операции сравнения
	 * @param string $orderStrict - при сравнении учитывать порядок строк таблицы
	 * @return boolean
	 */
	public static function eqTable(array $ar_1, array $ar_2, array $arIgnoredField = array(), $orderStrict=false) {
		if (sizeof($ar_1)!=sizeof($ar_2)) return false;
		if (!$ar_1 && !$ar_2) return true;
		reset($ar_2);
		foreach ($ar_1 as $value) {
			if (!$orderStrict) {
				$eq_res=false;
				foreach ($ar_2 as $value_2) {
					if (static::eqRow($value, $value_2, $arIgnoredField)) {
						$eq_res=true;
						break;
					}
				}
				if (!$eq_res) return false;
			} else {
				if (! static::eqRow($value, current($ar_2), $arIgnoredField)) {
					return false;
				}
				next($ar_2);
			}
		}
		return true;
	} // end eqTable
	
	/** Сравнение двух массивов (строк). На сравнение не влияет порядок элементов в массиве.
	 * 
	 * @param array $row_1
	 * @param array $row_2
	 * @param array $arIgnoredField - список полей, которые не будут учитываться при сравнении
	 * @return boolean
	 */
	public static function eqRow(array $row_1, array $row_2, array $arIgnoredField = array()) {
		if ($arIgnoredField) {
			$arIgnoredField=array_fill_keys($arIgnoredField, false);
		}
		if (sizeof($row_1)>sizeof($row_2)) {
			$r1=&$row_1;
			$r2=&$row_2;
		} else {
			$r1=&$row_2;
			$r2=&$row_1;
		}
		foreach ($r1 as $name => $value) {
			if (!isset($arIgnoredField[$name])) {
				if (!isset($r2[$name])) {
					return false;
				}
				if ($value!=$r2[$name]) {
					return false;
				}
			}
		}
		return true;
	} // end eqRow

	/** Выбрать колонку из таблицы.
	 * Функция формирует массив из одной колонки.
	 * входной массив - многомерный масив.
	 * индексы строк колонок могут быть сохранены ($param['saveIndex']=true) или массив может быть с числовыми номерами от 0 ($param['saveIndex']=false)
	 * если элемент с определенным индексом не будет строкой а будет значением то оно будет сохранено $param['saveValue']=true или не внесено в результат $param['saveIndex']=false
	 * 
	 * @param array $arr
	 * @param mixed $col_name
	 * @param array $param
	 * @return unknown[]|boolean
	 */
	public static function selectColumn(array $arr, $col_name, $param=array()) {
		if (!isset($param['saveIndex'])) {
			$param['saveIndex']=true;
		}
		if (!isset($param['saveValue'])) {
			$param['saveValue']=true;
		}
		
		$res=array();
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
				$ifsaved=false;
				if (is_array($val)) {
					if (isset($val[$col_name]))	{
						$savedValue=$val[$col_name];
						$ifsaved=true;
					}
				} else {
					if ($param['saveValue']) {
						$savedValue=&$val;
						$ifsaved=true;
					}
				}
				if ($ifsaved) {
					if ($param['saveIndex']) {
						$res[$key]=$savedValue;
					} else {
						$res[]=$savedValue;
					}
				}
			} // end foreach
		} else {
			if ($param['saveValue']) {
				return array($arr);
			} else {
				return false;
			}
		}
		return $res;
	} // end selectColumn
	
	
	/** функция формирует массив из нескольких колонок.
	 * входной массив - многомерный масив.
	 * индексы строк колонок могут быть сохранены ($param['saveIndex']=true) или массив может быть с числовыми номерами от 0 ($param['saveIndex']=false)
	 * 
	 * @param array $arr - входная таблица
	 * @param array $cols - список выбираемых колонок
	 * @param array $param
	 * @return array
	 */
	public static function selectColumns(array $arr, array $cols, array $param=array()) {
		if (!isset($param['saveIndex'])) {
			$param['saveIndex']=true;
		}
		$res=array();
		$resKey=0;
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
				if ($param['saveIndex']) {
					$resKey=$key;
				}
				if (is_array($val)) {
					foreach ($cols as $c_name) {
						if (isset($val[$c_name])) {
							$res[$resKey][$c_name]=$val[$c_name];
						}
					}
				}
				if (!$param['saveIndex']) {
					$resKey++;
				}
			} // end foreach
		}
		return $res;
	} // end function selectColumns
	
	/** Переформирует результат - группирует по заданной колонке - эта колонка становится индексом - результирующий массив становится вида arr[col_name][номер]=<строка данных> (см. парам non_uniqueness) - потому что col_name может быть не уникальным. входящий $arr должен быть двумерным массивом. если col_name нету в массиве то эта строка не попадает в результаты.
	 * 
	 * @param array $arr - группируемая таблица
	 * @param string $col_name - имя колонки по которое производится группировка 
	 * @param array $param - дополнительные параметры
	 * 	non_uniqueness - true/false(по умолч) - если true, то элементы не уникальны и в результате каждому значению в колонке col_name будет сопоставлено одно значение строки
	 *  если false (по умолчанию) то результирующий массив будет двумерным и иметь вид  arr[col_name]=array
	 * @return unknown[]|unknown
	 */
	public static function groupResult(array $arr, $col_name, array $param=array()) {
		$result=array();
		if (!isset($param['non_uniqueness'])) {
			$param['non_uniqueness']=false;
		}
		foreach ($arr as $val) {
			if (isset($val[$col_name])) {
				if ($param['non_uniqueness']) {
					$result[$val[$col_name]]=$val;
				} else {
					$result[$val[$col_name]][]=$val;
				}
			}
		}
		return $result;
	} // function groupResult
	
	/**
	 * 
	 * @param array $arTable - содержит таблицу имена колонок ассоциативные
	 * @param array $arSort - параметры сортировки ассоциативный массив индексы которого - названия колонок.
	 * 	перечисляем колонки в порядке сортировки. значение ASC (по возрастанию) и DESC (по убыванию).
	 * 	прим. $arSort=array('field_5'=>'asc', 'field_1'=>'asc');
	 * @return boolean
	 */
	public static function simpleSortTable(array &$arTable, array $arSort) {
		if ($arTable && is_array($arTable) && $arSort && is_array($arSort)) {
			foreach ($arSort as $key => $val) {
				$arSort[$key]=strtolower($val); // стандартизация парам. сортировки
			}
			$c=uasort($arTable, function ($a,$b) use ($arSort) {
				foreach ($arSort as $key => $val) {
					if (isset($a[$key]) && isset($b[$key])) {
						if (is_string($a[$key]) || is_string($b[$key])) {
							$r=strnatcasecmp($a[$key], $b[$key]);
						} else {
							if ($a[$key]<$b[$key]) {
								$r=-1;
							} elseif ($a[$key]>$b[$key]) {
								$r=1;
							} else {
								$r=0;
							}
						}
					} else {
						if (isset($a[$key]) && !isset($b[$key])) {
							$r=1;
						} elseif (!isset($a[$key]) && isset($b[$key])) {
							$r=-1;
						} elseif (!isset($a[$key]) && !isset($b[$key])) {
							$r=0;
						}
					}
					if ($r!=0) {
						if ($val=='desc') {
							if ($r==-1) {
								$r=1;
							} elseif ($r==1) {
								$r=-1;
							}
						}
						return $r;
					}
				} // end foreach
				return 0;
			});
			return $c;
		}
	} // end sortTable
// ==================================	
	/*public static function simpleFilterOneRow($arRow, $arFilterValue)
	{
		foreach ($arFilterValue as $sKey => $mValue)
		{
			if (isset($arRow[$sKey]))
			{
				if (is_array($mValue))
				{
					// задано множество возможных значений
					if (array_search($arRow[$sKey], $mValue)===false) return false; // среди возможных значений ничего не нашли
				}
				else
				{
					if ($arRow[$sKey]!=$mValue) return false; // не соответствует условию
				}
			} else return false;
		}
		return true;
	} // end filterOneRow*/

	/** Преобразует фильтр из формата simpleFilterLine в simpleFilter
	 * 
	 * @param array $arSimpleFilterLine
	 * @param array $arValidFiledNameList
	 * @return unknown[][]|unknown[]|string
	 */
	public static function convertConvertSimpleFilterLine(array $arSimpleFilterLine, array $arValidFiledNameList) {
		$arSF=array();
		if (empty($arValidFiledNameList)) return $arSF;
		foreach ($arSimpleFilterLine as $key => $val) {
			$maxFiledSize=-1;
			$fieldname='';
			// переберем список правильных полей таблицы и определим наличие каждого во входных данных.
			// возможен список полей типа: id, id_element, id_element_count и т.п.
			foreach ($arValidFiledNameList as $fkey => $fval) {
				if (stripos($val, $fval.'_')===0) {
					$s=strlen($fval);
					if ($maxFiledSize<$fval) {
						$maxFiledSize=$fval;
						$fieldname=$fval;
					}
				}
			}
			if ($fieldname) { // поле в фильтре определено
				$value=mb_substr($val, mb_strlen($fieldname)+1);
				// заносим значение в фильтр
				if ($value) {
					if (strpbrk($value, '%*?')!==false) {
						$resValue=array('String'=>$value);
						if (strpbrk($value, '*')!==false) {
							$value=str_replace(array('*', '%%%', '%%'), '%', $value);						
						}
						$resValue['like']=$value;
					} else {
						$resValue=$value;
					}
					
					if (isset($arSF[$fieldname])) {
						if (is_array($arSF[$fieldname])) {
							if (isset($arSF[$fieldname]['like'])) {
								$buff=$arSF[$fieldname];
								$arSF[$fieldname]=array($buff);
								unset($buff);
							}
						} else {
							$buff=$arSF[$fieldname];
							$arSF[$fieldname]=array($buff);
							unset($buff);
						}
						$arSF[$fieldname][]=$resValue;
					} else {
						$arSF[$fieldname]=$resValue;
					}
				}
			}
		}
		return $arSF;
	}
// =======================================
	public static function simpleFilterOneRow(array $arRow, array $arFilterValue) // расширенный фильтр одной строки таблицы. формат
	/* $arFilterValue ассоциативный массив, ключи массива соотв. ключам $arRow
	значение фильтра - массив с условиями:
	для чисел:
	lo - нижняя граница числового значения
	hi - верхняя граница числового значения
		если число больше lo и меньше hi, условие выполняется
	eq-lo - больше или равно
	eq-hi - меньше или равно
	для всех:
		like - строка в формате LIKE SQL для определения строки
	если значение является числом/строкой или списком значений, то проверяется на точное совпадение (как в simpleFilter)
	Дополнительно:
		зарезервированные имена полей
		@include - дополнительные условия в формате simpleFilter для включения
		@exclude - дополнительные условия в формате simpleFilter для исключения строки из выборки 
	*/
	{
		$is_result=true;
		foreach ($arFilterValue as $sKey => $mValue) {
			if (isset($arRow[$sKey])) { // проверка одного поля по фильтру
				$is_ok=false;
				if (is_array($mValue) && (isset($mValue['lo']) || isset($mValue['hi']) || isset($mValue['eq-lo']) || isset($mValue['eq-hi']) )) {
					if (!$is_ok && (isset($mValue['lo']) || isset($mValue['hi']) || isset($mValue['eq-lo']) || isset($mValue['eq-hi']))) {
							if (isset($mValue['lo'])) $mValue['lo']=floatval($mValue['lo']);
							if (isset($mValue['hi'])) $mValue['hi']=floatval($mValue['hi']);
							if (empty($mValue['eq-lo'])) { // признак - "больше или равно"
								$mValue['eq-lo']=false;
							}
							if (empty($mValue['eq-hi'])) { // признак - "меньше или равно"
								$mValue['eq-hi']=false;
							}							
							$fData=floatval($arRow[$sKey]);
							if (isset($mValue['lo']) && isset($mValue['hi']) && $mValue['lo']==$mValue['hi']) { // проверка на равенство
								if ($mValue['lo']==$fData) $is_ok=true;
							} else { // проверка на интервал
								$is_ok=true;
								if (isset($mValue['lo'])) { 
									$is_ok=false;
									if ($mValue['eq-lo']) {
										if ($fData>=$mValue['lo']) {
											$is_ok=true;
										}
									} elseif ($fData>$mValue['lo']) {
										$is_ok=true;
									}
								}
								if (isset($mValue['hi']) && $is_ok) { 
									if ($mValue['eq-hi']) {
										if ($fData<=$mValue['hi']) {
											$is_ok=true;
										} else {
											$is_ok=false;
										}
									} elseif ($fData<$mValue['hi']) {
										$is_ok=true;
									} else {
										$is_ok=false;
									}
								}
							}
					}
				}
				
				if (!$is_ok && is_array($mValue) && isset($mValue['like'])) { // задана конструкция LIKE
					if (!is_string($arRow[$sKey])) $arRow[$sKey]=strval($arRow[$sKey]);
					$strPregPattern=funcSearch::convertLikeToRegex($mValue['like']);
					if (preg_match($strPregPattern, $arRow[$sKey])==true) $is_ok=true;
				}
				
				if (!$is_ok) {
					// простая фильтрация
					if (is_array($mValue)) {
						// задано множество возможных значений
						if (array_search($arRow[$sKey], array_diff_key($mValue, static::$simpleFilterSpec))!==false) $is_ok=true; // среди возможных значений ничего не нашли
					} else {
						if ($arRow[$sKey]==$mValue) $is_ok=true; // не соответствует условию
					}
				}
				if (!$is_ok) {
					$is_result=false;
				}
			} else {
				$is_result=false; // такого поля в данных нет - не соответствует фильтру
			}
			if (!$is_result) {
				break;
			}
		} // end foreach
		if (!$is_result && !empty($arFilterValue['@include'])) { // @include
			$is_result=static::simpleFilterOneRow($arRow, $arFilterValue['@include']);
		}
		if ($is_result && !empty($arFilterValue['@exclude'])) { // @exclude
			$is_result=! static::simpleFilterOneRow($arRow, $arFilterValue['@exclude']);
		}
		return $is_result;
	} // end advancedFilterOneRow/simpleFilterOneRow
// =====================================	
	public static function simpleFilterTable(array &$arTable, array $arFilterValue)
	/* фильтрация таблицы по значениям
	$arTable - двумерный массив (таблица)
	$arFilterValue - массив со значениями полей строки
		arFilterValue[<имя колонки>]=<значение поля колонки>
		если в строке присутствуют все колонки из условия, равные заданным значениями arFilterValue - строка отбирается в результат
		<значение поля колонки> может быть массивом - можно задавать несколько возможных значений - если хотябы одно подходит, то ОК 
	*/
	{
		if ($arFilterValue!=false && is_array($arFilterValue) && is_array($arTable))
		{
			return array_filter($arTable, function (&$arRow) use ($arFilterValue)
				{
					return funcArray::simpleFilterOneRow($arRow, $arFilterValue);
				}
			);
		}		
		return $arTable;
	} // end filterTable
// =====================================
	/*public static function simpleFilterTableVariant(array &$arTable, array ...$arFilterValues) {
	/ Фильтрация таблицы по нескольким фильтрам.
	Для прохождения фильтра, строка таблицы должна соответствовать хотя бы одному фильтру.
	/
		if ($arTable)
		{
			return array_filter($arTable, function (&$arRow) use ($arFilterValues)
				{
					foreach ($arFilterValues as $arFilterValue) { // перебираем фильтры. как только найдено соответствие - прекращаем перебор.
						if (funcArray::simpleFilterOneRow($arRow, $arFilterValue)) {
							return true;
						}
					}
					return false;
				}
			);
		} else {
			return $arTable;
		}
	} // end func 
	*/
// =====================================
	public static function mergeSimpleFilter(array $arFilter1, array $arFilter2) // объединяет несколько simpleFilter в один
	{
		if ($arFilter1==false) return $arFilter2;
		if ($arFilter2==false) return $arFilter1;
		$arRes=array_merge_recursive($arFilter1, $arFilter2);
		array_walk($arRes, function (&$item, $key) {
			if (is_array($item)) $item=array_unique($item);
		});
		return $arRes;
	} // end mergeSimpleFilter
// =====================================
	public static function completeTable(array & $arTblPrim, $fnTblPrim, array & $arTblSec, $fnTblSec, $rewrite=false, array $arOnlyFields=array()) // дополняет первую таблицу данными из второй
	/*
	$arTblPrim - главная таблица,
	$fnTblPrim - поле главной таблицы, которое определеяет данные 2-й таблицы
	$arTblSec - подчиненная таблица,
	$fnTblSec - поле подчиненной таблицы,
	$rewrite=false - перезаписывать данные $arTbl_1 если имена полей совпадают с $arTable_2
	$arOnlyFields - будут добавлены только эти поля
	*/
	{
		foreach ($arTblPrim as $key => & $arItemPrim)
		{
			$keySec=static::inTable($arTblSec, $fnTblSec, $arItemPrim[$fnTblPrim]);
			if ($keySec!==false && isset($arTblSec[$keySec]))
			{
				$arSecLine=$arTblSec[$keySec];
				if ($arOnlyFields) static::limitArrayKeys($arSecLine, $arOnlyFields);
				if ($rewrite)
				{
					$arItemPrim=array_merge($arItemPrim, $arSecLine);
				} else $arItemPrim=array_merge($arSecLine, $arItemPrim);
			}
		}
	}
// =====================================	
	/** выдает таблицу в виде html
	 * 
	 * @param array $in_data
	 * @param array $param
	 * @return string
	 */
	public static function table2Html(array &$in_data, $param=array()) {
		$out='';
		if (count($in_data)>0) {
			//if ($if_print_fieldname)
			{
				// пропечатаем имена столбцов
				$out.='<tr>';
				foreach($in_data[0] as $name => $data) {
					$out.='<td'.$fns.'>'.$name.'</td>';
				}
				$out.='</tr>';
			}
			// основное тело таблицы
			foreach ($in_data as $key => $val) {
				$out.='<tr>';
				foreach($val as $key2 => $val2) {
					$out.='<td'.$tds.'>'.$val2.'</td>';
				}
				$out.='</tr>';
			}
		}
		return $out;
	}
// ===========================================	
	/** определяет двумерность массива по первому значению
	 * 
	 * @param array $table
	 * @return boolean
	 */
	public static function check2DArray(array &$table) {
		if (is_array($table)) {
			reset($table);
			//list($first)=$table;
			if (is_array(current($table))) return true;
		}
		return false;
	} // end check2DArray
// ============================================	
	/** ограничивает ключи массива только списком $arKeyList
	 * 
	 * @param unknown $row
	 * @param unknown $arKeyList
	 */
	public static function limitArrayKeys(&$row, $arKeyList) {
		$arDelKey=array_diff(array_keys($row), $arKeyList);
		foreach ($arDelKey as $num => $key) {
			unset($row[$key]);
		}
	} // end limitArrayKeys
// =============================================
	/** суммирует элементы с определенными именами
	 * 
	 * @param array $table
	 * @param unknown $col_name
	 * @return number|unknown
	 */
	public static function sumColumn(array &$table, $col_name) {
		$sum=0;
		array_walk($table, function ($val, $key) use (&$sum, $col_name) {
			if (isset($val[$col_name])) {
				$sum+=$val[$col_name];
			}
		});
		return $sum;
	} // end sumColumn

	/** заполнить колонку $col_name массива $table значением $value
	 * 
	 * @param array $table
	 * @param unknown $col_name
	 * @param unknown $value
	 */
	public static function fillColumn(array &$table, $col_name, $value) {
		foreach ($table as &$val) {
			$val[$col_name]=$value;
		}
	} // end fillColumn

	/** функция ищет первый элемент (возвращает ключ), который соответствует условию, которое определяется $func
	 * 
	 * @param array $table
	 * @param unknown $func - функция $func($key, $row)
	 * @return unknown|boolean
	 */
	public static function inTableCallable(array $table, $func)	{
		foreach ($table as $key => $arElement) {
			if ($func($key, $arElement)) {
				return $key;
			}
		}
		return false;
	} // end 
	
	/** Ищет значение $value в колонке $col_name таблицы $table
	 * 
	 * @param array $table
	 * @param unknown $col_name
	 * @param unknown $value
	 * @return \x51\functions\unknown|boolean
	 */
	public static function inTable(array $table, $col_name, $value) {
		return static::inTableCallable($table, function ($key, $row) use ($col_name, $value) {
				return (isset($row[$col_name]) && $row[$col_name]==$value);
			}
		);
	} // end func
// =============================================
// ПРЕОБРАЗОВАНИЕ simpleFilter
// =============================================
	
	/** преобразование simpleFilter в формат фильтра битрикса
	 * 
	 * @param array $arSF
	 * @return unknown[][]
	 */
	public static function sf_bitrix(array $arSF) {
		$arBF=array();
		if (is_array($arSF) && $arSF)
			foreach ($arSF as $fn => $filter) {
				$valBF=$filter;
				$prefixBF='';
				if (is_array($filter)) {
					if (isset($filter['like'])) {
						$valBF=$filter['like'];
					}
					if (isset($filter['lo'])) {
						$prefixBF='>';
						$valBF=$filter['lo'];
					}
					if (isset($filter['hi'])) {
						$prefixBF='<';
						$valBF=$filter['hi'];
					}
					if (isset($filter['lo']) && isset($filter['hi'])) {
						$valBF=array($filter['lo'], $filter['hi']);
					}
				}
				$arBF[$prefixBF.$fn]=$valBF;
			}
		return $arBF;
	} // end sf_bitrix
} // end class