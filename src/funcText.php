<?php

// ###
namespace x51\functions;

use \x51\functions\funcString;

// /###

/*
public static function stat_tok(&$input_str) // ФУНКЦИЯ ДЛЯ РАЗБИЕНИЯ ТЕКСТА НА СЛОВА
public static function stat_tok2(&$input_str, &$words) // ФУНКЦИЯ ДЛЯ РАЗБИЕНИЯ ТЕКСТА НА СЛОВА
public static function stat_del_stopword(array &$words) { // Удалим стоп-слова из результата stat_tok2
public static function stat_group(array $words) {  // Сгруппировать слова по звучанию
public static function textOptiWords(&$text) { // Разбивает текст на слова, исключает стоп-слова, группирует сходные слова

public static function bbCode($txt,$bCode1,$bCode2) // реализация bbCode
public static function prnTxt($text, $ifBB=false)// вывод текста на экран и обработка bbCode. процедура предназначена для вывода текста с доп.форматированием
public static function messageTransform($message, $direct, $max_size=1000)
public static function mytypograf($text)// очистка текста
public static function buildListH(&$htmlText, $makeAnchor=true) // строит список тегов H в тексте. проставляет якоря. результат в виде html списка

public static function metaYoutube(&$htmlText) { // /<#youtube\s+([^>]+)youtube#>/i
 */

class funcText
{
    protected static $delim = " .,:;()[]{}#%`-+=!?'_\"\n\r\t";

    // определяем bbCode
    protected static $bbCode1 = array(1 => '[b]', 2 => '[/b]', 3 => '[i]', 4 => '[/i]', 5 => '[c]', 6 => '[/c]', 7 => '[p]', 8 => '[/p]', 9 => "\r\n", 10 => "[list]", 11 => "[/list]", 12 => "[*]", 13 => '[br]');
    protected static $bbCode2 = array(1 => '<strong>', 2 => '</strong>', 3 => '<i>', 4 => '</i>', 5 => '<center>', 6 => '</center>', 7 => '<p>', 8 => '</p>', 9 => "<br>", 10 => "<ul>", 11 => "</ul>", 12 => "<li>", 13 => '<br>');
    // =-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-==-=-=-=

    public static $typo_mask = array(
        0 => array('search' => '/\040+/', 'replace' => ' '), // двойные пробелы
        5 => array('search' => '/([а-яa-z,])\040*\r\n\040*([а-яa-z,])/', 'replace' => '$1 $2'), // убирем висючие строки
        10 => array('search' => '/([>])\040*\r\n\040*([А-ЯA-Zа-яa-z])/', 'replace' => '$1$2'),
        //15=>array('search'=>'/( [а-яА-Я\w]{1,3})/', 'replace'=>'$1<nbsp/>'),
        20 => array('search' => '/\(\s/', 'replace' => '('), // между текстом и скобками пробелов нет
        25 => array('search' => '/\s\)/', 'replace' => ')'), // между текстом и скобками пробелов нет
        30 => array('search' => '/([.!,?а-яА-я])\s*(<\/)/', 'replace' => '$1$2'), // убираем пробелы между концом тега и текстом
        35 => array('search' => '/(\r\n\s*\r\n)+/', 'replace' => "\r\n"), // пустые строки
        40 => array('search' => '/\-\r\n(\w+)/s', 'replace' => '$1'), // перносы
        45 => array('search' => '/(\w+)\r\n\(/s', 'replace' => '$1 ('), //
        //35=>array('search'=>'/"\s+([а-яА-Яa-zA-Z\040,0-9]+)(\s+)"/','replace'=>' «$2$3»')
    );
    // =-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-==-=-=-=

    /** Разбиение текста на слова
     *
     * @param string $input_str
     */
    public static function stat_tok(&$input_str)
    {
        $words = array();
        static::stat_tok2($input_str, $words);
        return $words;
    } // end stat_tok

    /** Разбиение текста на слова
     *
     * @param string $input_str
     * @param array $words
     */
    public static function stat_tok2(&$input_str, array &$words)
    {
        $buff = &$input_str;
        $tok = mb_strtolower(trim(strtok($buff, static::$delim)));
        while ($tok) {
            if (strlen($tok) > 2) {
                // tok - слово текста
                if (isset($words[$tok])) {
                    $words[$tok]++;
                } else {
                    $words[$tok] = 1;
                }
            }
            $tok = mb_strtolower(strtok(static::$delim));
        }
    } // end stat_tok2

    /** Удалим стоп-слова из результата stat_tok2
     *
     * @param array $words
     *
     */
    public static function stat_del_stopword(array &$words)
    {
        foreach ($words as $word => $count) {
            if (\x51\functions\funcStopWords::isStop($word)) {
                unset($words[$word]);
            }
        }
    } // end stat_del_stopword

    /** Сгруппировать слова по звучанию
     *
     */
    public static function stat_group(array $words)
    {
        $gwords = array();
        $codewords = array();
        foreach ($words as $word => $count) {
            $c = metaphone(\x51\functions\funcCodePage::translit($word), 50);
            //echo $word.'='.$c."<br>\r\n";
            if (isset($codewords[$c])) {
                $gwords[$codewords[$c]] += $count;
            } else {
                $codewords[$c] = $word;
                $gwords[$codewords[$c]] = $count;
            }
        }
        return $gwords;
    } // end stat_group

    /** Разбивает текст на слова, исключает стоп-слова, группирует сходные слова
     *
     * @param unknown $text
     * @return struct
     * Результат в виде массива, где ключ - слово, значение - структура, содержащая следующие данные
     * count - кол-во в тексте
     * syn - слова которые признаны сходными с ключом
     */
    public static function textOptiWords(&$text)
    {
        $words = array();
        $codes = array();
        $tok = strtok($text, static::$delim);
        while ($tok !== false) {
            $tok = mb_strtolower($tok);
            if (mb_strlen($tok) > 2) {
                if (isset($words[$tok])) {
                    $words[$tok]['count']++;
                } else {
                    // проверка на стоп-слово
                    if (!\x51\functions\funcStopWords::isStop($tok)) {
                        $code = metaphone(\x51\functions\funcCodePage::translit($tok));
                        if (isset($codes[$code])) {
                            // сходные слова уже были
                            $words[$codes[$code]]['count']++;
                            if (!in_array($tok, $words[$codes[$code]]['syn'])) {
                                $words[$codes[$code]]['syn'][$tok] = $code;
                            }
                        } else {
                            $codes[$code] = $tok;
                            $words[$tok] = array(
                                'count' => 1,
                                'syn' => array(
                                    $tok => $code,
                                ),
                                'word' => $tok,
                                'code' => $code,
                            );
                        }
                    } // end stop word
                }
            }
            $tok = strtok(static::$delim);
        }
        //echo '<pre>';
        //print_r($words);
        //echo '</pre>';

        // вторая оптимизация - по коэффициенту
        $minK = 0.79;
        $maxLeva = 3;
        foreach ($words as $word => &$arData) {
            // ищем сходные по коэффициенту
            $arEq = array_filter($words, function ($val) use ($word, $minK, $maxLeva) {
                if ($val['word'] != $word) {
                    // если разница в длинне слов больше 2 то первым идет более длинное слово
                    $l1 = mb_strlen($val['word']);
                    $l2 = mb_strlen($word);
                    if (abs($l1 - $l2) < 4) { // по коэфф группируем если разница в длинне слов меньше 4
                        $leva = levenshtein($val['word'], $word);
                        if ($leva < $maxLeva) {
                            return true;
                        }
                    }
                }
                return false;
            });
            if ($arEq) { // совпадения по коэфф найдены
                foreach ($arEq as $k => $v) {
                    if (!isset($arData['syn'][$k])) {
                        $arData['syn'][$k] = $v['code'];
                    }
                    if (!empty($v['syn'])) {
                        foreach ($v['syn'] as $i => $s) {
                            if (!isset($arData['syn'][$i])) {
                                $arData['syn'][$i] = $s;
                            }
                        } // enв foreach
                    }
                    $arData['count'] += $v['count'];
                    unset($words[$k]);
                } // end foreach
            } // end arEq
        }
        return $words;
    } // end textOptiWords

    /** реализация bbCode
     *
     * @param unknown $txt
     * @param unknown $bCode1
     * @param unknown $bCode2
     * @return mixed
     */
    public static function bbCode($txt, $bCode1, $bCode2)
    {
        return str_ireplace($bCode1, $bCode2, $txt);
    }

    public static function prnTxt($text, $ifBB = false) // вывод текста на экран и обработка bbCode. процедура предназначена для вывода текста с доп.форматированием

    {
        if ($ifBB) {
            return str_ireplace(self::$bbCode1, self::$bbCode2, $text);
        } else {
            return $text;
        }
    }

    public static function messageTransform($message, $direct, $max_size = 1000)
    {
        /*
        0 - преобразование текста с BB-кодами в ХТМЛ
        1 - преобразование текста ХТМЛ в BB-кодами
        2 - удаление ХТМЛ и контроль объема
         */
        //$cut_len=(mb_strlen($message)>$max_size) ? $max_size : mb_strlen($message);
        $mess = false;
        // создадим массивы для замен
        switch ($direct) {
            case -1:{
                    return $message;
                }
            case 0:{
                    // в чистый ХТМЛ

                    // очистка от ХТМЛ
                    // преобразование ВВ в ХТМЛ
                    $mbuff = strip_tags($message);
                    if (mb_strlen($mbuff) > $max_size) {
                        $mess = str_ireplace(self::$bbCode1, self::$bbCode2, mb_substr($mbuff, 0, $max_size));
                    } else {
                        $mess = str_ireplace(self::$bbCode1, self::$bbCode2, $mbuff);
                    }
                    break;
                }
            case 1:{
                    // в ВВ коды

                    // преобразование в ВВ
                    // очистка от ХТМЛ
                    $mess = strip_tags(str_ireplace(self::$bbCode2, self::$bbCode1, $message));
                    if (mb_strlen($mess) > $max_size) {
                        $mess = mb_substr($mess, 0, $max_size);
                    }
                    break;
                }
            case 2:{
                    // удаление ХТМЛ и контроль объема
                    $mess = strip_tags($message);
                    if (mb_strlen($mess) > $max_size) {
                        $mess = mb_substr($mess, 0, $max_size);
                    }
                }

        }
        return $mess;
    }

    /** Очистка текста
     *
     * @param string $text
     * @return string
     */
    public static function mytypograf($text)
    {
        foreach (self::$typo_mask as $key1 => $val1) {
            // обрабатываем текст масками
            $text = preg_replace($val1['search'], $val1['replace'], $text);
        }
        return $text;
    }

    // **********************************************************************************

    /** строит список тегов H в тексте. проставляет якоря. результат в виде html списка
     *
     * @param unknown $htmlText
     * @param string $makeAnchor
     * @return string
     */
    public static function buildListH(&$htmlText, $makeAnchor = true)
    {
        $arMatches = array();
        preg_match_all('/<h(\d)+[\sa-z="]*>([^<]+)<\/h\d+>/is', $htmlText, $arMatches);
        $txtHMenu = '';
        if (isset($arMatches[1]) && $arMatches[1]) {
            // анкоры
            $arAnchors = (\x51\functions\funcCodePage::translit(
                str_replace(
                    array('&', ';', '.', ',', ';', ':', '!', '?', '/', '\\', '(', ')', '%', '+', ' ', '@', '#', '~', "\r", "\n", "\t"),
                    array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '_', '', '', '', '', '', ''),
                    $arMatches[2]
                ))
            );
            // проверка на повтор анкоров
            $arACount = array_count_values($arAnchors);
            $arACounter = array();
            array_walk($arAnchors, function (&$val, $key) use ($arACount, $arACounter) {
                if ($arACount[$val] > 1) {
                    if (isset($arACounter[$val])) {
                        $arACounter[$val]++;
                        $val .= '_' . $arACounter[$val];
                    } else {
                        $arACounter[$val] = 1;
                    }
                }
            });
            unset($arACount, $arACounter);
            // ***

            $txtHMenu = '<ul>';
            $prevLevel = 0;
            foreach ($arMatches[1] as $key => $level) {
                $arAnchors[$key] = mb_strtolower($arAnchors[$key]);
                if ($key > 0) {
                    if ($prevLevel == $level) {
                        $txtHMenu .= '</li>';
                    }

                    if ($prevLevel > $level) {
                        $txtHMenu .= str_repeat('</li></ul>', $prevLevel - $level);
                    }

                    if ($prevLevel < $level) {
                        $txtHMenu .= '<ul>';
                    }

                }
                $txtHMenu .= '<li><a href="#' . $arAnchors[$key] . '">' . $arMatches[2][$key] . '</a>';
                $prevLevel = $level;
            }
            $txtHMenu .= '</li></ul>';

            // проставим якоря в тексте
            if ($makeAnchor) {
                $pos = 0;
                foreach ($arMatches[0] as $key => $val) {
                    $fp = strpos($htmlText, $val, $pos);
                    if ($fp !== false) {
                        $anc = '<a name="' . $arAnchors[$key] . '" id="' . $arAnchors[$key] . '"></a>';
                        if ($fp == 0) {
                            $htmlText = $anc . $htmlText;
                        } else {
                            $htmlText = substr($htmlText, 0, $fp) . $anc . substr($htmlText, $fp);
                        }
                        $pos = $fp + strlen($anc) + strlen($val);
                    }
                }
            } // end makeAnchor
        }
        return $txtHMenu;
    } // end buildListH

    /** выбирает из текста всеми метакоды
     * <#$metaName ...... $metaName#>
     *
     * @param unknown $htmlText
     * @param unknown $metaName
     */
    protected static function metaGetAllCode(&$htmlText, $metaName)
    {
        $arMatches = array();
        $htmlText = str_replace(array('&lt;#' . $metaName . ' ', ' ' . $metaName . '#&gt;'), array('<#' . $metaName . ' ', ' ' . $metaName . '#>'), $htmlText);
        preg_match_all('/<#' . $metaName . '\s+([^>]+)' . $metaName . '#>/i', $htmlText, $arMatches); // 0 - полные вхождения 1 подмаска
        return $arMatches;
    } // end metaGetAllCode

    /** из содержимого метакода стирает свойства html с поределенным именем
     * например из width="854" height="510" src="//www.youtube.com/embed/g_YWozrw53c" frameborder="0" надо удалить src
     *
     * @param unknown $metaBody
     * @param unknown $propName
     */
    protected static function metaClearHtmlProp(&$metaBody, $propName)
    {
        if (!is_array($propName)) {
            $p = array($propName);
        } else {
            $p = &$propName;
        }
        $mask = array();
        foreach ($p as $pName) {
            $mask[] = '/\s+' . $pName . '\s*=\s*["\']{1}[^"\']+["\']{1}\s+/i';
        }
        $metaBody = preg_replace($mask, ' ', $metaBody);
    } // end metaClearHtmlProp

    /** выдает мета свойства. возвращает массив в котором ключ - имя, а значение - значение свойства. мета свойства вырезаются. в свойстве не применим #
     * мета свойства задаются в простом виде
     *
     * @param array $arProp
     * @param unknown $metaBody
     * @return string[]
     */
    protected static function metaGetProp(array $arProp, &$metaBody)
    {
        $result = array();
        foreach ($arProp as $propName) {
            $strEmbedPrefix = '#' . $propName . ' ';
            $sizeEm = strlen($strEmbedPrefix);

            $pEmbed = strpos($metaBody, $strEmbedPrefix);
            //while ($pEmbed=strpos($metaBody, $strEmbedPrefix) !== false);
            {
                $pCloser = strpos($metaBody, '#', $pEmbed + $sizeEm);
                if ($pCloser !== false) {
                    $result[$propName] = substr($metaBody, $pEmbed + $sizeEm, $pCloser - $pEmbed - $sizeEm);
                    $metaBody = substr($metaBody, 0, $pEmbed) . substr($metaBody, $pCloser + 1);
                }
            }
        }
        return $result;
    } // end metaGetProp

    /** ищет, возвращает и вырезает переключатели типа #switch1#
     * имена переключателей передаются в простом виде - массив array('switch1', 'switch2', ...)
     *
     * @param array $arSwitch
     * @param unknown $metaBody
     * @return boolean[]
     */
    protected static function metaGetSwitchers(array $arSwitch, &$metaBody)
    {
        $result = array();
        foreach ($arSwitch as &$switch) {
            $switch2 = '#' . $switch . '#';
            if (strpos($metaBody, $switch2) !== false) {
                $result[$switch] = true;
            }
            $switch = $switch2;
        }
        $metaBody = str_replace($arSwitch, '', $metaBody);
        return $result;
    } // end metaGetSwitchers

    /** заменяет метакоды youtube
     * <#youtube width="854" height="510" #embed g_YWozrw53c# frameborder="0" allowfullscreen youtube#>
     * #left# #right# #center#
     * #cleartop# #clearbottom#
     * <iframe width="854" height="510" src="//www.youtube.com/embed/g_YWozrw53c" frameborder="0" allowfullscreen></iframe>
     *
     * @param unknown $htmlText
     */
    public static function metaYoutube(&$htmlText)
    { // /<#youtube\s+([^>]+)youtube#>/i
        $arMatches = static::metaGetAllCode($htmlText, 'youtube');
        {
            if (isset($arMatches[1]) && $arMatches[1]) {
                foreach ($arMatches[1] as $key => &$val) {
                    $embedCode = false;
                    // сотрем src
                    static::metaClearHtmlProp($val, 'src');
                    $arCodes = static::metaGetProp(array('embed'), $val);
                    if (isset($arCodes['embed'])) {
                        $embedCode = $arCodes['embed'];
                    } else {
                        $embedCode = false;
                    }

                    if ($embedCode == false) {
                        $val = '';
                    } else {
                        // генерируем стили
                        // запросим свитчеры
                        $arSwitches = static::metaGetSwitchers(
                            array(
                                'left',
                                'right',
                                'center',
                                'padding5',
                                'padding10',
                                'cleartop',
                                'clearbottom',
                            ),
                            $val
                        );
                        $out = '';
                        $outBottom = '';
                        $style = ' style="display:block;';
                        $arStyles = array(
                            'left' => 'float:left;',
                            'right' => 'float:right;',
                            'center' => 'margin-right:auto; margin-left: auto;',
                            'padding5' => 'padding:0.5em',
                            'padding10' => 'padding:1em;',
                        );
                        foreach ($arSwitches as $switch => $bool) {
                            if (isset($arStyles[$switch])) {
                                $style .= $arStyles[$switch];
                            }

                        }
                        $style .= '"';
                        if (isset($arSwitches['cleartop']) !== false) {
                            $out .= '<div style="clear:both"></div>';
                        }
                        if (isset($arSwitches['clearbottom']) !== false) {
                            $outBottom .= '<div style="clear:both"></div>';
                        }
                        $out .= '<div' . $style . '><iframe src="//www.youtube.com/embed/' . $embedCode . '" ' . htmlspecialchars_decode($val) . '></iframe></div>' . $outBottom;
                        $val = $out;
                    }
                } // end foreach
                // замена кодов на код
                $htmlText = str_replace($arMatches[0], $arMatches[1], $htmlText);
            }
        }
    } // end metaYoutube

    public static function firstSentences($text, $n = 3, $start = 0)
    {
        if (!$text || $n <= 0 || $start > strlen($text)) {
            return '';
        }
        $delim = array('.', '!', '?', "\n");
        $pos = $start;
        $counterSent = 0;
        $theEnd = false;

        while (!$theEnd) {
            $minPos = -1;
            $minPosI = 0;
            foreach ($delim as $i => $d) {
                $p = strpos($text, $d, $pos);
                if ($p !== false && ($p < $minPos || $minPos == -1)) {
                    $minPos = $p;
                    $minPosI = $i;
                }
            }
            if ($minPos == -1) { // весь текст одно предложение
                $pos = strlen($text);
                $theEnd = true;
            } else {
                $lenSent = $minPos - $pos; // длинна предложения
                if ($lenSent > 1 && !\x51\functions\funcString::checkEmptyString(substr($text, $pos, $minPos - $pos))) {
                    $counterSent++;
                }
                $pos = $minPos + 1;
                if ($counterSent == $n) {
                    //$pos=$minPos;
                    $theEnd = true;
                }
            }
        } // end while
        return substr($text, 0, $pos);
    } // end firstSentences

} // end class funcText
