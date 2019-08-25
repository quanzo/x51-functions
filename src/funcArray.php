<?php
// ###
namespace x51\functions;

// /###
/* simpleFilter - ������
�������� ������� - ������ � ���������:
	��� �����:
	lo - ������ ������� ��������� ��������
	hi - ������� ������� ��������� ��������
		���� ����� ������ lo � ������ hi, ������� �����������
	eq-lo - ������ ��� �����
	eq-hi - ������ ��� �����
	��� ����:
		like - ������ � ������� LIKE SQL ��� ����������� ������
	�������������:
		����������������� ����� �����
		@include - �������������� ������� � ������� simpleFilter ��� ���������
		@exclude - �������������� ������� � ������� simpleFilter ��� ���������� ������ �� ������� 
������:
	array(
		'id'=>array(10, 15, 20), // ������������� ������ ��������
		'point'=>array(
			'like'=>'%���%' // ���� �������� ����� ���
		),
		'size'=>array(
			'lo'=>10 // ������ 10
		),
		'population'=>array(
			'hi'=> 1000 // ������ 1000
		),
		'@include'=>array(
			'size'=>1, // ������������� �������� � �������
		),
		'@exclude'=>array(
			'population'=>700 // ��������� �� �������
		),
	);

simpleFilterLine - ������
	�� ������������� ������
	�������� ����:
		<��� ����>_<��������> - id_100 ��� id_element_100 (id_element - ��� ����)

*/
// ������� ��� ������ � ���������
/*
public static function callableArray($ar) �� ��������� ��������� ������ � $ar (����� ��������� ����� ���� ��������, ������ ���������� ���� ������� ���� ������) ��������� �������������� ������
public static function eqTable(array $ar_1, array $ar_2, array $arIgnoredField = array(), $orderStrict=false) {
public static function eqRow(array $row_1, array $row_2, array $arIgnoredField = array()) {

public static function selectColumn($arr, $col_name, $param=array()) // �� ������������ �������������� ������� ���������� ���� ������� � ������������ ���������� ������
public static function groupResult(array $arr, $col_name, array $param=array()) // ������������� ��������� - ���������� �� �������� ������� - ���. � ����arr[col_name][�����]=<������ ������> (����� non_uniqueness=false �� �����) ��� arr[col_name]=<������ ������> (����� non_uniqueness=true �� �����)

public static function simpleSortTable(&$arTable, $arSort) // ��������� �������

public static function simpleFilterOneRow($arRow, $arFilterValue)
public static function simpleFilterTable(&$table, $arFilterValue)
//public static function simpleFilterTableVariant(array &$arTable, array ...$arFilterValues) { ����������������
public static function mergeSimpleFilter($arFilter1, $arFilter2) // ���������� ��������� simpleFilter � ����
public static function completeTable(array & $arTblPrim, $fnTblPrim, array & $arTblSec, $fnTblSec, $rewrite=false) // ��������� ������ ������� ������� �� ������
!!! public static function advancedFilterRow($arRow, $arFilterValue) // ����������� ������ ����� ������ �������. ������
public static function table2Html(&$in_data, $param=array())// �������� �������
public static function check2DArray(&$table); // ���������� ����������� ������� �� ������� ��������
public static function limitArrayKeys(&$table, $arKeyList) // ������������ ����� ������� ������ ������� $arKeyList
public static function sumColumn(&$table, $col_name) // ��������� �������� � ������������� �������
public static function fillColumn(array &$table, $col_name, $value) // ��������� ������� $col_name ������� $table ��������� $value
public static function inTableCallable(array $table, $func) // ������� ���� ������ ������� (���������� ����), ������� ������������� �������, ������� ������������ $func
public static function inTable(array $table, $col_name, $value) // ���� �������� $value � ������� $col_name ������� $table 

public static function sf_bitrix(array $arSF) // �������������� simpleFilter � ������ ������� ��������

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
	/** �� ��������� ��������� ������ � $ar (����� ��������� ����� ���� ��������, ������ ���������� ���� ������� ���� ������) ��������� �������������� ������
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
	/** ���������� ��� �������
	 * 
	 * @param array $ar_1
	 * @param array $ar_2
	 * @param array $arIgnoredField - ������ �����, ������� ����� ������������ � �������� ���������
	 * @param string $orderStrict - ��� ��������� ��������� ������� ����� �������
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
	
	/** ��������� ���� �������� (�����). �� ��������� �� ������ ������� ��������� � �������.
	 * 
	 * @param array $row_1
	 * @param array $row_2
	 * @param array $arIgnoredField - ������ �����, ������� �� ����� ����������� ��� ���������
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

	/** ������� ������� �� �������.
	 * ������� ��������� ������ �� ����� �������.
	 * ������� ������ - ����������� �����.
	 * ������� ����� ������� ����� ���� ��������� ($param['saveIndex']=true) ��� ������ ����� ���� � ��������� �������� �� 0 ($param['saveIndex']=false)
	 * ���� ������� � ������������ �������� �� ����� ������� � ����� ��������� �� ��� ����� ��������� $param['saveValue']=true ��� �� ������� � ��������� $param['saveIndex']=false
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
	
	
	/** ������� ��������� ������ �� ���������� �������.
	 * ������� ������ - ����������� �����.
	 * ������� ����� ������� ����� ���� ��������� ($param['saveIndex']=true) ��� ������ ����� ���� � ��������� �������� �� 0 ($param['saveIndex']=false)
	 * 
	 * @param array $arr - ������� �������
	 * @param array $cols - ������ ���������� �������
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
	
	/** ������������� ��������� - ���������� �� �������� ������� - ��� ������� ���������� �������� - �������������� ������ ���������� ���� arr[col_name][�����]=<������ ������> (��. ����� non_uniqueness) - ������ ��� col_name ����� ���� �� ����������. �������� $arr ������ ���� ��������� ��������. ���� col_name ���� � ������� �� ��� ������ �� �������� � ����������.
	 * 
	 * @param array $arr - ������������ �������
	 * @param string $col_name - ��� ������� �� ������� ������������ ����������� 
	 * @param array $param - �������������� ���������
	 * 	non_uniqueness - true/false(�� �����) - ���� true, �� �������� �� ��������� � � ���������� ������� �������� � ������� col_name ����� ������������ ���� �������� ������
	 *  ���� false (�� ���������) �� �������������� ������ ����� ��������� � ����� ���  arr[col_name]=array
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
	 * @param array $arTable - �������� ������� ����� ������� �������������
	 * @param array $arSort - ��������� ���������� ������������� ������ ������� �������� - �������� �������.
	 * 	����������� ������� � ������� ����������. �������� ASC (�� �����������) � DESC (�� ��������).
	 * 	����. $arSort=array('field_5'=>'asc', 'field_1'=>'asc');
	 * @return boolean
	 */
	public static function simpleSortTable(array &$arTable, array $arSort) {
		if ($arTable && is_array($arTable) && $arSort && is_array($arSort)) {
			foreach ($arSort as $key => $val) {
				$arSort[$key]=strtolower($val); // �������������� �����. ����������
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
					// ������ ��������� ��������� ��������
					if (array_search($arRow[$sKey], $mValue)===false) return false; // ����� ��������� �������� ������ �� �����
				}
				else
				{
					if ($arRow[$sKey]!=$mValue) return false; // �� ������������� �������
				}
			} else return false;
		}
		return true;
	} // end filterOneRow*/

	/** ����������� ������ �� ������� simpleFilterLine � simpleFilter
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
			// ��������� ������ ���������� ����� ������� � ��������� ������� ������� �� ������� ������.
			// �������� ������ ����� ����: id, id_element, id_element_count � �.�.
			foreach ($arValidFiledNameList as $fkey => $fval) {
				if (stripos($val, $fval.'_')===0) {
					$s=strlen($fval);
					if ($maxFiledSize<$fval) {
						$maxFiledSize=$fval;
						$fieldname=$fval;
					}
				}
			}
			if ($fieldname) { // ���� � ������� ����������
				$value=mb_substr($val, mb_strlen($fieldname)+1);
				// ������� �������� � ������
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
	public static function simpleFilterOneRow(array $arRow, array $arFilterValue) // ����������� ������ ����� ������ �������. ������
	/* $arFilterValue ������������� ������, ����� ������� �����. ������ $arRow
	�������� ������� - ������ � ���������:
	��� �����:
	lo - ������ ������� ��������� ��������
	hi - ������� ������� ��������� ��������
		���� ����� ������ lo � ������ hi, ������� �����������
	eq-lo - ������ ��� �����
	eq-hi - ������ ��� �����
	��� ����:
		like - ������ � ������� LIKE SQL ��� ����������� ������
	���� �������� �������� ������/������� ��� ������� ��������, �� ����������� �� ������ ���������� (��� � simpleFilter)
	�������������:
		����������������� ����� �����
		@include - �������������� ������� � ������� simpleFilter ��� ���������
		@exclude - �������������� ������� � ������� simpleFilter ��� ���������� ������ �� ������� 
	*/
	{
		$is_result=true;
		foreach ($arFilterValue as $sKey => $mValue) {
			if (isset($arRow[$sKey])) { // �������� ������ ���� �� �������
				$is_ok=false;
				if (is_array($mValue) && (isset($mValue['lo']) || isset($mValue['hi']) || isset($mValue['eq-lo']) || isset($mValue['eq-hi']) )) {
					if (!$is_ok && (isset($mValue['lo']) || isset($mValue['hi']) || isset($mValue['eq-lo']) || isset($mValue['eq-hi']))) {
							if (isset($mValue['lo'])) $mValue['lo']=floatval($mValue['lo']);
							if (isset($mValue['hi'])) $mValue['hi']=floatval($mValue['hi']);
							if (empty($mValue['eq-lo'])) { // ������� - "������ ��� �����"
								$mValue['eq-lo']=false;
							}
							if (empty($mValue['eq-hi'])) { // ������� - "������ ��� �����"
								$mValue['eq-hi']=false;
							}							
							$fData=floatval($arRow[$sKey]);
							if (isset($mValue['lo']) && isset($mValue['hi']) && $mValue['lo']==$mValue['hi']) { // �������� �� ���������
								if ($mValue['lo']==$fData) $is_ok=true;
							} else { // �������� �� ��������
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
				
				if (!$is_ok && is_array($mValue) && isset($mValue['like'])) { // ������ ����������� LIKE
					if (!is_string($arRow[$sKey])) $arRow[$sKey]=strval($arRow[$sKey]);
					$strPregPattern=funcSearch::convertLikeToRegex($mValue['like']);
					if (preg_match($strPregPattern, $arRow[$sKey])==true) $is_ok=true;
				}
				
				if (!$is_ok) {
					// ������� ����������
					if (is_array($mValue)) {
						// ������ ��������� ��������� ��������
						if (array_search($arRow[$sKey], array_diff_key($mValue, static::$simpleFilterSpec))!==false) $is_ok=true; // ����� ��������� �������� ������ �� �����
					} else {
						if ($arRow[$sKey]==$mValue) $is_ok=true; // �� ������������� �������
					}
				}
				if (!$is_ok) {
					$is_result=false;
				}
			} else {
				$is_result=false; // ������ ���� � ������ ��� - �� ������������� �������
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
	/* ���������� ������� �� ���������
	$arTable - ��������� ������ (�������)
	$arFilterValue - ������ �� ���������� ����� ������
		arFilterValue[<��� �������>]=<�������� ���� �������>
		���� � ������ ������������ ��� ������� �� �������, ������ �������� ���������� arFilterValue - ������ ���������� � ���������
		<�������� ���� �������> ����� ���� �������� - ����� �������� ��������� ��������� �������� - ���� ������ ���� ��������, �� �� 
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
	/ ���������� ������� �� ���������� ��������.
	��� ����������� �������, ������ ������� ������ ��������������� ���� �� ������ �������.
	/
		if ($arTable)
		{
			return array_filter($arTable, function (&$arRow) use ($arFilterValues)
				{
					foreach ($arFilterValues as $arFilterValue) { // ���������� �������. ��� ������ ������� ������������ - ���������� �������.
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
	public static function mergeSimpleFilter(array $arFilter1, array $arFilter2) // ���������� ��������� simpleFilter � ����
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
	public static function completeTable(array & $arTblPrim, $fnTblPrim, array & $arTblSec, $fnTblSec, $rewrite=false, array $arOnlyFields=array()) // ��������� ������ ������� ������� �� ������
	/*
	$arTblPrim - ������� �������,
	$fnTblPrim - ���� ������� �������, ������� ����������� ������ 2-� �������
	$arTblSec - ����������� �������,
	$fnTblSec - ���� ����������� �������,
	$rewrite=false - �������������� ������ $arTbl_1 ���� ����� ����� ��������� � $arTable_2
	$arOnlyFields - ����� ��������� ������ ��� ����
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
	/** ������ ������� � ���� html
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
				// ����������� ����� ��������
				$out.='<tr>';
				foreach($in_data[0] as $name => $data) {
					$out.='<td'.$fns.'>'.$name.'</td>';
				}
				$out.='</tr>';
			}
			// �������� ���� �������
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
	/** ���������� ����������� ������� �� ������� ��������
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
	/** ������������ ����� ������� ������ ������� $arKeyList
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
	/** ��������� �������� � ������������� �������
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

	/** ��������� ������� $col_name ������� $table ��������� $value
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

	/** ������� ���� ������ ������� (���������� ����), ������� ������������� �������, ������� ������������ $func
	 * 
	 * @param array $table
	 * @param unknown $func - ������� $func($key, $row)
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
	
	/** ���� �������� $value � ������� $col_name ������� $table
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
// �������������� simpleFilter
// =============================================
	
	/** �������������� simpleFilter � ������ ������� ��������
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