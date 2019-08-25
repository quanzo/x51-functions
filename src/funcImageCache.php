<?php


// ###
namespace x51\functions;

// /###

# класс с функциями кеширования изображений
#
#
#
#
#

class funcImageCache
{

	public static $cache_style=1; /* 0 - стандарт: сначала поиск производится в каталогах сайта, а затем в каталогах ЦМС 
	
	1-разделение на основании регистрации: если пользователь выполнил вход систему, то поиск производится в каталогах сайта и затем в каталогах ЦМС. если пользователь не в системе, то поиск производится только в каталоге сайта
	*/
	
	public static $cache_name='cache/'; // имя каталога с кешированными вариантами граф.файлов
	public static $strict_ratio=5; // ограничение на увеличение
	public static $error=0; // ошибок нет
	
	// возвращает массив с именами каталогов
	public static function getDirs()
	{
		global $cms_home_dir, $cms_dir, $cms_local_image_dirname;
		return array(
			'img_dir_public'=>(HOME_DIR.$_ENV['cms_dir']['image_local']), // каталог с изображениями находящимися на текущем сайте
			'img_dir_strict'=>($_ENV['cms_dir']['main'].$_ENV['cms_dir']['image']), // каталог с изображениями в директории CMS
			'cache_dir_public'=>(HOME_DIR.$_ENV['cms_dir']['image_local'].self::$cache_name), // кеш в директории текущего сайта
			'cache_dir_strict'=>($_ENV['cms_dir']['main'].$_ENV['cms_dir']['image'].self::$cache_name) // кеш в директории CMS
		);
	}
	
	
	// определяет наличие графического файла
	// параметры $param: result=dir/full     find_in=dir/cache
	// если result=dir - только каталог, если full - полный путь с именем файла
	// если find_in=dir - ищем в каталогах (не в кеше), find_in=cache - ищем только в кэше (сайта и ЦМС)
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
				0=>$sta_dir['img_dir_public'], // каталог сайта
				1=>$sta_dir['img_dir_strict'] // каталог в ЦМС
			);
		}
		if ($param['find_in']=='cache')
		{
			$sta_path=array(
				0=>$sta_dir['cache_dir_public'], // каталог сайта
				1=>$sta_dir['cache_dir_strict'] // каталог в ЦМС
			);
		}
		
			
		// убираем недопустимые символы - на всякий случай
		//$filename=str_replace(array('\\','/','.'),'',$i_filename);
		$fn=$filename; // просто имя
		// определяем наличие файла с таким именем - только jpg, gif, png
		{
			//$ext=strtolower($i_ext);
			if (isset($sta_ext[$ext]))
			{
				$sta_ext[$ext]=1;
				$filename.='.'.$ext;
			}
			else { self::$error=2; } // неверное расширение
		}

		if (!self::$error) // определяем наличие граф файла
		{
			if (self::$cache_style==1)
			{
				if (isLogged())
				{
					// ищем сначала в каталоге сайта а затем в закрытом каталоге системы управления
					if (!file_exists($sta_path[0].$filename))
					{
						if (!file_exists($sta_path[1].$filename)) { self::$error=3; } // файл не найден
							else { $full_path=&$sta_path[1]; }
					}
					else { $full_path=&$sta_path[0]; }
				}
				else
				{
					// ищем только в каталоге сайта
					if (!file_exists($sta_path[0].$filename)) { self::$error=3; } // файл не найден
						else { $full_path=&$sta_path[0]; }
				}
			}
			else
			{
				// ищем сначала в каталоге сайта а затем в закрытом каталоге системы управления
				if (!file_exists($sta_path[0].$filename))
				{
					if (!file_exists($sta_path[1].$filename)) { self::$error=3; } // файл не найден
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
	
	/* возвращает полное имя граф.файла с определенной шириной и высотой
	параметры: w - ширина   h - высота   rgb - цвет фона рисунка
	можно задавать только ширину или высота и тогда недостающий параметр будет посчитан по ratio
	если файла с подходящими параметрами нет в кеше и есть в наличии исходный файл, то создаем в кеше нужный вариант
	
	
	*/
	public static function getImageFile($i_filename, $i_ext, $param=array())
	{
		global $cms_home_dir, $cms_dir, $cms_local_image_dirname;
		
		self::$error=0;
		
		if (!isset($param['w'])) { $param['w']=0; }
		if (!isset($param['h'])) { $param['h']=0; }
		if (!isset($param['rgb'])) { $param['rgb']=0xFFFFFF; }
		
		// убираем недопустимые символы - на всякий случай
		$filename=str_replace(array('\\','/','.'),'',$i_filename);
		$fn=$filename; // просто имя
		$ext=strtolower($i_ext);
		$full_path=self::findImageFile($filename, $ext, array('result'=>'dir'));
		
		$filename.='.'.$ext;
		
		if (!self::$error) // определение характеристик для вывода
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
			
			// ограничение на увеличение
			$x_ratio = $w / $image_info[0];
			$y_ratio = $h / $image_info[1];
			$max_ratio=max($x_ratio, $y_ratio);
			//$strict_ratio=5;
			if ($max_ratio>self::$strict_ratio) // увеличивать не более чем на $strict_ratio
			{
				$x_ratio=$x_ratio-($max_ratio-$strict_ratio);
				$y_ratio=$y_ratio-($max_ratio-$strict_ratio);
				$w=round($image_info[0]*$x_ratio);
				$h=round($image_info[1]*$y_ratio);
			} // ограничение на увеличение
			
			$cache_filename=$fn.'_'.$w.'_'.$h.'_'.$param['rgb'].'.'.$ext; // имя в кеше
			//echo $w.'    '.$h;
			if (!file_exists($full_path.self::$cache_name.$cache_filename))
			{
				// создаем кэшированную копию
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