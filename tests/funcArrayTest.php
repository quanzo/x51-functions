<?php
namespace x51\tests\functions;
	use x51\functions\funcArray;
	
class funcArrayTest extends \PHPUnit\Framework\TestCase{
	
	protected $arTable=[
['name'=>'AliceBlue', 'hex'=>'#F0F8FF', 'rgb'=>'240,248,255', 240, 248, 255],
['name'=>'BlueViolet', 'hex'=>'#8A2BE2', 'rgb'=>'138,43,226', 138, 43, 226],
['name'=>'CadetBlue', 'hex'=>'#5F9EA0', 'rgb'=>'95,158,160', 95, 158, 160],
['name'=>'CadetBlue1', 'hex'=>'#98F5FF', 'rgb'=>'152,245,255', 152, 245, 255],
['name'=>'CadetBlue2', 'hex'=>'#8EE5EE', 'rgb'=>'142,229,238', 142, 229, 238],
['name'=>'CadetBlue3', 'hex'=>'#7AC5CD', 'rgb'=>'122,197,205', 122, 197, 205],
['name'=>'CadetBlue4', 'hex'=>'#53868B', 'rgb'=>'83,134,139', 83, 134, 139],
['name'=>'CornflowerBlue', 'hex'=>'#6495ED', 'rgb'=>'100,149,237', 100, 149, 237],
['name'=>'DarkBlue', 'hex'=>'#00008B', 'rgb'=>'0,0,139', 0, 0, 139],
['name'=>'DarkCyan', 'hex'=>'#008B8B', 'rgb'=>'0,139,139', 0, 139, 139],
['name'=>'DarkSlateBlue', 'hex'=>'#483D8B', 'rgb'=>'72,61,139', 72, 61, 139],
['name'=>'DarkTurquoise', 'hex'=>'#00CED1', 'rgb'=>'0,206,209', 0, 206, 209],
['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255],
['name'=>'DeepSkyBlue1', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255],
['name'=>'DeepSkyBlue2', 'hex'=>'#00B2EE', 'rgb'=>'0,178,238', 0, 178, 238],
['name'=>'DeepSkyBlue3', 'hex'=>'#009ACD', 'rgb'=>'0,154,205', 0, 154, 205],
['name'=>'DeepSkyBlue4', 'hex'=>'#00688B', 'rgb'=>'0,104,139', 0, 104, 139],
['name'=>'DodgerBlue', 'hex'=>'#1E90FF', 'rgb'=>'30,144,255', 30, 144, 255],
['name'=>'DodgerBlue1', 'hex'=>'#1E90FF', 'rgb'=>'30,144,255', 30, 144, 255],
['name'=>'DodgerBlue2', 'hex'=>'#1C86EE', 'rgb'=>'28,134,238', 28, 134, 238],
['name'=>'DodgerBlue3', 'hex'=>'#1874CD', 'rgb'=>'24,116,205', 24, 116, 205],
['name'=>'DodgerBlue4', 'hex'=>'#104E8B', 'rgb'=>'16,78,139', 16, 78, 139],
['name'=>'LightBlue', 'hex'=>'#ADD8E6', 'rgb'=>'173,216,230', 173, 216, 230],
['name'=>'LightBlue1', 'hex'=>'#BFEFFF', 'rgb'=>'191,239,255', 191, 239, 255],
['name'=>'LightBlue2', 'hex'=>'#B2DFEE', 'rgb'=>'178,223,238', 178, 223, 238],
['name'=>'LightBlue3', 'hex'=>'#9AC0CD', 'rgb'=>'154,192,205', 154, 192, 205],
['name'=>'LightBlue4', 'hex'=>'#68838B', 'rgb'=>'104,131,139', 104, 131, 139],
['name'=>'LightCyan', 'hex'=>'#E0FFFF', 'rgb'=>'224,255,255', 224, 255, 255],
['name'=>'LightCyan1', 'hex'=>'#E0FFFF', 'rgb'=>'224,255,255', 224, 255, 255],
['name'=>'LightCyan2', 'hex'=>'#D1EEEE', 'rgb'=>'209,238,238', 209, 238, 238],
['name'=>'LightCyan3', 'hex'=>'#B4CDCD', 'rgb'=>'180,205,205', 180, 205, 205],
['name'=>'LightCyan4', 'hex'=>'#7A8B8B', 'rgb'=>'122,139,139', 122, 139, 139],
['name'=>'LightSkyBlue', 'hex'=>'#87CEFA', 'rgb'=>'135,206,250', 135, 206, 250],
['name'=>'LightSkyBlue1', 'hex'=>'#B0E2FF', 'rgb'=>'176,226,255', 176, 226, 255],
['name'=>'LightSkyBlue2', 'hex'=>'#A4D3EE', 'rgb'=>'164,211,238', 164, 211, 238],
['name'=>'LightSkyBlue3', 'hex'=>'#8DB6CD', 'rgb'=>'141,182,205', 141, 182, 205],
['name'=>'LightSkyBlue4', 'hex'=>'#607B8B', 'rgb'=>'96,123,139', 96, 123, 139],
['name'=>'LightSlateBlue', 'hex'=>'#8470FF', 'rgb'=>'132,112,255', 132, 112, 255],
['name'=>'LightSteelBlue', 'hex'=>'#B0C4DE', 'rgb'=>'176,196,222', 176, 196, 222],
['name'=>'LightSteelBlue1', 'hex'=>'#CAE1FF', 'rgb'=>'202,225,255', 202, 225, 255],
['name'=>'LightSteelBlue2', 'hex'=>'#BCD2EE', 'rgb'=>'188,210,238', 188, 210, 238],
['name'=>'LightSteelBlue3', 'hex'=>'#A2B5CD', 'rgb'=>'162,181,205', 162, 181, 205],
['name'=>'LightSteelBlue4', 'hex'=>'#6E7B8B', 'rgb'=>'110,123,139', 110, 123, 139],
['name'=>'MediumAquamarine', 'hex'=>'#66CDAA', 'rgb'=>'102,205,170', 102, 205, 170],
['name'=>'MediumBlue', 'hex'=>'#0000CD', 'rgb'=>'0,0,205', 0, 0, 205],
['name'=>'MediumSlateBlue', 'hex'=>'#7B68EE', 'rgb'=>'123,104,238', 123, 104, 238],
['name'=>'MediumTurquoise', 'hex'=>'#48D1CC', 'rgb'=>'72,209,204', 72, 209, 204],
['name'=>'MidnightBlue', 'hex'=>'#191970', 'rgb'=>'25,25,112', 25, 25, 112],
['name'=>'NavyBlue', 'hex'=>'#000080', 'rgb'=>'0,0,128', 0, 0, 128],
['name'=>'PaleTurquoise', 'hex'=>'#AFEEEE', 'rgb'=>'175,238,238', 175, 238, 238],
['name'=>'PaleTurquoise1', 'hex'=>'#BBFFFF', 'rgb'=>'187,255,255', 187, 255, 255],
['name'=>'PaleTurquoise2', 'hex'=>'#AEEEEE', 'rgb'=>'174,238,238', 174, 238, 238],
['name'=>'PaleTurquoise3', 'hex'=>'#96CDCD', 'rgb'=>'150,205,205', 150, 205, 205],
['name'=>'PaleTurquoise4', 'hex'=>'#668B8B', 'rgb'=>'102,139,139', 102, 139, 139],
['name'=>'PowderBlue', 'hex'=>'#B0E0E6', 'rgb'=>'176,224,230', 176, 224, 230],
['name'=>'RoyalBlue', 'hex'=>'#4169E1', 'rgb'=>'65,105,225', 65, 105, 225],
['name'=>'RoyalBlue1', 'hex'=>'#4876FF', 'rgb'=>'72,118,255', 72, 118, 255],
['name'=>'RoyalBlue2', 'hex'=>'#436EEE', 'rgb'=>'67,110,238', 67, 110, 238],
['name'=>'RoyalBlue3', 'hex'=>'#3A5FCD', 'rgb'=>'58,95,205', 58, 95, 205],
['name'=>'RoyalBlue4', 'hex'=>'#27408B', 'rgb'=>'39,64,139', 39, 64, 139],
['name'=>'SkyBlue', 'hex'=>'#87CEEB', 'rgb'=>'135,206,235', 135, 206, 235],
['name'=>'SkyBlue1', 'hex'=>'#87CEFF', 'rgb'=>'135,206,255', 135, 206, 255],
['name'=>'SkyBlue2', 'hex'=>'#7EC0EE', 'rgb'=>'126,192,238', 126, 192, 238],
['name'=>'SkyBlue3', 'hex'=>'#6CA6CD', 'rgb'=>'108,166,205', 108, 166, 205],
['name'=>'SkyBlue4', 'hex'=>'#4A708B', 'rgb'=>'74,112,139', 74, 112, 139],
['name'=>'SlateBlue', 'hex'=>'#6A5ACD', 'rgb'=>'106,90,205', 106, 90, 205],
['name'=>'SlateBlue1', 'hex'=>'#836FFF', 'rgb'=>'131,111,255', 131, 111, 255],
['name'=>'SlateBlue2', 'hex'=>'#7A67EE', 'rgb'=>'122,103,238', 122, 103, 238],
['name'=>'SlateBlue3', 'hex'=>'#6959CD', 'rgb'=>'105,89,205', 105, 89, 205],
['name'=>'SlateBlue4', 'hex'=>'#473C8B', 'rgb'=>'71,60,139', 71, 60, 139],
['name'=>'SteelBlue', 'hex'=>'#4682B4', 'rgb'=>'70,130,180', 70, 130, 180],
['name'=>'SteelBlue1', 'hex'=>'#63B8FF', 'rgb'=>'99,184,255', 99, 184, 255],
['name'=>'SteelBlue2', 'hex'=>'#5CACEE', 'rgb'=>'92,172,238', 92, 172, 238],
['name'=>'SteelBlue3', 'hex'=>'#4F94CD', 'rgb'=>'79,148,205', 79, 148, 205],
['name'=>'SteelBlue4', 'hex'=>'#36648B', 'rgb'=>'54,100,139', 54, 100, 139],
['name'=>'aquamarine', 'hex'=>'#7FFFD4', 'rgb'=>'127,255,212', 127, 255, 212],
['name'=>'aquamarine1', 'hex'=>'#7FFFD4', 'rgb'=>'127,255,212', 127, 255, 212],
['name'=>'aquamarine2', 'hex'=>'#76EEC6', 'rgb'=>'118,238,198', 118, 238, 198],
['name'=>'aquamarine3', 'hex'=>'#66CDAA', 'rgb'=>'102,205,170', 102, 205, 170],
['name'=>'aquamarine4', 'hex'=>'#458B74', 'rgb'=>'69,139,116', 69, 139, 116],
['name'=>'azure', 'hex'=>'#F0FFFF', 'rgb'=>'240,255,255', 240, 255, 255],
['name'=>'azure1', 'hex'=>'#F0FFFF', 'rgb'=>'240,255,255', 240, 255, 255],
['name'=>'azure2', 'hex'=>'#E0EEEE', 'rgb'=>'224,238,238', 224, 238, 238],
['name'=>'azure3', 'hex'=>'#C1CDCD', 'rgb'=>'193,205,205', 193, 205, 205],
['name'=>'azure4', 'hex'=>'#838B8B', 'rgb'=>'131,139,139', 131, 139, 139],
['name'=>'blue', 'hex'=>'#0000FF', 'rgb'=>'0,0,255', 0, 0, 255],
['name'=>'blue1', 'hex'=>'#0000FF', 'rgb'=>'0,0,255', 0, 0, 255],
['name'=>'blue2', 'hex'=>'#0000EE', 'rgb'=>'0,0,238', 0, 0, 238],
['name'=>'blue3', 'hex'=>'#0000CD', 'rgb'=>'0,0,205', 0, 0, 205],
['name'=>'blue4', 'hex'=>'#00008B', 'rgb'=>'0,0,139', 0, 0, 139],
['name'=>'cyan', 'hex'=>'#00FFFF', 'rgb'=>'0,255,255', 0, 255, 255],
['name'=>'cyan1', 'hex'=>'#00FFFF', 'rgb'=>'0,255,255', 0, 255, 255],
['name'=>'cyan2', 'hex'=>'#00EEEE', 'rgb'=>'0,238,238', 0, 238, 238],
['name'=>'cyan3', 'hex'=>'#00CDCD', 'rgb'=>'0,205,205', 0, 205, 205],
['name'=>'cyan4', 'hex'=>'#008B8B', 'rgb'=>'0,139,139', 0, 139, 139],
['name'=>'navy', 'hex'=>'#000080', 'rgb'=>'0,0,128', 0, 0, 128],
['name'=>'turquoise', 'hex'=>'#40E0D0', 'rgb'=>'64,224,208', 64, 224, 208],
['name'=>'turquoise1', 'hex'=>'#00F5FF', 'rgb'=>'0,245,255', 0, 245, 255],
['name'=>'turquoise2', 'hex'=>'#00E5EE', 'rgb'=>'0,229,238', 0, 229, 238],
['name'=>'turquoise3', 'hex'=>'#00C5CD', 'rgb'=>'0,197,205', 0, 197, 205],
['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139],
	];
	
	
	public function testSimpleFilterOneRow(){
		//echo "\nTest simpleFilterRow\n";
		$Tests=[
			[
				'row'=>[0=>10, 1=>14, 3=>18],
				'filter'=>[
					0=>['lo'=>9, 'hi'=>11]
				],
				'res'=>true
			],
			[
				'row'=>[0=>10, 1=>14, 3=>18],
				'filter'=>[0=>['lo'=>11, 'hi'=>100]],
				'res'=>false
			],
			['row'=>[0=>10, 1=>14, 3=>18], 'filter'=>[0=>10], 'res'=>true],
			['row'=>[0=>10, 1=>14, 3=>18], 'filter'=>[0=>10, 1=>14], 'res'=>true],
			['row'=>[0=>10, 1=>14, 3=>18], 'filter'=>[0=>10, 1=>15], 'res'=>false],
			['row'=>[0=>10, 1=>14, 3=>18], 'filter'=>[0=>12], 'res'=>false],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>['name'=>['like'=>'????Sky%']], 'res'=>true],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>['name'=>['like'=>'%Sky%']], 'res'=>true],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>['name'=>['like'=>'%sky%']], 'res'=>false],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>['name'=>['like'=>'Sky%']], 'res'=>false],
			
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>[
			'name'=>['like'=>'Sky%'],
			], 'res'=>false],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>[
			'name'=>['like'=>'Sky%'],
			'@include'=>['name'=>'DeepSkyBlue']
			], 'res'=>true],
			
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>[
			'name'=>['like'=>'%Sky%'],
			], 'res'=>true],
			['row'=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255], 'filter'=>[
			'name'=>['like'=>'%Sky%'],
			'@exclude'=>['name'=>'DeepSkyBlue']
			], 'res'=>false],
			
			['row'=>[0, 191, 255], 'filter'=>[0=>[0, 1, 2, 3]], 'res'=>true],
			['row'=>[0, 191, 255], 'filter'=>[0=>[0, 'lo'=>0]], 'res'=>true],
			['row'=>[0, 191, 255], 'filter'=>[0=>[1, 'lo'=>0]], 'res'=>false],
		];
		foreach ($Tests as $testNum => $test){
			//echo "\nTest #$testNum\n";
			//print_r($test);
			$this->assertEquals(funcArray::simpleFilterOneRow($test['row'], $test['filter']), $test['res']);
		} // end foreach
	} // end testSimpleFilterOneRow
	
	/* public function testSimpleFilterTableVariant_1() { // simpleFilterTableVariant временно недоступен т.к. реализует php7 возможности 
		$Tests=[
			[	'filter'=>[
					[0=>[0], 1=>[191]],
					[0=>[138], 1=>[43]],
					['name'=>'azure4']
				],
				'result'=>[
					12=>['name'=>'DeepSkyBlue', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255],
					13=>['name'=>'DeepSkyBlue1', 'hex'=>'#00BFFF', 'rgb'=>'0,191,255', 0, 191, 255],
					1=>['name'=>'BlueViolet', 'hex'=>'#8A2BE2', 'rgb'=>'138,43,226', 138, 43, 226],
					84=>['name'=>'azure4', 'hex'=>'#838B8B', 'rgb'=>'131,139,139', 131, 139, 139],
				],
			],
			[
				'filter'=>[
					['name'=>['like'=>'turqu%']],
					['name'=>['like'=>'cyan%']],
				],
				'result'=>[
					90=>['name'=>'cyan', 'hex'=>'#00FFFF', 'rgb'=>'0,255,255', 0, 255, 255],
					91=>['name'=>'cyan1', 'hex'=>'#00FFFF', 'rgb'=>'0,255,255', 0, 255, 255],
					92=>['name'=>'cyan2', 'hex'=>'#00EEEE', 'rgb'=>'0,238,238', 0, 238, 238],
					93=>['name'=>'cyan3', 'hex'=>'#00CDCD', 'rgb'=>'0,205,205', 0, 205, 205],
					94=>['name'=>'cyan4', 'hex'=>'#008B8B', 'rgb'=>'0,139,139', 0, 139, 139],
					96=>['name'=>'turquoise', 'hex'=>'#40E0D0', 'rgb'=>'64,224,208', 64, 224, 208],
					97=>['name'=>'turquoise1', 'hex'=>'#00F5FF', 'rgb'=>'0,245,255', 0, 245, 255],
					98=>['name'=>'turquoise2', 'hex'=>'#00E5EE', 'rgb'=>'0,229,238', 0, 229, 238],
					99=>['name'=>'turquoise3', 'hex'=>'#00C5CD', 'rgb'=>'0,197,205', 0, 197, 205],
					100=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139],
				],
			],
		];
		foreach ($Tests as $numTest => $test) {
			$this->assertEquals(funcArray::simpleFilterTableVariant($this->arTable, ...$test['filter']), $test['result']);
		}
	} // end func
	*/
	
	public function testEqRow() {
		$Tests=[
			[
				'row1'=>[],
				'row2'=>[],
				'ignore'=>[],
				'result'=>true,
			],
			[
				'row1'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139],
				'row2'=>['hex'=>'#00868B', 'name'=>'turquoise4', 0, 'rgb'=>'0,134,139', 134, 139],
				'ignore'=>[],
				'result'=>true,
			],
			[
				'row1'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139],
				'row2'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139, 'desc'=>'Hello'],
				'ignore'=>['desc'],
				'result'=>true,
			],
			[
				'row1'=>['name'=>'turquoise4', 'rgb'=>'0,134,139', 'hex'=>'#00868B', 0, 134, 139 ,'desc'=>'Hello'],
				'row2'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139, 'text'=>'test'],
				'ignore'=>['desc', 'text'],
				'result'=>true,
			],
			[
				'row1'=>['name'=>'turquoise4', 139, 'rgb'=>'0,134,139', 'hex'=>'#00868B', 0, 134, 'desc'=>'Hello'],
				'row2'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139, 'text'=>'test'],
				'ignore'=>[],
				'result'=>false,
			],
			[
				'row1'=>['name'=>'turquoise4', 'hex'=>'#00868B', 'rgb'=>'0,134,139', 0, 134, 139],
				'row2'=>['hex'=>'#00868B', 'name'=>'turquoise5', 0, 'rgb'=>'0,134,139', 134, 139],
				'ignore'=>[],
				'result'=>false,
			],
			[
				'row1'=>['name'=>'turquoise4', 0, 134, 139],
				'row2'=>['hex'=>'#00868B', 'name'=>'turquoise4', 0, 'rgb'=>'0,134,139', 134, 139],
				'ignore'=>[],
				'result'=>false,
			],
			
		];
		foreach ($Tests as $numTest => $test) {
			//echo "$numTest\r\n";
			$this->assertEquals(funcArray::eqRow($test['row1'], $test['row2'], $test['ignore']), $test['result']);
		}
	} // end testEqRow
	
	public function testEqTables() {
		$Tests=[
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>[],
				'orderstrict'=>false,
				'result'=>true,
			],
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>[],
				'orderstrict'=>false,
				'result'=>true,
			],
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>9, 1=>92, 'name'=>'Test ###'],
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
				],
				'ignore'=>[],
				'orderstrict'=>true,
				'result'=>false,
			],
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #', 'desc'=>''],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>[],
				'orderstrict'=>false,
				'result'=>false,
			],
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #', 'desc'=>''],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>['desc'],
				'orderstrict'=>false,
				'result'=>true,
			],
			[
				'table1'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #', 'desc'=>''],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'table2'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>['desc'],
				'orderstrict'=>true,
				'result'=>true,
			],
			[
				'table1'=>[
					[0=>9, 1=>92, 'name'=>'Test ###'],
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #', 'desc'=>''],
				],
				'table2'=>[
					[0=>1, 1=>2, 'name'=>'Test'],
					[0=>10, 1=>87, 'name'=>'Test #'],
					[0=>9, 1=>92, 'name'=>'Test ###'],
				],
				'ignore'=>['desc'],
				'orderstrict'=>true,
				'result'=>false,
			],
		];
		foreach ($Tests as $numTest => $test) {
			//echo "$numTest\r\n";
			$this->assertEquals(funcArray::eqTable($test['table1'], $test['table2'], $test['ignore'], $test['orderstrict']), $test['result']);
		}
	} // end TestEqTables
	
	public function testCallableArray() {
		$Tests=[
			['input'=>[], 'res'=>[]],
			['input'=>[1, 2, 3, 'строка'], 'res'=>[1, 2, 3, 'строка']],
			['input'=>['key'=>1, 'key_2'=>2, 'k10'=>'строка'], 'res'=>[0=>1, 1=>2, 2=>'строка']],
			['input'=>[0=>1, 'key_2'=>2, function () { return 100;}, 'k10'=>'строка'], 'res'=>[0=>1, 1=>2, 2=>100, 3=>'строка']],
			['input'=>[0=>1, 'key_2'=>2, function () { return [100, 101];}, 'k10'=>'строка'], 'res'=>[0=>1, 1=>2, 2=>100, 3=>101, 4=>'строка']],
		];
		foreach ($Tests as $numTest => $test) {
			//echo "#$numTest";
			$this->assertEquals(funcArray::callableArray($test['input']), $test['res']);
		}
	} // end testCallableArray
	
	public function testConvertConvertSimpleFilterLine() {
		$Tests=[
			['input'=>['id_100', 'value_hello'], 'valid_fields'=>['id', 'value'], 'res'=>['id'=>100, 'value'=>'hello']],
			['input'=>['id_100', 'value_hello dolly', 'id_101'], 'valid_fields'=>['id', 'value'], 'res'=>['id'=>[100, 101], 'value'=>'hello dolly']],
			['input'=>['id_100', 'value_hello_dolly', 'id_101'], 'valid_fields'=>['id', 'value'], 'res'=>['id'=>[100, 101], 'value'=>'hello_dolly']],
			['input'=>['id_100', 'value_*hello', 'value_do??y', 'id_101'], 'valid_fields'=>['id', 'value'], 'res'=>['id'=>[100, 101], 'value'=>[['like'=>'%hello', 'String' => '*hello'],['like'=>'do??y', 'String' => 'do??y']]]],
		];
		foreach ($Tests as $numTest => $test) {
			$arSimpleFilter=funcArray::convertConvertSimpleFilterLine($test['input'], $test['valid_fields']);
			var_dump($arSimpleFilter);
			$this->assertTrue(funcArray::eqRow($arSimpleFilter, $test['res']));
		}
		
	} // end testConvertConvertSimpleFilterLine

	
} // end class