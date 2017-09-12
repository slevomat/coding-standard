<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SniffSettingsHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testNormalizeInteger(): void
	{
		$this->assertSame(2, SniffSettingsHelper::normalizeInteger(2));
		$this->assertSame(2, SniffSettingsHelper::normalizeInteger('2'));
		$this->assertSame(2, SniffSettingsHelper::normalizeInteger('  2  '));
	}

	public function testNormalizeArray(): void
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

	public function testNormalizeAssociativeArray(): void
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
			'   ' => '  ',
			'app' => 'Slevomat',
			'  ' => '  ',
			'build/SlevomatSniffs/Sniffs' => 'SlevomatSniffs\Sniffs',
			'      ' => '  ',
			'tests/ui' => 'Slevomat\UI',
			' ' => '  ',
			'tests' => 'Slevomat',
			'' => '  ',
		]));
	}

	public function testNormalizeAssociativeArrayWithIntegerKeys(): void
	{
		$this->assertSame([
			'app/ui' => 'Slevomat\UI',
			'app' => 'Slevomat',
		], SniffSettingsHelper::normalizeAssociativeArray([
			'app/ui' => 'Slevomat\UI',
			'app' => 'Slevomat',
			0 => '  ',
		]));
	}

}
