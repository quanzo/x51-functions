<?php
namespace x51\tests\functions;
	use x51\functions\funcFileSystem;
	
class funcFileSystemTest extends \PHPUnit\Framework\TestCase{
	
	public function testNormalizePath() {
		$Tests=[
			['path'=>'', 'result'=>''],
			['path'=>'var', 'result'=>'var'],
			['path'=>'/var/www/site1.local/cms/front-end/js/', 'result'=>'/var/www/site1.local/cms/front-end/js/'],
			['path'=>'/var/www/site1.local\.\cms\front-end/js/', 'result'=>'/var/www/site1.local/cms/front-end/js/'],
			['path'=>'/var/www/site1.local/cms/../front-end/../js/', 'result'=>'/var/www/site1.local/js/'],
			['path'=>'/var/www/site1.local/cms\../front-end\..\js/', 'result'=>'/var/www/site1.local/js/'],
			['path'=>'/var/www/site1.local/cms/../../../front-end/js/', 'result'=>'/var/front-end/js/'],
			['path'=>'/var/www/site1.local/cms/../../../../../front-end/js/script.js', 'result'=>'front-end/js/script.js'],
			['path'=>'', 'result'=>''],
		];
		foreach ($Tests as $testNum => $test) {
			//echo "#$testNum\n";
			//echo funcFileSystem::normalizePath($test['path'])."\n";
			$this->assertEquals(funcFileSystem::normalizePath($test['path']), $test['result']);
		}
	} // end NormalizePath
	
	public function testNormalizeDir() {
		$Tests=[
			['path'=>'', 'result'=>'/'],
			['path'=>'var', 'result'=>'var/'],
			['path'=>'/var/www/site1.local/cms/front-end/js/', 'result'=>'/var/www/site1.local/cms/front-end/js/'],
			['path'=>'/var/www/site1.local\.\cms\front-end/js/', 'result'=>'/var/www/site1.local/cms/front-end/js/'],
			['path'=>'/var/www/site1.local/cms/../front-end/../js/', 'result'=>'/var/www/site1.local/js/'],
			['path'=>'/var/www/site1.local/cms\../front-end\..\js', 'result'=>'/var/www/site1.local/js/'],
			['path'=>'/var/www/site1.local/cms/../../../front-end/js', 'result'=>'/var/front-end/js/'],
			['path'=>'/var/www/site1.local/cms/../../../../../front-end/js/', 'result'=>'front-end/js/'],
			['path'=>'', 'result'=>'/'],
		];
		foreach ($Tests as $testNum => $test) {
			$this->assertEquals(funcFileSystem::normalizeDir($test['path']), $test['result']);
		}
	} // end NormalizePath
	
	public function testCssUrlCorrection() {
		$Tests=[
			['input'=>'background-image: url(/images/1.jpg);', 'file'=>'/css/style.css', 'result'=>'background-image: url("/images/1.jpg");'],
			['input'=>'background-image: url(../images/1.jpg);', 'file'=>'/css/style.css', 'result'=>'background-image: url("/images/1.jpg");'],
			['input'=>'background-image: url(images/1.jpg);', 'file'=>'/css/style.css', 'result'=>'background-image: url("/css/images/1.jpg");'],
			['input'=>'background-image: url(./images/1.jpg);', 'file'=>'/css/style.css', 'result'=>'background-image: url("/css/images/1.jpg");'],
		];
		foreach ($Tests as $testNum => $test) {
			$this->assertEquals(funcFileSystem::cssUrlCorrection($test['input'], $test['file']), $test['result']);
		}
	} // end testCssUrlCorrection
	
	public function testRelativePath() {
		$Tests=[
			['path'=>'', 'rel'=>'', 'result'=>''],
			['path'=>'/home/user/doc/1/', 'rel'=>'/home/', 'result'=>'/user/doc/1/'],
			['path'=>'/home/user/doc/1/', 'rel'=>'/home', 'result'=>'/user/doc/1/'],
			['path'=>'/home/user/doc/1/', 'rel'=>'/user', 'result'=>false],
		];
		foreach ($Tests as $testNum => $test) {
			$this->assertEquals(funcFileSystem::relativePath($test['path'], $test['rel']), $test['result']);
		}
		
	} // end testRelativePath

}