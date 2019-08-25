<?php


// ###
namespace x51\functions;

// /###

# ����� � ��������� ����������� �����������
#
#
#
#
#

class funcImageCache
{

	public static $cache_style=1; /* 0 - ��������: ������� ����� ������������ � ��������� �����, � ����� � ��������� ��� 
	
	1-���������� �� ��������� �����������: ���� ������������ �������� ���� �������, �� ����� ������������ � ��������� ����� � ����� � ��������� ���. ���� ������������ �� � �������, �� ����� ������������ ������ � �������� �����
	*/
	
	public static $cache_name='cache/'; // ��� �������� � ������������� ���������� ����.������
	public static $strict_ratio=5; // ����������� �� ����������
	public static $error=0; // ������ ���
	
	// ���������� ������ � ������� ���������
	public static function getDirs()
	{
		global $cms_home_dir, $cms_dir, $cms_local_image_dirname;
		return array(
			'img_dir_public'=>(HOME_DIR.$_ENV['cms_dir']['image_local']), // ������� � ������������� ������������ �� ������� �����
			'img_dir_strict'=>($_ENV['cms_dir']['main'].$_ENV['cms_dir']['image']), // ������� � ������������� � ���������� CMS
			'cache_dir_public'=>(HOME_DIR.$_ENV['cms_dir']['image_local'].self::$cache_name), // ��� � ���������� �������� �����
			'cache_dir_strict'=>($_ENV['cms_dir']['main'].$_ENV['cms_dir']['image'].self::$cache_name) // ��� � ���������� CMS
		);
	}
	
	
	// ���������� ������� ������������ �����
	// ��������� $param: result=dir/full     find_in=dir/cache
	// ���� result=dir - ������ �������, ���� full - ������ ���� � ������ �����
	// ���� find_in=dir - ���� � ��������� (�� � ����), find_in=cache - ���� ������ � ���� (����� � ���)
	public static function findImageFile($filename, $ext, $param=array())
	{
		global $cms_home_dir, $cms_dir;
		
		if (!isset($param['result'])) { $param['result']='full'; }
		if (!isset($param['find_in'])) { $param['find_in']='dir'; }
		
		self::$error=0;
		
		$sta_ext=array('gif'=>0, 'jpg'=>0, 'png'=>0);
		$sta_dir=self::getDirs();
		if ($param['find_in']=='dir')
		{
			$sta_path=array(
				0=>$sta_dir['img_dir_public'], // ������� �����
				1=>$sta_dir['img_dir_strict'] // ������� � ���
			);
		}
		if ($param['find_in']=='cache')
		{
			$sta_path=array(
				0=>$sta_dir['cache_dir_public'], // ������� �����
				1=>$sta_dir['cache_dir_strict'] // ������� � ���
			);
		}
		
			
		// ������� ������������ ������� - �� ������ ������
		//$filename=str_replace(array('\\','/','.'),'',$i_filename);
		$fn=$filename; // ������ ���
		// ���������� ������� ����� � ����� ������ - ������ jpg, gif, png
		{
			//$ext=strtolower($i_ext);
			if (isset($sta_ext[$ext]))
			{
				$sta_ext[$ext]=1;
				$filename.='.'.$ext;
			}
			else { self::$error=2; } // �������� ����������
		}

		if (!self::$error) // ���������� ������� ���� �����
		{
			if (self::$cache_style==1)
			{
				if (isLogged())
				{
					// ���� ������� � �������� ����� � ����� � �������� �������� ������� ����������
					if (!file_exists($sta_path[0].$filename))
					{
						if (!file_exists($sta_path[1].$filename)) { self::$error=3; } // ���� �� ������
							else { $full_path=&$sta_path[1]; }
					}
					else { $full_path=&$sta_path[0]; }
				}
				else
				{
					// ���� ������ � �������� �����
					if (!file_exists($sta_path[0].$filename)) { self::$error=3; } // ���� �� ������
						else { $full_path=&$sta_path[0]; }
				}
			}
			else
			{
				// ���� ������� � �������� ����� � ����� � �������� �������� ������� ����������
				if (!file_exists($sta_path[0].$filename))
				{
					if (!file_exists($sta_path[1].$filename)) { self::$error=3; } // ���� �� ������
						else { $full_path=&$sta_path[1]; }
				}
				else { $full_path=&$sta_path[0]; }
			}
		}
		if (self::$error) { return false; }
		else {
			if ($param['result']=='full') { return $full_path.$filename; }
			if ($param['result']=='dir') { return $full_path; }
		}
	} // end function
	
	/* ���������� ������ ��� ����.����� � ������������ ������� � �������
	���������: w - ������   h - ������   rgb - ���� ���� �������
	����� �������� ������ ������ ��� ������ � ����� ����������� �������� ����� �������� �� ratio
	���� ����� � ����������� ����������� ��� � ���� � ���� � ������� �������� ����, �� ������� � ���� ������ �������
	
	
	*/
	public static function getImageFile($i_filename, $i_ext, $param=array())
	{
		global $cms_home_dir, $cms_dir, $cms_local_image_dirname;
		
		self::$error=0;
		
		if (!isset($param['w'])) { $param['w']=0; }
		if (!isset($param['h'])) { $param['h']=0; }
		if (!isset($param['rgb'])) { $param['rgb']=0xFFFFFF; }
		
		// ������� ������������ ������� - �� ������ ������
		$filename=str_replace(array('\\','/','.'),'',$i_filename);
		$fn=$filename; // ������ ���
		$ext=strtolower($i_ext);
		$full_path=self::findImageFile($filename, $ext, array('result'=>'dir'));
		
		$filename.='.'.$ext;
		
		if (!self::$error) // ����������� ������������� ��� ������
		{
			$h=(int) $param['h'];
			$w=(int) $param['w'];
			
			$image_info = getimagesize($full_path.$filename);
			//print_r($image_info);
			$image_ratio=$image_info[0]/$image_info[1];
			if ($h==0 && $w==0)
			{
				$w=$image_info[0];
				$h=$image_info[1];
			}
			if ($h==0 && $w!=0) { $h=round($w/$image_ratio); }
			if ($h!=0 && $w==0) { $w=round($h*$image_ratio); }
			
			// ����������� �� ����������
			$x_ratio = $w / $image_info[0];
			$y_ratio = $h / $image_info[1];
			$max_ratio=max($x_ratio, $y_ratio);
			//$strict_ratio=5;
			if ($max_ratio>self::$strict_ratio) // ����������� �� ����� ��� �� $strict_ratio
			{
				$x_ratio=$x_ratio-($max_ratio-$strict_ratio);
				$y_ratio=$y_ratio-($max_ratio-$strict_ratio);
				$w=round($image_info[0]*$x_ratio);
				$h=round($image_info[1]*$y_ratio);
			} // ����������� �� ����������
			
			$cache_filename=$fn.'_'.$w.'_'.$h.'_'.$param['rgb'].'.'.$ext; // ��� � ����
			//echo $w.'    '.$h;
			if (!file_exists($full_path.self::$cache_name.$cache_filename))
			{
				// ������� ������������ �����
				funcImageService::img_resize($full_path.$filename, $full_path.self::$cache_name.$cache_filename, $w, $h, $param['rgb']);
			}
			//echo $cache_filename.'<br>';
			return $full_path.self::$cache_name.$cache_filename;
		}
		if (self::$error)
		{
			return false;
		}
	} // end function getImageFile

} // end class
?>