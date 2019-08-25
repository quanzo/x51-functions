<?php


// ###
namespace x51\functions;

// /###

// ������� ��� ������ � �������� ��������
/*
public static function fileOut($filename) // ������ ���� � �������
public static function deleteDirectory($dir) // ������� �������� ���������� �� ����� ���������������
public static function scandir($dir, $param=array()) // ����������� ������ scandir
public static function checkFileFromDirs($filename, $arDirs) // ���������� � ����� �������� �� ������ ���� ����. ���������� ������ ���������� �������
public static function checkFileFromDirsAll($filename, $arDirs) // ���������� ������� ����� � ��������� ������. ���������� ��� ���������� � ������� ��������� ����
public static function checkDirList($arDirs) // ��������� ������ � ������� ���������� �� �� �������
public static function checkFilesList($dir, array $arFiles) // ��������� ������ � ������� ������ �� �� ������� � ����������
public static function getFileExtWithDot($e_name) // ���������� ����� � ������
public static function getFileExt($e_name) // ���������� ����� ��� �����
public static function getFileName($e_name) // ���������� ������ ��� �����
public static function getMimeFromExt($ext) // ���������� mime �� ����������
public static function normalizePath($path, $relDir=false) // ���������� ���� � ����� � ������� �����
public static function scansubdir($dir, $param=array()) // ��������� �����������. ��������� ������ � ���� �������, � ������� ������������ ������ ��������� � ������
public static function normalizeDir($dir) // ����������� ���� � ��������� - ��������� ������ /
public static function backSearch($startDir, $name, $endDir='/', $mode=1) // �������� ����� ����� �\��� ����������. ���������� ������ ���������� ��������
public function cssUrlCorrection($str, $filename) { // ����������� ������������� url � ���������� ������������ $filename
public function relativePath($fullPath, $relDir) {
*/
class funcFileSystem
{
	public static $mimeType=array(
		'.doc'=>'application/msword',
		'.pdf'=>'application/pdf',
		'.xls'=>'application/vnd.ms-excel',
		'.ppt'=>'application/vnd.ms-powerpoint',
		'.gz'=>'application/x-gzip',
		'.tgz'=>'application/x-gzip',
		'.swf'=>'application/x-shockwave-flash',
		'.xsl'=>'application/xml',
		'.xml'=>'application/xml',
		'.xslt'=>'application/xslt+xml',
		'.zip'=>'application/zip',
		'.mid'=>'audio/midi',
		'.midi'=>'audio/midi',
		'.mp4'=>'audio/mp4',
		'.wav'=>'audio/x-wav',
		'.bmp'=>'image/bmp',
		'.jpg'=>'image/jpeg',
		'.jpeg'=>'image/jpeg',
		'.jpe'=>'image/jpeg',
		'.png'=>'image/png',
		'.svg'=>'image/svg+xml',
		'.tiff'=>'image/tiff',
		'.tif'=>'image/tiff',
		'.djvu'=>'image/vnd.djvu',
		'.djv'=>'image/vnd.djvu',
		'.ico'=>'image/x-icon',
		'.mpeg'=>'video/mpeg',
		'.mpe'=>'video/mpeg',
		'.mpg'=>'video/mpeg',
		'.avi'=>'video/x-msvideo',
		'.rtf'=>'text/rtf',
		'.txt'=>'text/plain',
		'.css'=>'text/css',
		'.scss'=>'text/css',
		'.html'=>'text/html',
		'.htm'=>'text/html',
		'.js'=>'application/javascript',
	);
	
	public static function fileOut($filename) // ������ ���� � �������
	// filename - ���� � �����
	{
		$filename=static::normalizePath($filename);
		if (file_exists($filename))
		{
			$ext=static::getFileExtWithDot($filename);
			if (!empty(static::$mimeType[$ext]))
			{
				$fp=fopen($filename, "rb");
				header('Content-type: '.static::$mimeType[$ext]);
				header('Content-Length: '.filesize($filename));
				fpassthru($fp);
				fclose($fp);
				return true;
			}
		}
		return false;
	} // end fileOut
	
	public static function deleteDirectory($dir) // ������� �������� ���������� �� ����� ���������������
	{
		/*if ($objs = glob($dir."/*")) {
			foreach($objs as $obj) {
				is_dir($obj) ? removeDir($obj) : unlink($obj);
			}
		}
		rmdir($dir);*/
		
		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
			if (!static::deleteDirectory($dir . "/" . $item)) {
				chmod($dir . "/" . $item, 0777);
				if (!static::deleteDirectory($dir . "/" . $item)) return false;
			};
		}
		return rmdir($dir);
	} // end removeDir
// ========================	
	public static function scandir($dir, $param=array()) // ����������� ������ scandir
	/* ��������
	
	sort		asc desc
		dir
		name
		date
		size
		������� ���������� ������
	
	show
		dir true/false
		file true/false
	func_check
	return_full - �����. ������ ������ �� ����� - name (���), dir (������� ����������), date (���� �����), size (������)
	return_fullPath - ��� ����� ��������� �� ���� ����� � ����. �� ��������� false
	*/
	{
		if ($dir!='')
		{
			// ������������� ����������
				if (!isset($param['show']['dir'])) $param['show']['dir']=true;
				if (!isset($param['show']['file'])) $param['show']['file']=true;
				if (!isset($param['func_check'])) $param['func_check']=false;
				if (!isset($param['return_full'])) $param['return_full']=false;
				if (!isset($param['return_fullPath'])) $param['return_fullPath']=false;

			
			if (substr($dir,strlen($dir)-1,1)!='/') $dir.='/';
			
			$list=scandir($dir);
			// ��������� ����������� ������
			$list_size=sizeof($list);
			$multilist=array();
			for ($i=0; $i<$list_size; $i++)
			{
				if ($list[$i]!='.' && $list[$i]!='..')
				{
					$fname=$dir.$list[$i];
					$is_dir=is_dir($fname);
					$is_view=false;
					if ($param['show']['dir']!=false && $is_dir) $is_view=true;
					if ($param['show']['file']!=false && !$is_dir) $is_view=true;
					if ($is_view)
					{
						$oneline=array('name'=>$list[$i], 'dir'=>$is_dir, 'date'=>filemtime($fname), 'size'=>filesize($fname));
						if ($param['func_check']!=false) $is_view=$param['func_check']($oneline);
						if ($is_view)
						{
							if ($param['return_fullPath']) $oneline['name']=$dir.$oneline['name'];
							$multilist[]=$oneline;
						}
					}
				}
			}
			unset($list);
			if (isset($param['sort']))
			{
				
				funcArray::simpleSortTable($multilist, $param['sort']);
				
				/*$c=usort(&$multilist, function ($a,$b) use ($param)
					{
						foreach ($param['sort'] as $key => $val)
						{
							$val=strtolower($val);
							if (is_string($a[$key]) || is_string($b[$key])) { $r=strnatcasecmp($a[$key], $b[$key]);}
							else
							{
								if ($a[$key]<$b[$key]) { $r=-1; }
								else if ($a[$key]>$b[$key]) { $r=1; }
									else $r=0;
							}
							if ($r!=0)
							{
								if ($val=='desc')
								{
									if ($r==-1) { $r=1; }
										else if ($r==1) $r=-1;
								}
								return $r;
							}
						}
						return 0;
					}
				);*/
			}
			if ($param['return_full']) { return $multilist; }
				else return funcArray::selectColumn($multilist, 'name', array('saveIndex'=>false, 'saveValue'=>false));
		}
		return false;
	} // end function scandir
// =====================
	public static function checkFileFromDirs($filename, $arDirs) // ���������� � ����� �������� �� ������ ���� ����. ���������� ������ ���������� �������
	{
		if (is_array($arDirs))
			foreach ($arDirs as $dirname)
			{
				$dirname=static::normalizePath($dirname);
				$l=strrpos($dirname, '/');
				if ($l!=strlen($dirname)-1) $dirname.='/';
				if (is_file($dirname.$filename)) return $dirname;
			}
		return false;
	} // end checkFileFromDirs
// =====================
	public static function checkFileFromDirsAll($filename, $arDirs) // ���������� ������� ����� � ��������� ������. ���������� ��� ���������� � ������� ��������� ����
	{
		$arResult=array();
		if (is_array($arDirs))
			foreach ($arDirs as $dirname)
			{
				$dirname=static::normalizePath($dirname);
				$l=strrpos($dirname, '/');
				if ($l!=strlen($dirname)-1) $dirname.='/';
				if (is_file($dirname.$filename)) $arResult[]=$dirname;
			}
		return $arResult;
	} // end checkFileFromDirs
// =====================
	public static function checkDirList(array $arDirs) // ��������� ������ � ������� ���������� �� �� �������
	{
		foreach ($arDirs as $key => $name) if (!is_dir($name)) unset($arDirs[$key]);
		return $arDirs;
	} // end checkDirList
// =====================
	public static function checkFilesList($dir, array $arFiles) // ��������� ������ � ������� ������ �� �� ������� � ����������
	{
		$dir=static::normalizeDir($dir);
		if (is_dir($dir)) {
			foreach ($arFiles as $key => $fn) if (!file_exists($dir.$fn)) unset($arFiles[$key]);
			return $arFiles;
		}
		return false;
	} // end checkFilesList
// =====================
	public static function getFileExtWithDot($e_name) // ���������� ����� � ������
	{
		$e=static::getFileExt($e_name);
		if ($e) return '.'.$e;
		return '';
	}
// =====================
	public static function getFileExt($e_name) // ���������� ����� ��� �����
	{
		return strtolower(pathinfo($e_name, PATHINFO_EXTENSION));
		/*$ext_pos=strrpos($e_name, '.');// ������� ����������
		$ext='';
		if ($ext_pos!==false) $ext=strtolower(substr($e_name,$ext_pos+1));
		return $ext;*/
	}
// =====================
	public static function getFileName($e_name) // ���������� ��� �����
	{
		return pathinfo($e_name, PATHINFO_FILENAME);
	}
// =====================
	public static function getMimeFromExt($ext) // ���������� mime �� ����������
	{
		// ���������� ������������ �� ����� � 1 �������
		$p=strpos($ext, '.');
		if ($p!==false && $p==0) { $ext2=strtolower($ext); } else { $ext2='.'.strtolower($ext); }
		if (isset(static::$mimeType[$ext2])) { return static::$mimeType[$ext2]; }
			else return false;
	}
// =====================
	/** ����������� ����. ���������� ���������� � ���. ���� ���� ������������ $relDir, �� ���������� ���.
	*/
	protected static function normalizePathString($path, $relDir=false) {
		$path=str_replace(array('\\', '\\\\', '/', '//', '/./'), '/', ($relDir ? $relDir.$path : $path));
		if (strpos($path, '../')) {
			// ��������� ..
			$ar=explode('/', $path);
			$ar2=array();
			$count=sizeof($ar);
			$prev=-1;
			for ($i=0; $i<$count; $i++) {
				if ($ar[$i]=='..') {
					$s=sizeof($ar2)-1;
					if ($s >= 0) unset($ar2[$s]);
				} else {
					$ar2[sizeof($ar2)]=$ar[$i];
				}
			}
			if (sizeof($ar2)==0) return '';
			$path=implode('/', $ar2);
		}
		return $path;
	}
	
	/** ����������� ����. ���������� ���������� � ���. ���� ���� ������������ $relDir, �� ���������� ���.
	*/
	public static function normalizePath($path, $relDir=false) { // ���������� ���� � ����� � ������� �����
		if (is_array($path)) {
			foreach ($path as $key => &$val) {
				$val=static::normalizePathString($val, $relDir);
			}
			return $path;
		} else {
			return static::normalizePathString($path, $relDir);
		}
	}
// =====================
	public static function scansubdir($dir, $param=array()) // ��������� �����������. ��������� ������ � ���� �������, � ������� ������������ ������ ��������� � ������	
	/* ���������:	
		
	������ ������: ���������� ������������ ������	
	������ ������� - ������	
		level - �������	
		name - ���	
		fullname - ������ ��� �� ����� �������	
		relname - ���, ������������ ��������� $dir	
		
		onlyname - ������ ��� �����, ��� ����������
		ext - ������ ����������
		
		prev - �����c �������� ����� �������, ������� �������� ������������ ��� �����
		count - ���-�� ��������� �� ���� ������
		i - ����� �������� �� ������
	
	*/
	{
		/*$res=array();*/
		
		$r2=array();
		$r2_i=0;
		
		if ($dir!='')
		{
			$stepdir=$dir;
			$level=0;
			
			$stack=array(0=>scandir($stepdir));
			$arI=array(0=>0);
			$arSize=array(0=>sizeof($stack[0]));
			$arRelName=array(0=>'');
			
			$arR2Prev=array(0=>false);
			
			while ($arI[$level]<$arSize[$level]) {
				$value=&$stack[$level][$arI[$level]];
				if ($value!='.' && $value!='..') {
					$rvalue=$arRelName[$level].$value;
					$fvalue=$dir.$rvalue;
					
					$r2[$r2_i]=array('level'=>$level, 'name'=>$value, 'prev'=>$arR2Prev[$level], 'dir'=>false, 'count'=>$arSize[$level]-2, 'i'=>$arI[$level]-2);
					
					// ������� ������ ��� � ����������
					$pos_point=strrpos($value, '.');
					if ($pos_point===false)
					{
						$r2[$r2_i]['ext']='';
						$r2[$r2_i]['onlyname']=$value;
					}
					else {
						$r2[$r2_i]['onlyname']=substr($value, 0, $pos_point);
						$r2[$r2_i]['ext']=substr($value, $pos_point+1);
					}
					
					if (is_dir($fvalue))
					{
						$r2[$r2_i]['dir']=true;
						$r2[$r2_i]['fullname']=$fvalue.'/';
						$r2[$r2_i]['relname']=$rvalue.'/';
						$arI[$level]++;
						$level++;
						$arR2Prev[$level]=$r2_i;
						
						$arRelName[$level]=$rvalue.'/';
	
						$stack[$level]=scandir($fvalue.'/');
						$arI[$level]=0;
						$arSize[$level]=sizeof($stack[$level]);
						if ($arSize[$level]==2) $level--;
					}
					else
					{
						$r2[$r2_i]['fullname']=$fvalue;
						$r2[$r2_i]['relname']=$rvalue;
						$arI[$level]++;
					}
					while ($level>0 && $arI[$level]>=$arSize[$level]) $level--;
					$r2_i++;
				}
				else $arI[$level]++;
			} // end while
		}
		return $r2;
	} // end scansubdir
//==================
	public static function normalizeDir($dir) {// ����������� ���� � ��������� - ��������� ������ /
		$res=static::normalizePath($dir);
		if (substr($res, strlen($res)-1, 1)!='/') $res.='/';
		return $res;
	}
//==================
	public static function backSearch($startDir, $name, $endDir='/', $mode=1) // �������� ����� ����� �\��� ����������. ���������� ������ ���������� ��������
	/* $startDir - ���������� � ������� ���������� �����. ���������� �������� ������ ����
	$name - ��� ����. ������ ����������
	$mode - 0 - ����� ����������
		1 - ����� �����
		>1 - ����� � ����� � ����������
	$endDir - ��� ���������� ��������� � ������� ����� ����� - ���������� �������� ������ ����
	���� ������ �� ������� - ������������ false
	���������� ������:
		
	*/
	{
		$startDir=static::normalizeDir($startDir);
		$endDir=static::normalizeDir($endDir);
		// ������ endDir � startDir
		$posED=strpos($startDir, $endDir);
		if ($posED===false)
		{
			$endDir='/';
			$posED=strpos($startDir, $endDir);
			if ($posED===false) return false;
		}
		$relStartDir=substr($startDir, $posED+strlen($endDir)-1);
		$relEndDir=substr($startDir, 0, $posED+strlen($endDir)-1);
	
		$end=false;
		$rel_2=$relStartDir;
		while (!$end)
		{
			$p=strrpos($rel_2, '/');
			if ($p!==false)
			{
				$rel_2=substr($rel_2, 0, $p);
				$fullPath=$relEndDir.$rel_2;
				if (is_dir($fullPath))
				{
					$list=scandir($fullPath);
					$arFindKeys=array_keys($list, $name);
					if ($arFindKeys)
					{ // ���-�� �������
						reset($arFindKeys);
						$res=false;
						if ($mode>1) { $res=$list[current($arFindKeys)]; }
						else
						{
							foreach ($arFindKeys as $key)
							{
								$name_1=&$list[$key];
								$name_1_dir=is_dir($fullPath.$name_1);
								if ($mode==0 && $name_1_dir) { $res=$name_1; break; }
								if ($mode==1 && !$name_1_dir) { $res=$name_1; break; }
							}
						}
						if ($res)
						{
							return static::normalizeDir($fullPath).$res;
						}
					}
				} else return false;
			} else $end=true;
		} // end while
		return false;
	} // end backSearch
	
	/** ������������ url � ����� css ��� ������������� ������� ������
	 * 
	 * @param string $str
	 * @param string $filename - ����, ������������ �������� ����� �������������� ����
	 * @return mixed|unknown
	 */
	public static function cssUrlCorrection($str, $filename) { // ����������� ������������� url � ���������� ������������ $filename
		$dirFile=dirname($filename).'/';
		return preg_replace_callback('/url\s*\((\s*[\'"]*\s*([a-zA-z\.\/0-9\-?&=]+)\s*[\'"]*\s*)\)/', function ($matches) use ($dirFile) {
			if (strpos($matches[2], '/')===0) {
				$res=funcFileSystem::normalizePath($matches[2]);
			} else {
				$res=funcFileSystem::normalizePath($matches[2], $dirFile);
			}
			return 'url("'.$res.'")';
		}, $str);
	} // end cssUrlCorrection
	
	// ���������� ���� ������������ $relDir ��� false, ���� $fullPath �� ����� ���� ����� ������������ $relDir
	public static function relativePath($fullPath, $relDir) {
		if (!$fullPath || !$relDir) return false;
		$fullPath=static::normalizePath($fullPath);
		$relDir=static::normalizeDir($relDir);
		if (strpos($fullPath, $relDir)===0) {
			return '/'.substr($fullPath, strlen($relDir));
		}
		return false;
	} // end relativePath
	
	public static function satinizeFilename($fn) { // ������ ��� ����� �� ������������ ������
		return str_replace(
			['..', '..', '\\\\', '//', '//', '*', '?'],
			['', '', '/', '/', '/', '', ''],
			$fn
		);
	} // end satinizeFilename
	
// --------------------------------------------------------------------------------
} // end class