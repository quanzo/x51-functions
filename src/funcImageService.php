<?php


// ###
namespace x51\functions;

// /###

/*
public static function out_image($filename) // ������ ������������ ���� � �������
public static function getImageSize($filename) // ���������� ������ �������� $filename
public static function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF)
public static function calc_printSize($mm_width, $mm_height,$dpi) // ���������� ������ ����������� ��� ������ � ������������� �����������. ������� � ��
public static function calc_resizeConstRatio($src_size, $new_size, $align=false) // ����������� ����� ��������� ��� ��������� �������� ������� � ����������� ����������� ������


*/

class funcImageService{
	const DIRECTION_X=0;
	const DIRECTION_Y=1;
	
	static $sizeCache=array();

	
	public static function out_image($filename) {
		$size = getimagesize($filename);
		$fp=fopen($filename, "rb");
		if ($size && $fp) {
	  		header("Content-type: {$size['mime']}");
	  		header("Content-Length: " . filesize($filename));
	  		fpassthru($fp);
			fclose($fp);
			return true;
		} else {
			return false;
		}
	} // end out_image
	
	
	public static function getImageSize($filename) // ���������� ������ �������� $filename
	/* ������������ ������
	0 - ������
	1 - ������
	width - ������
	height - ������
	mime - 
	*/
	{
		if (isset(static::$sizeCache[$filename]))
		{
			return static::$sizeCache[$filename];
		}
		else
		{
			static::$sizeCache[$filename]=getimagesize($filename);
			if (static::$sizeCache[$filename]!=false)
			{
				static::$sizeCache[$filename]['width']=static::$sizeCache[$filename][0];
				static::$sizeCache[$filename]['height']=static::$sizeCache[$filename][1];
			}
			return static::$sizeCache[$filename];
		}
	} // getImageSize
	
	
	/***********************************************************************************
	������� img_resize(): ��������� thumbnails
	���������:
	  $src             - ��� ��������� �����
	  $dest            - ��� ������������� �����
	  $width, $height  - ������ � ������ ������������� �����������, � ��������
	�������������� ���������:
	  $rgb             - ���� ����, �� ��������� - �����
	  $quality         - �������� ������������� JPEG, �� ��������� - ������������ (100)
	***********************************************************************************/
	public static function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=75)
	{
	  if (!file_exists($src)) return false;
	  $size = static::getImageSize($src);
	  if ($size === false) return false;
	
	  // ���������� �������� ������ �� MIME-����������, ���������������
	  // �������� getimagesize, � �������� ��������������� �������
	  // imagecreatefrom-�������.
	  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	  $icfunc = "imagecreatefrom" . $format;
	  if (!function_exists($icfunc)) return false;
	  $new_sizes=static::calc_resizeConstRatio(array('width'=>$size[0],'height'=>$size[1]),array('width'=>$width, 'height'=>$height)); // ������� �������� ������ ������� � ����������� ����������� ������
	  // ������������ ������ ������
	  if ($width==0) $width=$new_sizes['width'];
	  if ($height==0) $height=$new_sizes['height'];
	
	  
	  $isrc = $icfunc($src);
	  $idest = imagecreatetruecolor($width, $height);
	
	  imagefill($idest, 0, 0, $rgb);
		imagecopyresampled($idest, $isrc, $new_sizes['left'], $new_sizes['top'], 0, 0, $new_sizes['width'], $new_sizes['height'], $size[0], $size[1]);
		$outfunc='image'.$format;
		if ($format=='jpeg' || $format=='png')
		{
			$outfunc($idest, $dest, $quality);
		} else $outfunc($idest, $dest);
		imagedestroy($isrc);
		imagedestroy($idest);
	
	  return true;
	} // img_resize
	
	public static function img_resize_jpg($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=95)
	{
	  if (!file_exists($src)) return false;
	
	  $size = getimagesize($src);
	
	  if ($size === false) return false;
	
	  // ���������� �������� ������ �� MIME-����������, ���������������
	  // �������� getimagesize, � �������� ��������������� �������
	  // imagecreatefrom-�������.
	  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	  $icfunc = "imagecreatefrom" . $format;
	  if (!function_exists($icfunc)) return false;
	
	  /*$x_ratio = $width / $size[0];
	  $y_ratio = $height / $size[1];
	
	  $ratio       = min($x_ratio, $y_ratio);
	  $use_x_ratio = ($x_ratio == $ratio);
	
	  $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
	  $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
	  $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
	  $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);*/
	  
	  $new_sizes=static::calc_resizeConstRatio(array('width'=>$size[0],'height'=>$size[1]),array('width'=>$width, 'height'=>$height));
	  
	
	  $isrc = $icfunc($src);
	  $idest = imagecreatetruecolor($width, $height);
	
	  imagefill($idest, 0, 0, $rgb);
	  //imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
		imagecopyresampled($idest, $isrc, $new_sizes['left'], $new_sizes['top'], 0, 0, $new_sizes['width'], $new_sizes['height'], $size[0], $size[1]);
	  imagejpeg($idest, $dest, $quality);
	
	  imagedestroy($isrc);
	  imagedestroy($idest);
	
	  return true;
	}
	
	public static function calc_printSize($mm_width, $mm_height,$dpi) // ���������� ������ ����������� ��� ������ � ������������� �����������. ������� � ��
	{
		$koff=3937/100000; // ����������� ���������
		return array('width'=>round($koff*$mm_width*$dpi), 'height'=>round($koff*$mm_height*$dpi));
	}
	
	
	public static function calc_resizeConstRatio($src_size, $new_size, $align=false) // ����������� ����� ��������� ��� ��������� �������� ������� � ����������� ����������� ������
	/* 
	�������� align['horizontal'] left/right      align['vertical'] top/bottom
	�� ��������� ��������� ������� �� ��������
	������������ ������ � ����������� width/height - ������ �������, top/left - ��������� �� ������
	������ ������ ��� ������� �������� new_size
	*/
	{
		//if (!isset($new_size['width']) || $new_size['width']==0) 
		//if (!isset($new_size['height']) || $new_size['height']==0) 
	//funcDebug::debugPrint($new_size);
		if ($new_size['width']==0 && $new_size['height']==0)
		{
			$ratio=1;
			$new_size['width']=$src_size['width'];
			$new_size['height']=$src_size['height'];
			$use_x_ratio=false;
		}
		else if ($new_size['width']==0)
			{
				$ratio = $new_size['height'] / $src_size['height'];
				$new_size['width']=floor($src_size['width'] * $ratio);
				$use_x_ratio=false;
			}
			else if ($new_size['height']==0)
			{
				//funcDebug::debugPrint('<h1>height=0 width<>0</h1>');
				$ratio = $new_size['width'] / $src_size['width'];
				$new_size['height']=floor($src_size['height'] * $ratio);
				$use_x_ratio=true;
			}
			else
			{
				$x_ratio = $new_size['width'] / $src_size['width'];
				$y_ratio = $new_size['height'] / $src_size['height'];
				$ratio       = min($x_ratio, $y_ratio);
				$use_x_ratio = ($x_ratio == $ratio);
			}
		//funcDebug::debugPrint($ratio, $src_size, $new_size);
		$res=array();
		$res['width']   = $use_x_ratio  ? $new_size['width']  : floor($src_size['width'] * $ratio);
		$res['height']  = !$use_x_ratio ? $new_size['height'] : floor($src_size['height'] * $ratio);
		
		// ���������� - ���������
		if ($use_x_ratio)
		{
			$res['left']=0;
			$res['top']=floor(($new_size['height']-$res['height']) / 2);	
		}
		else
		{
			$res['left']=floor(($new_size['width']- $res['width']) / 2);
			$res['top']=0;
		}
		//$res['left']    = $use_x_ratio  ? 0 : floor(($new_size['width'] - $res['width']) / 2);
		//$res['top']     = !$use_x_ratio ? 0 : floor(($new_size['height'] - $res['height']) / 2);
		
		if ($align!=false)
		{
			if ($align['vertical']=='top') {$res['top']=0;}
			if ($align['vertical']=='bottom') {$res['top']=!$use_x_ratio ? 0 : floor($new_size['height'] - $res['height']);}
			if ($align['horizontal']=='left') {$res['left']=0;}
			if ($align['horizontal']=='right') {$res['left']=$use_x_ratio ? 0 : floor($new_size['width'] - $res['width']);}
		}
		
		 return $res;
	} // end calc_resizeConstRatio
	
	/** ������ ����������� 
	*/
	public static function blank($width, $height, $use_alpha = false) {
		$image = imagecreatetruecolor($width, $height);
		if ($use_alpha) {
			imagesavealpha($image, true);
			$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
			imagefill($image, 0, 0, $transparent);
		}
		return $image;
	}
	
	public static function fromFile($filename) {
		if (!file_exists($filename)) return false;
		$size = static::getImageSize($filename);
		if ($size === false) return false;
		// ���������� �������� ������ �� MIME-����������, ���������������
		// �������� getimagesize, � �������� ��������������� �������
		// imagecreatefrom-�������.
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		return $icfunc($filename);
	}
	
	/** ��������� ����������� � ����
	*/
	public static function saveImage($img, $file_output, $ext='jpeg', $jpeg_quality=75) {
		if ($ext == 'jpeg') { 
			return imagejpeg($img,$file_output,$jpeg_quality);
		}
		$func = 'image'.$ext;
		return $func($img,$file_output);
	}
	
	/** ��������� �������� � ����������� ���������. � ����������� ���������.
	���������:
	  $isrc             - �������� ��������
	  $width, $height  - ������ � ������ ������������� �����������, � ��������
	�������������� ���������:
	  $rgb             - ���� ����, �� ��������� - �����
	  $quality         - �������� ������������� JPEG, �� ��������� - ������������ (100)
	*/
	public static function resize($isrc, $width, $height, $rgb=0xFFFFFF, $quality=75) {
		$src_x=imagesx($isrc);
		$src_y=imagesy($isrc);
		
		if ($src_x==$width && $src_y==$height){
			return $isrc;
		}
		$new_sizes=static::calc_resizeConstRatio(
			array('width'=>$src_x,'height'=>$src_y),
			array('width'=>$width, 'height'=>$height)
		); // ������� �������� ������ ������� � ����������� ����������� ������
		// ������������ ������ ������
		if ($width==0) $width=$new_sizes['width'];
		if ($height==0) $height=$new_sizes['height'];
		
		$idest = imagecreatetruecolor($width, $height);
		imagefill($idest, 0, 0, $rgb);
		imagecopyresampled($idest, $isrc, $new_sizes['left'], $new_sizes['top'], 0, 0, $new_sizes['width'], $new_sizes['height'], $src_x, $src_y);
		return $idest;
	} // img_resize
	
	public static function join(array $arImages, $width, $height, $direction=0, $rgb=0xFFFFFF){
		
		/*$min=array(
			'width'=>99999,
			'height'=>99999
		);
		$max=array(
			'width'=>0,
			'height'=>0
		);
		$arSize=array();
		foreach ($arImages as $key => $image) {
			$arSize[$key]=array();
			$info=getimagesizefromstring($image);
			if ($info) {
				$arSize[$key]['width']=$info[0];
				$arSize[$key]['height']=$info[1];
				if ($min['width']>$info[0]) {
					$min['width']>$info[0];
				}
				if ($min['height']>$info[1]) {
					$min['height']=$info[1];
				}
				if ($max['width']<$info[0]) {
					$max['width']=$info[0];
				}
				if ($max['height']<$info[1]) {
					$max['height']=$info[1];
				}
			}
		} // end foreach*/
		
		$resSize=array();
		if ($direction==static::DIRECTION_X) {
			$resSize['width']=$width*sizeof($arImages);
			$resSize['height']=$height;
		}
		if ($direction==static::DIRECTION_Y) {
			$resSize['width']=$width;
			$resSize['height']=$height*sizeof($arImages);
		}
		
		$idest = imagecreatetruecolor($resSize['width'], $resSize['height']);
		imagefill($idest, 0, 0, $rgb);
		$dst_x=0;
		$dst_y=0;
		foreach ($arImages as $key => $image) {
			$image_1=static::resize($image, $width, $height, $rgb);
			if ($image_1) {
				if (imagecopy($idest, $image_1, $dst_x, $dst_y, 0, 0, $width, $height)) {
					if ($direction==static::DIRECTION_X) {
						$dst_x+=$width;
					}
					if ($direction==static::DIRECTION_Y) {
						$dst_y+=$height;
					}
				}
			}
		}
		return $idest;
	} // end func
	
	
	
	
	
	
} // end class funcImageService