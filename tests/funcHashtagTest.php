<?php
namespace x51\tests\functions;
	use x51\functions\funcHashtag;
	
class funcHashtagTest extends \PHPUnit\Framework\TestCase{
	
	public function testGetHashtags() {
		$Tests=[
			['message'=>'', 'result'=>array()],
			['message'=>true, 'result'=>array()],
			['message'=>false, 'result'=>array()],
			['message'=>'бот генерирует стикеры, вы пишите #текст он подбирает #стикер и вставляет текст', 'result'=>array('текст', 'стикер')],
			['message'=>'бот генерирует стикеры, вы пишите #текст#хорошийтекст он подбирает #стикер и вставляет текст', 'result'=>array('текст', 'хорошийтекст', 'стикер')],
			['message'=>'бот генерирует стикеры, вы пишите #текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст', 'result'=>array('текст', 'хорошийтекст', 'text', 'good', 'стикер')],
			['message'=>'бот генерирует стикеры, вы пишите #Текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст', 'result'=>array('Текст', 'хорошийтекст', 'text', 'good', 'стикер')],
		];
		foreach ($Tests as $test) {
			$this->assertEquals(funcHashtag::getHashtags($test['message']), $test['result']);
		}
	} // end testGetHashtags
	
	public function testHashtag2link() {
		$Tests=[
			['message'=>'', 'result'=>''],
			['message'=>true, 'result'=>true],
			['message'=>false, 'result'=>false],
			['message'=>'bot gen #good', 'result'=>'bot gen <a href="https://www.facebook.com/hashtag/good" class="hashtag">#good</a>'],
			['message'=>'бот генерирует стикеры, вы пишите #Текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст', 'result'=>'бот генерирует стикеры, вы пишите <a href="https://www.facebook.com/hashtag/Текст" class="hashtag">#Текст</a><a href="https://www.facebook.com/hashtag/хорошийтекст" class="hashtag">#хорошийтекст</a><a href="https://www.facebook.com/hashtag/text" class="hashtag">#text</a> <a href="https://www.facebook.com/hashtag/good" class="hashtag">#good</a> он подбирает <a href="https://www.facebook.com/hashtag/стикер" class="hashtag">#стикер</a> и вставляет текст'],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(funcHashtag::hashtag2link($test['message'], 'https://www.facebook.com/hashtag/#hashtag'), $test['result']);
		}
	} // end testGetHashtags
	
	public function testReplaceHashtagPattern() {
		$Tests=[
			['message'=>'', 'result'=>''],
			['message'=>true, 'result'=>true],
			['message'=>false, 'result'=>false],
			['message'=>'bot gen #good', 'result'=>'bot gen {good}'],
			['message'=>'бот генерирует стикеры, вы пишите #Текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст', 'result'=>'бот генерирует стикеры, вы пишите {Текст}{хорошийтекст}{text} {good} он подбирает {стикер} и вставляет текст'],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(funcHashtag::replaceHashtagPattern($test['message'], '{#hashtag}'), $test['result']);
		}
	} // end testGetHashtags
	
	public function testCheckTag() {
		$Tests=[
			['message'=>'', 'tag'=>'good', 'result'=>false],
			['message'=>true, 'tag'=>'good', 'result'=>false],
			['message'=>false, 'tag'=>'good', 'result'=>false],
			['message'=>'bot gen #good, ', 'tag'=>'good', 'result'=>true],
			['message'=>'bot gen #good#wood#text', 'tag'=>'good', 'result'=>true],
			['message'=>'bot gen #god#wood#text', 'tag'=>'good', 'result'=>false],
			['message'=>'бот генерирует стикеры, вы пишите #Текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст',  'tag'=>'good', 'result'=>true],
		];
		foreach ($Tests as $num => $test) {
			//echo "$num\r\n";
			$this->assertEquals(funcHashtag::checkTag($test['message'], $test['tag']), $test['result']);
		}
	} // end testCheckTag
	
	public function testCheckTags() {
		$Tests=[
			['message'=>'', 'tag'=>[], 'result'=>false],
			['message'=>true, 'tag'=>['good'], 'result'=>false],
			['message'=>false, 'tag'=>['good'], 'result'=>false],
			['message'=>false, 'tag'=>[], 'result'=>false],
			['message'=>'bot gen #good', 'tag'=>['good'], 'result'=>true],
			['message'=>'bot gen #good', 'tag'=>['good', 'wood'], 'result'=>false],
			['message'=>'bot gen #good#wood#text', 'tag'=>['good', 'wood'], 'result'=>true],
			['message'=>'bot gen #good#wood#text', 'tag'=>[], 'result'=>false],
			['message'=>'bot gen #god#wood#text', 'tag'=>['good', 'wood'], 'result'=>false],
			['message'=>'bot gen #god#wood#text', 'tag'=>['god', 'wood', 'text'], 'result'=>true],
			['message'=>'бот генерирует стикеры, вы пишите #Текст#хорошийтекст#text #good он подбирает #стикер и вставляет текст',  'tag'=>['good', 'text'], 'result'=>true],
		];
		foreach ($Tests as $num => $test) {
			$this->assertEquals(funcHashtag::checkTags($test['message'], $test['tag']), $test['result']);
		}
	} // end testCheckTags
	
} // end class