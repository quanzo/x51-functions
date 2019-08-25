<?php

// ###
namespace x51\functions;

/* Функции для фронт-энда

*/

class funcFrontEnd {
	
	public static function normalizeCssUnitSize($strParam, $defUnit='px')
	{
		$arP=array('px', 'em', '%');
		$strParam=trim($strParam);
		$defEd=trim($defEd);
		$size=sizeof($arP);
		$plen=strlen($strParam);
		$ifFind=false;
		for ($i=0; $i<$size; $i++)
		{
			$p=strpos($strParam, $arP[$i]);
			if ($p!==false && $p==($plen-strlen($arP[$i])))
			{
				$ifFind=true;
				break;
			}
		}
		//var_dump($ifFind);
		if ($ifFind) return $strParam;
		return $strParam.$defUnit;
	} // getCssParam
	
	
	
} // end class