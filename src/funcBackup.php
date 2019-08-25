<?php


// ###
namespace x51\functions;

use x51\classes\database\funcDB;
// /###

/* ������� ���������� ����������� ������ �������

������� ��� ��������� ����� ������ ����� ������� ������ � �������� �� ������� �������� ������ - ���� ������� ������ ���� ������� � ����� ��� ����� �� ��� � � �������� �������
������ �������������� �������������� ���� backup_date       ��� ���� - ����/�����      ���� ���������� ��� �������� ���� �������� �������. backup_unique - ���������� ����� ���������� INT/autoincrement

*/

// ��������� ����������� ������ ��� ��������������
class funcBackup
{

// ������� ��������� ���� ������ �� tablename � �������� ����� keyfield_name �������� �������� keyfield_val � ������� ��������� ����� backuptable
public static function backup_ns_1line($tablename, $keyfield_name, $keyfield_val, $backuptable)
{
	// �������� ������ ������
	$ssql='SELECT * FROM '.$tablename.' WHERE '.$keyfield_name.'="'.addslashes($keyfield_val).'"';
	$i_res=funcDB::dbQuery($ssql);
	if (mysql_num_rows($i_res)>0)
	{
		$o_r=mysql_fetch_assoc($i_res); // �������� ������ ������
		$b_field='backup_date="'.date('Y-m-d H:i:s').'"';
		
		$f1='backup_date';
		$f2='"'.date('Y-m-d H:i:s').'"';
		foreach ($o_r as $fname => $fval)
		{
			$f1.=','.$fname;
			$f2.=',"'.addslashes($fval).'"';
		}
		$ssql='INSERT INTO '.$backuptable.' ('.$f1.') VALUES ('.$f2.')';
		funcDB::dbQuery($ssql);
		if (funcDB::affected()>0) { return true; }
		else { return false; }
	}
	else
	{
		return false;
	}
} // end public static function backup_ns_1line

// �������� ���-�� ��������� � ������� �������. 
public static function backup_ns_controlcount($backuptable, $keyfield_name, $size)
{
	$ssql='SELECT '.$keyfield_name.',COUNT('.$keyfield_name.') FROM '.$backuptable.' GROUP BY '.$keyfield_name;
	$i_res=funcDB::dbQuery($ssql);
	$curr_size=mysql_num_rows($i_res);
	for ($i=0; $i<$curr_size; $i++)
	{
		$vcount=mysql_result($i_res,$i,1);
		if ($vkount>$size)
		{
			// ������� ������
			$vkey=mysql_result($i_res,$i,0);
			$ds=$vkount-$size;
			$ssql='SELECT backup_unique FROM '.$backuptable.' WHERE '.$keyfield_name.'="'.$vkey.'"'.' ORDER backup_unique ASC';
			$i_res2=funcDB::dbQuery($ssql);
		
			$md=mysql_result($i_res, $ds-1, 0);
			$ssql='DELETE FROM '.$backuptable.' WHERE backup_unique<="'.$md.'" AND '.$keyfield_name.'="'.$vkey.'"';
			funcDB::dbQuery($ssql);
		}
	}
} // end public static function backup_ns_controlcount

// ������������� ����������� ������������ ��� ������������� �������� ������� ��
public static function backup_ns_restore_one($tablename, $keyfield_name, $keyfield_val, $backuptable, $backupdesc='')
// backupdesc ������ ���������������� � ���������� ����� ��������� ��������� ��������������
{
	$ssql='SELECT COUNT(*) FROM '.$backuptable.' WHERE '.$keyfield_name.'="'.$keyfield_val.'"';
	//echo $ssql;
	$i_res=funcDB::dbQuery($ssql);

	if (mysql_result($i_res, 0, 0)>0)
	{
		$ssql='SELECT MAX(backup_unique) FROM '.$backuptable.' WHERE '.$keyfield_name.'="'.$keyfield_val.'"'; // �������� ���� ���������� ����������
		//echo $ssql;
		$i_res=funcDB::dbQuery($ssql);
		$md=mysql_result($i_res,0,0); // ��������� ����
		$ssql='SELECT * FROM '.$backuptable.' WHERE backup_unique="'.$md.'" AND '.$keyfield_name.'="'.$keyfield_val.'"'; // ������ ���������� ����������
		//echo $ssql;
		$i_res=funcDB::dbQuery($ssql);
		$row1=mysql_fetch_assoc($i_res);
		// ��������� ��� ������� - update ��� insert
		$ssql='SELECT COUNT(*) FROM '.$tablename.' WHERE '.$keyfield_name.'="'.addslashes($row1[$keyfield_name]).'"';
		//echo $ssql;
		$i_res=funcDB::dbQuery($ssql);
		if (mysql_result($i_res,0,0)>0) { $z_type=1; } // update
		else { $z_type=0; } // insert
		// ���������� ���� � ���������� ������
		$p1='';
		$p2='';
		unset($row1['backup_date']); // ������� ��������� ����
		unset($row1['backup_unique']); // ������� ��������� ����
		$rkeys=array_keys($row1);
		$rkeys_count=sizeof($rkeys);

		for ($i=0; $i<$rkeys_count; $i++)
		{
			$kn=$rkeys[$i];
			if ($z_type==1) // update
			{ $p1.=$kn.'="'.addslashes($row1[$kn]).'"'; }
			if ($z_type==0) // insert
			{
				$p1.=$kn;
				$p2.='"'.addslashes($row1[$kn]).'"';
			}
			if ($i<($rkeys_count-1))
			{
				$p1.=',';
				$p2.=',';
			}
		}
		// ��������� �������
		if ($z_type==1) // update
		{
			$ssql='UPDATE '.$tablename.' SET '.$p1.' WHERE '.$keyfield_name.'="'.addslashes($row1[$keyfield_name]).'"';
		}
		if ($z_type==0) // insert
		{
			$ssql='INSERT INTO '.$tablename.' ('.$p1.') VALUES ('.$p2.')';
		}
		//echo $ssql;
		// ��������� ������� ��������������
		funcDB::dbQuery($ssql);
		//if (funcDB::affected()>0) // ���� �������������� ���������
		{
			// ������� �������������� ������
			$ssql='DELETE FROM '.$backuptable.' WHERE backup_unique="'.$md.'" AND '.$keyfield_name.'="'.$keyfield_val.'"';
			funcDB::dbQuery($ssql);
		}
		//echo $ssql;
	}
} // end public static function backup_ns_restore

// ���������� ������ �������� ������ �� ������� ����������� ����������
public static function backup_ns_list_key($keyfield_name, $backuptable)
{
	
	$ssql='SELECT DISTINCT('.$keyfield_name.') FROM '.$backuptable.' ORDER BY backup_unique DESC';
	$i_res=funcDB::dbQuery($ssql);
	$curr_size=mysql_num_rows($i_res);
	if ($curr_size>0)
	{
		$result=array();
		for ($i=0; $i<$curr_size; $i++)
		{
			$result[]=mysql_result($i_res, 0,0);
		}
		return $result;
	}
	return array();
} // end

public static function backup_ns_list_val($keyfield_name, $keyfield_val, $backuptable, $fout_list) // ���������� �������� ����� ��� ������ ��������������
// $fout_list - ������ ����� ������� ����� ����������, $keyfield_name - ��� ��������� ���� ������� (���������� ID �������� uitxt), $keyfield_val - �������� ��������� ���� 
{
	
	
	if (array_search('backup_date', $fout_list)===false)
	{
		$fout_list[]='backup_date';
	}
	if (array_search('backup_unique', $fout_list)===false)
	{
		$fout_list[]='backup_unique';
	}
	$fol=implode($fout_list, ',');
	$ssql='SELECT '.$fol.' FROM '.$backuptable.' WHERE '.$keyfield_name.'="'.$keyfield_val.'" ORDER BY backup_unique DESC';
	//echo $ssql;
	$curr_size=0;
	$i_res=@funcDB::dbQuery($ssql);
	$curr_size=@mysql_num_rows($i_res);
	//echo '>>>'.$curr_size;
	if ($curr_size>0)
	{
		$result=array();
		for ($i=0; $i<$curr_size; $i++)
		{
			$r1=mysql_fetch_assoc($i_res);
			$result[$r1['backup_date']]=$r1;
		}
		return $result;
	}
	else { return array(); }
}

public static function backup_ns_create_bak_table($tablename) // ������� ������� ��� ��������� �����
// tablename  -   ��� ������� ������� ��������
{
	
	
	$ssql='DESCRIBE '.$tablename; // ������� �� ��������� ������ �� �������
	//echo '<br>������� �� ��������� ������ � ������� : '.$ssql."<br>\r\n";
	$i_res=funcDB::dbQuery($ssql);
	$i_count=mysql_num_rows($i_res);
	
	if ($i_count>0)
	{
		// �������� ������ ������ ��� �������
		$col='backup_date datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\', backup_unique int NOT NULL AUTO_INCREMENT, ';
		for ($i=0; $i<$i_count; $i++)
		{
			$r1=mysql_fetch_row($i_res);
			//print_r($r1);
			// txtdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
			$col.=$r1[0].' '.$r1[1];
			if (strtolower($r1[2])!='yes') { $col.=' NOT NULL'; }
			//$col.=" DEFAULT '".$r1[4]."'"; � �������� ��� ���� ������� �������� �� ��������� �� ����� �.�. ��� ����������� ������ ��� ���� ���������� 1 � 1
			if ($i<($i_count-1)) { $col.=" ,\r\n"; }
		} // end for
		// ������� ������
		$new_tb=$tablename.'_backup';
		$ssql='CREATE TABLE IF NOT EXISTS '.$new_tb.' ( '.$col.', PRIMARY KEY (backup_unique))';
		//echo '<br>������� �� �������� ������� ������� ������ : '.$ssql."\r\n<br>";
		// ��������� ������
		funcDB::dbQuery($ssql);
		if (funcDB::affected()) { return $new_tb; }
		else { return false; echo '<br>������� �� ���������<br>';}
	}
	else { return false; }
} // end backup_ns_create_bak_table

} // end class

?>