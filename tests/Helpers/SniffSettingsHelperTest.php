<?php

namespace SlevomatCodingStandard\Helpers;

class SniffSettingsHelperTest extends \PHPUnit_Framework_TestCase
{

	public function testNormalizeArray()
	{
		$this->assertSame([
			'Foo\Bar\BarException',
			'Foo\Bar\BazException',
		], SniffSettingsHelper::normalizeArray([
			'    ',
			'Foo\Bar\BarException',
			'  ',
			' Foo\Bar\BazException  ',
			'',
			'  ',
		]));
	}

}
