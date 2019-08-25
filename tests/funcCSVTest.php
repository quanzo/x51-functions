<?php
namespace x51\tests\functions;
	use x51\functions\funcArray;
	use x51\functions\funcCSV;
	
class funcCSVTest extends \PHPUnit\Framework\TestCase{
	protected $arFields=[
			'id', 'color', 'desc'
	];
	protected $strFieldsCsv='"id";"color";"desc"'."\r\n";
	protected $arData=[
			['id'=>0, 'color'=>"", 'desc'=>""],
			['id'=>1, 'color'=>"", 'desc'=>""],
			['id'=>2, 'color'=>"", 'desc'=>""],
			['id'=>3, 'color'=>"", 'desc'=>""],
			['id'=>4, 'color'=>"", 'desc'=>""],
			['id'=>5, 'color'=>"", 'desc'=>""],
			['id'=>6, 'color'=>"", 'desc'=>""],
	];
	protected $arPutTest=[
			['data'=>['id'=>0, 'color'=>"red", 'desc'=>'"One" "Two"'], 'result'=>'"0";"red";"""One"" ""Two"""'."\r\n"]
	];
	
	protected $fn='csvtest.csv';
	
	public function testPutGet() {
		foreach ($this->arPutTest as $num => $test) {
			$strCsv=funcCSV::str_putcsv($test['data']);
			$this->assertEquals($test['result'], $strCsv);
			$this->assertEquals(array_values($test['data']), funcCSV::str_getcsv($strCsv));
		}
	}
	
	/*public function testCreateCsv() {
		$ffn=__DIR__.'/'.$fn;
		$h=fopen($ffn, 'w');
		$this->assertEquals($h!=false, true);
		if ($h) {
			$fields=funcCSV::str_putcsv($this->arFields);
			
			
			fclose($h);
		} else {
			die('Not open '.$ffn);
		}
	}*/
	
}