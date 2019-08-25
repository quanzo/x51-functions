<?php
namespace x51\functions;

/* *** Строковые функции ***

function lmv_substr_replace_array($stack, $beginpos_array, $length_array, $rep_array) // замены в строке
function lmv_substr_part($stack, $lpart, $rpart, $start = 0) // возвращает фрагмент строки ограниченный подстроками
function lmv_substr_part_all($stack, $lpart, $rpart, $start = 0) // находит все конструкции по порядку
function lmv_substr($stack, $start, $length) // выдает подстроку
function lmv_pos($stack, $needle, $start = 0) // определяет позицию подстроки
public static function lmv_strrpos($stack, $needle, $end = -1) {// Обратный поиск подстроки в строке. Поиск ведется в отрезке строки начиная с начала строки и до $end
public static function multiReplace($search, $replace, $subject) // замена пока все не будет заменено
public static function removeRN($stack)
public static function removeDblSpace($stack)
function lmv_getStrCharType($stack) // определяет тип символов строки. результат в виде массива
public static function strDateRange($beginDate, $endDate = '') // выдает диапазон дат в иде строки. даты указывать в виде строк в формате 'DD.MM.YYYY HH:MI:SS'
public static function checkPhone($value) // является ли строка телефонным номером
public static function checkEmptyString($value) // проверяет строку на отсутствие данных (лишние символы препинания и т.п. не учитываются)
public static function soundex($word)
public static function metaphone($word)
 */

class funcString
{

    public static $arSymbolGroup = array(
        'number' => '0123456789',
        'ruschar' => 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя',
        'engchar' => 'abcdefghijklmnopqrstuvwxyz',
        'space' => " ",
        'plusminus' => '-+',
        'brackets' => '()[]{}',
    );

    protected static $arModifyCommand = array(
        'one_line' => array(
            array("\r", "\n"),
            array('', ' '),
        ),
        'delete_emptystring' => array(
            array("\r", "\n\n"),
            array('', ''),
        ),
        'delete_tab' => array(
            array("\t"),
            array(''),
        ),
        'delete_doublespace' => array(
            array('  '),
            array(' '),
        ),
        'tab2space' => array(
            array("\t"),
            array(' '),
        ),
    );

//////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает первый найденный needle в строке haystack (тот который ближе к offset)
     *
     * @param string $haystack
     * @param array $needle
     * @param integer $offset
     * @return integer|boolean
     */
    public static function findFirst($haystack, array $needle, $offset = 0)
    {
        $result = false;
        if ($needle && $haystack && is_string($haystack)) {
            foreach ($needle as $n) {
                $p = strpos($haystack, $n, $offset);
                if ($result === false || ($p !== false && $p < $result)) {
                    $result = $p;
                }
            }
        }
        return $result;
	} // end findFirst
	
    /**
     * Разбивает строку. Разделители задаем в виде массива. В порядке приоритета использования.
     *
     * @param array $delimiter
     * @param string $str
	 * @param boolean $cleanup
     * @return array
     */
    public static function explode(array $delimiter, $str, $cleanup = false)
    {
		$result = [];
		$size = strlen($str);
		if ($str && $delimiter) {
			$offset = 0;
			do {
				$p = static::findFirst($str, $delimiter, $offset);
				//var_dump( $p);var_dump( $offset);
				if ($p !== false) {
					$r = substr($str, $offset, $p-$offset);
					if ($r && $cleanup) {
						$r = str_replace($delimiter, '', $r);
					}
					if ($cleanup) {
						if ($r) {
							$result[] = $r;		
						}
					} else {
						$result[] = $r;
					}
					$offset = $p+1;
				}
			} while ($p !== false);
			if ($offset <= $size-1) {
				$result[] = substr($str, $offset);
			}
        }
        return $result;
    } // end explode

/** замены в строке
 * в строке $stack производится замена фрагментов с позиций $beginpos_array[i] длинной $length_array[i] на $rep_array[i]
 *
 * @param string $stack
 * @param array $beginpos_array
 * @param array $length_array
 * @param array $rep_array
 * @return string
 */
    public static function lmv_substr_replace_array($stack, array $beginpos_array, array $length_array, array $rep_array)
    {
        // подсчитаем длинну результирующей строки
        $stack_len = static::Strlen($stack);
        $rep_count = sizeof($rep_array);
        $bpa_count = sizeof($beginpos_array);

        $buffer = '';
        $start1 = 0;
        for ($i = 0; $i < $bpa_count; $i++) {
            $scount = $beginpos_array[$i] - $start1;
            $buffer .= static::lmv_substr($stack, $start1, $scount) . $rep_array[$i];
            $start1 = $beginpos_array[$i] + $length_array[$i];
        }
        // оконцовка
        $scount = $stack_len - $start1;
        $buffer .= static::lmv_substr($stack, $start1, $scount);
        return $buffer;
    }

/** возвращает фрагмент строки ограниченный подстроками
 *
 *
 * @param unknown $stack
 * @param unknown $lpart
 * @param unknown $rpart
 * @param number $start
 * @return boolean[]|string[]|unknown[]|number[]|boolean
 * функция возвращает:
 *     left_pos - позиция левой части $lpart в строке $stack
 *     right_pos - позиция правой части -/-/-
 *     body - содержимое строки $stack между $lpart и $rpart
 *     body2 - содержимое строки $stack вместе с $lpart и $rpart
 * если не найдено $lpart или $rpart то возвращается false
 */
    public static function lmv_substr_part($stack, $lpart, $rpart, $start = 0)
    {
        $result = array('left_pos' => false, 'right_pos' => false, 'body' => '', 'body2' => '');
        $p1 = static::lmv_pos($stack, $lpart, $start);
        if ($p1 !== false) {
            $lpart_len = static::Strlen($lpart);
            $rpart_len = static::Strlen($rpart);
            $start2 = $p1 + $lpart_len;
            $p2 = static::lmv_pos($stack, $rpart, $start2);

            if ($p2 !== false) {
                $result['left_pos'] = $p1;
                $result['right_pos'] = $p2;
                $result['body'] = static::lmv_substr($stack, $start2, $p2 - $start2);
                $result['body2'] = static::lmv_substr($stack, $p1, $p2 + $rpart_len - $p1);
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

/** находит все конструкции по порядку
 * в отличии от lmv_substr_part возвращает все вхождения в строке в порядке их нахождения.
 * возвращается нумерованный массив элементы которого по структуре соответствуют lmv_substr_part
 *
 * @param unknown $stack
 * @param unknown $lpart
 * @param unknown $rpart
 * @param number $start
 * @return \x51\functions\boolean[][]|\x51\functions\string[][]|\x51\functions\unknown[][]|\x51\functions\number[][]|boolean[]
 */
    public static function lmv_substr_part_all($stack, $lpart, $rpart, $start = 0)
    {
        $res = array();
        $start2 = $start;
        $e_f = false;
        do {
            $buf1 = static::lmv_substr_part($stack, $lpart, $rpart, $start2);
            if ($buf1 !== false) {
                $res[] = $buf1;
                $start2 = $buf1['right_pos']+static::Strlen($rpart);
                $e_f = true;
            } else {
                $e_f = false;
            }
        } while ($e_f == true);
        return $res;
    }

/** выдает подстроку
 *
 * @param unknown $stack
 * @param unknown $start
 * @param unknown $length
 * @return string|unknown
 */
    public static function lmv_substr($stack, $start, $length)
    {
        $stack_len = static::Strlen($stack);
        if ($stack_len == 0) {return '';}
        if ($length <= 0) {return '';}
        if ($start < 0 || $start > $stack_len) {return '';}
        if (function_exists('mb_substr')) {
            return mb_substr($stack, $start, $length);
        } elseif (function_exists('substr')) {
            return substr($stack, $start, $length);
        } else {
            if (($start + $length - 1) > ($stack_len - 1)) {
                $slen = $stack_len - $start + 1;
            } else {
                $slen = $length;
            }
            $buffer = str_repeat(' ', $slen);
            for ($i = $start; $i < $start + $slen; $i++) {
                $buffer{$i - $start} = $stack{$i};
            }
            return $buffer;
        }
    }

/** определяет позицию подстроки
 *
 * @param unknown $stack
 * @param unknown $needle
 * @param number $start
 * @return boolean|number
 */
    public static function lmv_pos($stack, $needle, $start = 0)
    {
        if (!$needle) {
            return false;
        }
        if ($start < 0) {
            $start = 0;
        }

        $stack_len = static::Strlen($stack);
        $needle_len = static::Strlen($needle);
        $nnend = $stack_len - $start;
        if ($nnend < $needle_len) {
            return false;
        }

        if (function_exists('mb_strpos')) {
            return mb_strpos($stack, $needle, $start);
        } elseif (function_exists('strpos')) {
            return strpos($stack, $needle, $start);
        } else {
            $is_end = false;
            $curr_pos = $start;
            while (!$is_end) {
                $nnend = $stack_len - $curr_pos; // длинна до конца строки
                if ($nnend < $needle_len) {return false;} else {
                    if ($stack{$curr_pos} == $needle{0}) {
                        // проверка строки
                        $needle_pos = 0;
                        $curr_pos2 = $curr_pos;
                        $is_end_cycle = false;
                        $is_correct = true;
                        while (!$is_end_cycle) {
                            if ($stack{$curr_pos2} != $needle{$needle_pos}) {
                                // не найдено
                                $is_end_cycle = true;
                                $is_correct = false;
                            }
                            $curr_pos2++;
                            $needle_pos++;
                            if ($needle_pos > ($needle_len - 1)) {$is_end_cycle = true;}
                        }
                        if ($is_correct) {
                            return $curr_pos; // возвращаем позицию
                        }
                    }
                    $curr_pos++;
                    if ($curr_pos > ($stack_len - 1)) {$is_end = true;}
                }
            }
            return false;
        }
    }

    public static function Strlen($stack)
    {
        $funcName = 'strlen';
        if (function_exists('mb_strlen')) {
            $funcName = 'mb_strlen';
        }

        return $funcName($stack);
    } // Strlen

/** Обратный поиск подстроки в строке.
 * Поиск ведется в отрезке строки начиная с начала строки и до $end
 *
 * @param unknown $stack
 * @param unknown $needle
 * @param unknown $end
 */
    public static function Strrpos($stack, $needle, $end = -1)
    {
        $funcName = 'strrpos';
        if (function_exists('mb_strrpos')) {
            $funcName = 'mb_strrpos';
        }

        $stackSize = static::Strlen($stack);
        $needleSize = static::Strlen($needle);
        if ($end == -1 || $end >= $stackSize) {
            return $funcName($stack, $needle);
        } else {
            $buffer = static::lmv_substr($stack, 0, $end);
            return $funcName($buffer, $needle);
        }
    } // end Strrpos

// end lmv_str

// BEGIN *****************************************************************************************************************************

    protected static function strMultiReplace($search, $replace, $subject)
    {
        if ($subject && $search) {
            $maxCountReplace = 0;
            if (strpos($replace, $search) !== false) {
                $maxCountReplace = 1;
            }
            // если from содержится в to - это приямой путь к зацикливанию.
            $countRep = 0;
            do {
                $subject = str_replace($search, $replace, $subject, $countRep);
            } while ($countRep > 0 && $maxCountReplace == 0 && $subject);
        }
        return $subject;
    }

/** выполняется замена пока все не будет заменено
 *
 * @param unknown $search
 * @param unknown $replace
 * @param unknown $subject
 * @return unknown
 */
    public static function multiReplace($search, $replace, $subject)
    {
        if ($search) {
            if (!is_array($subject)) {
                $where[0] = $subject;
            } else {
                $where = $subject;
            }
            if (!is_array($search)) {
                $from[0] = &$search;
            } else {
                $from = $search;
            }
            if (!is_array($replace)) {
                $to[0] = &$replace;
            } else {
                $to = &$replace;
            }
            $to_size = sizeof($to);
            foreach ($where as &$whereVal) {
                reset($to);
                $toVal = current($to);
                $i_to = 0;
                foreach ($from as $fromVal) {
                    $whereVal = static::strMultiReplace($fromVal, $toVal, $whereVal);
                    if ($i_to < ($to_size - 1)) {
                        $toVal = next($to);
                        $i_to++;
                    }
                }
            }
            if (!is_array($subject)) {
                return $where[0];
            } else {
                return $where;
            }
        }
        return $subject;
    } // end multiReplace

/** Удалить переводы строки
 *
 * @param unknown $stack
 * @return mixed
 */
    public static function removeRN($stack)
    {
        return str_replace(array("\r", "\n"), '', $stack);
    }

/** Удалить двойные пробелы
 *
 * @param unknown $stack
 * @return \x51\functions\unknown
 */
    public static function removeDblSpace($stack)
    {
        return static::multiReplace('  ', ' ', $stack);
    }

/** Определяет тип символов строки.
 * Результат в виде массива где подсчитано кол-во символов из каждой группы, общее кол-во.
 *
 *
 * @param unknown $stack
 * @param string $arSymbolGroup
 * @return number[]|unknown[]|NULL[]|mixed[]
 * формат результата:
 *     индекс - соотв public static $Chars
 *     значение - кол-во вхождений символов этой группы
 * дополнительные индексы
 *     Identified - кол-во определенных символов
 *     notIdentified - кол-во символов которые не определены
 *
 */
    public static function lmv_getStrCharType($stack, $arSymbolGroup = false)
    {
        if ($arSymbolGroup == false) {
            $Chars = &static::$arSymbolGroup;
        } else {
            $Chars = &$arSymbolGroup;
        }

        $arRes = array('Identified' => 0, 'notIdentified' => static::Strlen($stack));
        if ($stack != false) {
            $arUniqueChars = count_chars(mb_strtolower($stack), 1); // уникальные символы
            foreach ($Chars as $cKey => $cVal) {
                $arRes[$cKey] = 0;
            }

            foreach ($arUniqueChars as $code => $count) {
                $symbol = chr($code);
                $ifIdentified = false;
                // определяем группу к которой относится
                foreach ($Chars as $cKey => $cVal) {
                    if (strpos($cVal, $symbol) !== false) {
                        $arRes[$cKey] += $count;
                        $ifIdentified = true;
                    } else {
                        $arRes[$cKey] += 0;
                    }

                }
                if ($ifIdentified) {
                    $arRes['Identified'] += $count;
                    $arRes['notIdentified'] -= $count;
                }
            }
        }
        return $arRes;
    } // end lmv_getStrCharType
    //---------------------------------------------------

    /** выдает диапазон дат в виде строки. даты указывать в виде строк в формате 'DD.MM.YYYY HH:MI:SS'
     *
     * @param unknown $beginDate
     * @param string $endDate
     * @return string
     */
    public static function strDateRange($beginDate, $endDate = '')
    {
        $arMonth = array(
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря',
        );
        $arBeginDate = ParseDateTime($beginDate, 'DD.MM.YYYY HH:MI:SS');
        if ($endDate != '') {
            $arEndDate = ParseDateTime($endDate, 'DD.MM.YYYY HH:MI:SS');
        }

        if ($beginDate != '' && $endDate != '') {
            if ($arBeginDate['MM'] == $arEndDate['MM']) {
                if ($arBeginDate['DD'] != $arEndDate['DD']) {
                    $str = $arBeginDate['DD'] . '-' . $arEndDate['DD'] . ' ' . mb_strtolower($arMonth[intval($arBeginDate['MM'])]);
                } else {
                    $str = $arEndDate['DD'] . ' ' . mb_strtolower($arMonth[intval($arBeginDate['MM'])]);
                }
            } else {
                $str = $arBeginDate['DD'] . ' ' . mb_strtolower($arMonth[intval($arBeginDate['MM'])]) . ' - ' . $arEndDate['DD'] . ' ' . strtolower($arMonth[intval($arEndDate['MM'])]);
            }
        } else {
            if ($beginDate == '' && $endDate != '') {
                $str = 'до ' . $arEndDate['DD'] . ' ' . mb_strtolower($arMonth[intval($arEndDate['MM'])]);
            }
            if ($beginDate != '' && $endDate == '') {
                $str = 'c ' . $arBeginDate['DD'] . ' ' . mb_strtolower($arMonth[intval($arBeginDate['MM'])]);
            }
        }
        return $str;
    } // end func

    /** является ли строка телефонным номером
     *
     * @param unknown $value
     * @return boolean
     */
    public static function checkPhone($value)
    {
        $arCharType = static::lmv_getStrCharType($value);
        if (isset($arCharType['number']) && $arCharType['number'] >= 7 && $arCharType['ruschar'] == 0 && $arCharType['engchar'] == 0) {
            return true;
        }

        return false;
    } // end checkPhone

    /** проверяет строку на отсутствие данных (лишние символы препинания и т.п. не учитываются)
     *
     * @param unknown $value
     * @return boolean
     */
    public static function checkEmptyString($value)
    {
        if ($value) {
            return (str_replace(array(' ', "\r", "\n", "\t", '.', ',', '"', "'", ';', '*', '/', '\\'), '', $value) ? false : true);
        } else {
            return false;
        }
    } // end checkEmptyString

    /** Возвращает код soundex. Русские символы переводятся в транслит.
     *
     * @param string $word
     * @return string
     */
    public static function soundex($word)
    {
        return soundex(\x51\functions\funcCodePage::translit($word));
    }

    /** Возвращает код metaphone. Русские символы переводятся в транслит.
     *
     * @param string $word
     * @return string
     */
    public static function metaphone($word)
    {
        return metaphone(\x51\functions\funcCodePage::translit($word));
    }

    /*public static function Strtolower($str) {
    if (function_exists('mb_strtolower')) {
    if (defined('CHARSET')) {
    return mb_strtolower($str, CHARSET);
    } else {
    return mb_strtolower($str);
    }
    } else {
    return strtolower($str);
    }
    }

    public static function Strpos($str, $needle, $offset = 0) {
    if (function_exists('mb_strpos')) {
    if (defined('CHARSET')) {
    return mb_strpos($str, $needle, $offset, CHARSET);
    } else {
    return mb_strpos($str, $needle, $offset);
    }
    } else {
    return strpos($str, $needle, $offset);
    }
    }*/

    public static function tanimotoWord($word1, $word2)
    {
        $a = mb_strlen($word1);
        $b = mb_strlen($word2);
        $c = 0;

        $count = mb_strlen($word1);
        for ($i = 0; $i < $count; $i++) {
            if (mb_strpos($word2, mb_substr($word1, $i, 1)) !== false) {
                $c++;
            }
        }

        $k = $a + $b - $c;
        if ($k > 0) {
            return $c / $k;
        } else {
            return 0;
        }
    } // tanimoto

} // end class
