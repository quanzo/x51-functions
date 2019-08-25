<?php


// ###
namespace x51\functions;

// /###

class funcDebug
{
	// из ее плюсов перед die + print_r, часто надо посмотреть картину в разрезе – больше 1ой переменной, поэтому функция выводит все переменные переданные ей. Так же она выводит список заинклуженных файлов + статистику использования памяти
	public static function debug($var)  
	{  
	    while(ob_get_length()) ob_end_clean();  
	    ob_start();  
	  
	    $vars = func_get_args();  
	    echo "<pre>";  
	    foreach($vars as $var)  
	    {  
	        print_r($var);  
	        echo '<HR size="1">';  
	    }  
	    echo "<B>Used memory</B>: ".number_format(memory_get_usage())." bytes\n";  
	    echo "<B>Used real memory</B>: ".number_format(memory_get_usage(1))." bytes\n";  
	    echo "<HR size=\"1\">\n";  
	    echo "<B>Included files list</B>\n";  
	    echo " 
	<UL>\n";  
	    foreach(get_included_files() as $i=>$v)  
	    {  
	        echo " 
	<LI>#$i: $v</LI> 
	 
	\n";  
	    }  
	    echo "</UL> 
	 
	";  
	  
	    echo "<HR size=\"1\">\n";  
	    echo "<B>Current resource usages</B>\n";  
	    echo " 
	<UL>\n";  
	    foreach(getrusage() as $i=>$v)  
	    {  
	        echo " 
	<LI>$i = $v</LI> 
	 
	\n";  
	    }  
	    echo "</UL> 
	 
	";  
	    if(!isset($_SERVER['REQUEST_METHOD'])) die(strip_tags(ob_get_clean()));
	    die();  
	} // end debug
	
	public static function debugPrint() // отладочный вывод 
	{
		$countArg=func_num_args();
		if ($countArg>0)
		{
			$arArg=func_get_args();
			$arBackTrace=debug_backtrace();
			echo "<h1>debugPrint: ".$arBackTrace[0]['file'].':'.$arBackTrace[0]['line']."</h1><br>\r\n";
			foreach ($arArg as $mArg)
			{
				if (is_array($mArg) || is_object($mArg))
				{
					echo "<pre>".nl2br(print_r($mArg, true))."</pre>\r\n\r\n";
				}
				else echo $mArg;
				echo "<hr>";
			}
		}
		else
		{
			echo "debugPrint: нет аргументов.<br>\r\n";
		}
	} // end funcDebug::print
} // end class
?>