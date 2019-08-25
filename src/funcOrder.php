<?php


// ###
namespace x51\functions;

// /###

/*function order_get() // ������� ��� �������� ������ �����. �� ������ ������
public static function order_cleanArray($arr) // ������ ������ ����������� order_get �� ������ �����
function order_html($table) // ������� ��� ������������ html �� ��������� cart_order
public static function order_txt($table) // ��������� ����� �� ��������� �������
*/

class funcOrder
{

public static function order_get($param=array()) // ������� ��� �������� ������ �����. �� ������ ������
	/* ����� �������� � ���� html. ����� ���� ��������� ���������.
	� ������� ���� ��� ������ ������ ����� ���������� ��������:
	_desc - �������� ���������� �������� ������� ������
	_attr - �������������� ��������
	��� ������ ���������� ������ ����� POST ���������� � ������. ���� ������ desc - �� ��� �������� ������ �������, ����� ��� �������� ���� � �����
	�� ������ �������� ������: ����� - �������� �����, ���������� - �������� �����
	
	���������:
	only_desc - ������� ������ ����� ������� ����_desc
	*/
	{
		if (!isset($param['only_desc'])) $param['only_desc']=false; // ������� ������ ����� ������� ����_desc
		
		$pk=array_keys($_POST);
		$ps=sizeof($pk);
		$ignore=array('_attr', '_desc');
		$out=array();
		for ($i=0; $i<$ps; $i++) // ���������� �����
		{
			$is_ignore=false;
			for ($i2=0; $i2<sizeof($ignore); $i2++)
			{
				$f=strpos($pk[$i],$ignore[$i2]);
				if ($f!==false) // ������ ������������� �������
				{
					// ��������� ��� ����� � �����
					if (($f+strlen($ignore[$i2]))==strlen($pk[$i])) { $is_ignore=true; }
				}
			}
			if (!$is_ignore)
			{
				// ������
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

public static function order_cleanArray($arr) // ������ ������ ����������� order_get �� ������ �����
{
	$key=array_keys($arr);
	$k_count=sizeof($key);
	for ($i=0; $i<$k_count; $i++) if ($arr[$key[$i]]=='') unset($arr[$key[$i]]);
	return $arr;
}
	

} // end class funcOrder