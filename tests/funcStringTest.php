<?php
namespace x51\tests\functions;
	use x51\functions\funcString;
	
class funcStringTest extends \PHPUnit\Framework\TestCase{
	
	public function testLmvSubstr() {
		$Tests=[
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>0, 'length'=>0, 'result'=>''],
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>0, 'length'=>26, 'result'=>'qwertyuiopasdfghjklzxcvbnm'],
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>0, 'length'=>30, 'result'=>'qwertyuiopasdfghjklzxcvbnm'],
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>3, 'length'=>3, 'result'=>'rty'],
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>-1, 'length'=>5, 'result'=>''],
			['stack'=>'qwertyuiopasdfghjklzxcvbnm', 'start'=>0, 'length'=>-5, 'result'=>''],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(
				funcString::lmv_substr($test['stack'], $test['start'], $test['length']),
				$test['result']
			);
		}
	} // end testLmvSubstr
	
	public function testLmvStrpos() {
		$Tests=[
			['stack'=>'qwerty qwerty qwerty   qwerty', 'needle' => 'qwe', 'start'=>0, 'result'=>0],
			['stack'=>'qwerty qwerty qwerty   qwerty', 'needle' => 'uiop', 'start'=>0, 'result'=>false],
			['stack'=>'qwerty qwerty qwerty   qwerty', 'needle' => 'qwe', 'start'=>1, 'result'=>7],
			['stack'=>'qwerty qwerty qwerty   qwerty', 'needle' => 'qwe', 'start'=>-7, 'result'=>false],
		];
		foreach ($Tests as $num => $test) {
			//echo "\n$num\n";
			$this->assertEquals(
				funcString::lmv_pos($test['stack'], $test['needle'], $test['start']),
				$test['result']
			);
		}
	} // end testLmvSubstr
	
	public function testMultiReplace() {
		$Tests=[
			['search'=>'', 'replace'=>'a', 'subject'=>'testing stringer', 'result'=>'testing stringer'],
			['search'=>'  ', 'replace'=>' ', 'subject'=>'     testing       stringer', 'result'=>' testing stringer'],
			['search'=>'aa', 'replace'=>'aab', 'subject'=>'aaaaaaaaaa', 'result'=>'aabaabaabaabaab'],
			['search'=>['aa', 'b'], 'replace'=>'aab', 'subject'=>['aaaaaaaaaa', 'bbbbbbbbbb'], 'result'=>['aaaabaaaabaaaabaaaabaaaab', 'aabaabaabaabaabaabaabaabaabaab']],
			['search'=>'aa', 'replace'=>'a', 'subject'=>'aaaaaaaaaa', 'result'=>'a'],
			['search'=>['aa', 'a'], 'replace'=>['a', 'b'], 'subject'=>['aaaaaaaaaa', 'aaaacaaaac'], 'result'=>['b', 'bcbc']],
			
		];
		foreach ($Tests as $num => $test) {
			//echo "\nfuncStringTest: $num\n";
			//echo funcString::multiReplace($test['search'], $test['replace'], $test['subject'])."\n";
			$this->assertEquals(
				funcString::multiReplace($test['search'], $test['replace'], $test['subject']),
				$test['result']
			);
		}
	} // end testMultiReplace
	
	public function testFindFirst() {
		$Tests = [
			['haystack' => 'a, b, c', 'needle' => [' ', ','], 'offset'=>0, 'result'=>1],
			['haystack' => 'a, b, c', 'needle' => [',', ' '], 'offset'=>0, 'result'=>1],
			['haystack' => 'a, b, c', 'needle' => [' ', ','], 'offset'=>2, 'result'=>2],
			['haystack' => 'abc', 'needle' => [' ', ','], 'offset'=>1, 'result'=>false],
			['haystack' => '', 'needle' => [' ', ','], 'offset'=>1, 'result'=>false],
			['haystack' => '', 'needle' => [' ', ','], 'offset'=>0, 'result'=>false],
			['haystack' => [], 'needle' => [' ', ','], 'offset'=>1, 'result'=>false],
			['haystack' => 'a, b, c', 'needle' => [' ', ','], 'offset'=>5, 'result'=>5],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(
				funcString::findFirst($test['haystack'], $test['needle'], $test['offset']),
				$test['result']
			);
		}
	}
	
	public function testExplode() {
		$Tests = [
			['haystack' => 'a, b, c', 'needle' => [' ', ','], 'cleanup'=>false, 'result'=>['a', '', 'b', '', 'c']],
			['haystack' => 'a, b, c', 'needle' => [' ', ','], 'cleanup'=>true, 'result'=>['a', 'b', 'c']],
			['haystack' => 'a', 'needle' => [' ', ','], 'cleanup'=>true, 'result'=>['a']],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(
				funcString::explode($test['needle'], $test['haystack'], $test['cleanup']),
				$test['result']
			);
		}
	}
	
	
} // end funcStringTest