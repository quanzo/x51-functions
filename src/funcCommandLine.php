<?php


// ###
namespace x51\functions;

// /###


// ������� ��������� ������� ����������
/*
public static function getNpost() // ������ $_GET � $_POST ����������� �� ������
function protectGET($fieldnamearray) // ������� ��� ������ �� �������������� ���������� /��������� ������/

*/

class funcCommandLine
{
	
	/** �������� ������� ������ $_GET � $_POST � ��������� ��� ������� ������� ���������� ������������ �� ������
	 * 
	 */
	public static function getNpost() {
		$not_post=array_diff_key($_GET, $_POST);
		$not_get=array_diff_key($_POST, $_GET);
		foreach ($not_post as $key => $val) $_POST[$key]=$val;
		foreach ($not_get as $key => $val) $_GET[$key]=$val;
	}
	
	/** ������� ��� ������ �� �������������� ���������� /��������� ������/
	 * 
	 * @param array $fieldnamearray - ������ ����������� �����
	 */
	public static function protectGET(array $fieldnamearray) {
		// ������� ������� �������������� ��������� �� ������� $_GET
		$getfields=array_keys($_GET); // ��� ���������
		$lishnee=array_diff($getfields, $fieldnamearray); // ���������� ������ �����
		foreach ($lishnee as $val) {
			unset($_GET[$val]);
			unset($_REQUEST[$val]);
		}
		$count_unset=sizeof($lishnee);
		unset($getfields);
		
		// ��������� ��������� � $_GET �� �������� - ������ �� ������������ ����������
			ksort($_GET);
			reset($_GET);
		// ---
		
		if ($count_unset>0) {
			// ������� ������ $_SERVER['REQUEST_URI']
			// ������ ������ ?
			$buff1=$_SERVER['REQUEST_URI'];
			$bg1=strpos($buff1,'?');
			if ($bg1!==false) {
				$res_name=substr($buff1,0,$bg1+1); // �������� ������ � ��������
				$get_count=sizeof($_GET);
				$i=0;
				foreach ($_GET as $param_name => $param_value) {
					$i++;
					if (is_array($param_value)) {
						$s=sizeof($param_value);
						$c=0;
						foreach ($param_value as $i => $x) {
							if ($c>0) {
								$res_name.='&';
							}
							$res_name.=$param_name.'[]='.rawurlencode($x);
						}
					} else {
						$res_name.=$param_name.'='.rawurlencode($param_value);
					}
					if ($i<$get_count) {
						$res_name.='&';
					}
				}
			} else {
				// GET ��� ������ �� ������
			}
		}
	} // end function protectGET

} // end class