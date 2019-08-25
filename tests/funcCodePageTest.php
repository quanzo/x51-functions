<?php
namespace x51\tests\functions;
	use x51\functions\funcCodePage;
	
class funcCodePageTest extends \PHPUnit\Framework\TestCase{
	
	public function testTranslit() {
		$Tests=[
			['input'=>'Привет', 'result'=>'Privet'],
			['input'=>['Привет', 'Солнце'], 'result'=>['Privet', 'Solntse']],
			['input'=>'', 'result'=>''],
			['input'=>['Привет'], 'result'=>['Privet']],
			['input'=>'Hello', 'result'=>'Hello'],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(
				funcCodePage::translit($test['input']),
				$test['result']
			);
		}
	} // testTranslit
	
	
	
} // enc test