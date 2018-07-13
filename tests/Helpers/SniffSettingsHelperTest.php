<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SniffSettingsHelperTest extends TestCase
{

	public function testNormalizeInteger(): void
	{
		self::assertSame(2, SniffSettingsHelper::normalizeInteger(2));
		self::assertSame(2, SniffSettingsHelper::normalizeInteger('2'));
		self::assertSame(2, SniffSettingsHelper::normalizeInteger('  2  '));
	}

	public function testNormalizeArray(): void
	{
		self::assertSame([
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
		self::assertSame([
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
		self::assertSame([
			'app/ui' => 'Slevomat\UI',
			'app' => 'Slevomat',
		], SniffSettingsHelper::normalizeAssociativeArray([
			'app/ui' => 'Slevomat\UI',
			'app' => 'Slevomat',
			0 => '  ',
		]));
	}

	/**
	 * @dataProvider validRegularExpressionsProvider
	 * @dataProvider invalidRegularExpressionsProvider
	 * @param string $expression
	 * @param bool $valid
	 */
	public function testIsValidRegularExpression(string $expression, bool $valid): void
	{
		self::assertSame($valid, SniffSettingsHelper::isValidRegularExpression($expression));
	}

	/**
	 * @return mixed[][]
	 */
	public function validRegularExpressionsProvider(): array
	{
		return [
			['~abc~', true],
			['~abc~Aix', true],
			['(hello)', true],
			['{hello}', true],
			['[hello]', true],
			['##u', true],
			['#some(more|coml[ea]?x)pattern#ui', true],
		];
	}

	/**
	 * @return mixed[][]
	 */
	public function invalidRegularExpressionsProvider(): array
	{
		return [
			['abc', false],
			['aregexpa', false],
			['~abc', false],
			['\\abc\\', false],
		];
	}

}
