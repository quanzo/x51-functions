<?php
namespace x51\functions;

// ��� ������ � ��������� ����������

class funcMoney{
	// ���������� �������� ������� �� 2 ������ ����� �������
	public static function roundMoney($m) {
		return round($m, 2);
	}
	
	public static function explodeMoney($m)	{
		$p=self::roundMoney($m);
		$rub=floor($p);
		$kop=round((($p-$rub)*100),0);
		//echo $m.'=='.$p.' == '.$rub.' == '.$kop.' -- ';
		$result=array(
			0=>$rub,
			1=>$kop,
			'rub'=>$rub,
			'kop'=>$kop
		);
		return $result;
	}
} // end class