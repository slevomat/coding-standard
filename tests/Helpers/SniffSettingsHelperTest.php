<?php declare(strict_types = 1);

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

	public function testNormalizeAssociativeArray()
	{
		$this->assertSame([
			'app/ui' => 'Slevomat\UI',
			'app' => 'Slevomat',
			'build/SlevomatSniffs/Sniffs' => 'SlevomatSniffs\Sniffs',
			'tests/ui' => 'Slevomat\UI',
			'tests' => 'Slevomat',
		], SniffSettingsHelper::normalizeAssociativeArray([
			'    ' => '   ',
			'app/ui' => 'Slevomat\UI',
			'  ' => '  ',
			'app' => 'Slevomat',
			'  ' => '  ',
			'build/SlevomatSniffs/Sniffs' => 'SlevomatSniffs\Sniffs',
			'  ' => '  ',
			'tests/ui' => 'Slevomat\UI',
			'  ' => '  ',
			'tests' => 'Slevomat',
			'  ' => '  ',
		]));
	}

}
