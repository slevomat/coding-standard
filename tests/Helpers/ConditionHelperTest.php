<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use const PHP_VERSION_ID;
use const T_IF;

class ConditionHelperTest extends TestCase
{

	/**
	 * @return list<array{0: int, 1: bool}>
	 */
	public static function dataConditionReturnsBoolean(): array
	{
		return [
			[3, false],
			[7, false],
			[11, true],
			[15, true],
			[19, true],
			[23, false],
			[27, true],
			[31, true],
			[35, true],
		];
	}

	/**
	 * @dataProvider dataConditionReturnsBoolean
	 */
	#[DataProvider('dataConditionReturnsBoolean')]
	public function testConditionReturnsBoolean(int $line, bool $result): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/conditions.php');
		$tokens = $phpcsFile->getTokens();

		$ifPointer = $this->findPointerByLineAndType($phpcsFile, $line, T_IF);

		self::assertSame(
			$result,
			ConditionHelper::conditionReturnsBoolean(
				$phpcsFile,
				$tokens[$ifPointer]['parenthesis_opener'] + 1,
				$tokens[$ifPointer]['parenthesis_closer'] - 1,
			),
		);
	}

	/**
	 * @return list<array{0: int, 1: string}>
	 */
	public static function dataGetNegativeCondition(): array
	{
		return [
			[31, 'false'],
			[35, 'true'],
			[7, '!$foo->isValid()'],
			[39, '!$flag || ![0 => $a === 1, 1 => \'fallback\']'],
			[43, '![0 => $a === 1, 1 => \'fallback\']'],
			[47, '[0 => $a] !== [0 => $b]'],
			[51, '$a !== [0 => $b === 1]'],
			[55, '![0 => $a && $b]'],
			[59, '(new class () { public function foo(): int { return 1; } }) === null'],
			[63, '$a !== 1'],
			[67, '$a'],
			[71, '$a && $b'],
			[75, '!$a || ($b && $c)'],
			[79, '!($a instanceof Foo)'],
			[83, '!($a & $b)'],
			[87, '!($a ?? $b)'],
			[91, '!($a ? $b : $c)'],
			[95, '!($a xor $b)'],
			[99, '!is_string($a)'],
		];
	}

	/**
	 * Verifies that ConditionHelper::getNegativeCondition correctly inverts conditions
	 * while treating balanced operands atomically.
	 *
	 * @dataProvider dataGetNegativeCondition
	 * @param int $line Line number of the target if condition.
	 * @param string $expected Expected negated condition string.
	 */
	#[DataProvider('dataGetNegativeCondition')]
	public function testGetNegativeCondition(int $line, string $expected): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/conditions.php');
		$tokens = $phpcsFile->getTokens();

		$ifPointer = $this->findPointerByLineAndType($phpcsFile, $line, T_IF);

		self::assertSame(
			$expected,
			ConditionHelper::getNegativeCondition(
				$phpcsFile,
				$tokens[$ifPointer]['parenthesis_opener'] + 1,
				$tokens[$ifPointer]['parenthesis_closer'] - 1,
			),
		);
	}

	/**
	 * Verifies that ConditionHelper::getNegativeCondition correctly handles PHP 8.0 match expressions
	 * by skipping enclosed curly brackets when finding top-level operators.
	 */
	public function testGetNegativeConditionWithMatch(): void
	{
		if (PHP_VERSION_ID < 80000) {
			self::markTestSkipped('PHP 8.0 required.');
		}

		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/conditionsMatch.php');
		$tokens = $phpcsFile->getTokens();

		$ifPointer = $this->findPointerByLineAndType($phpcsFile, 3, T_IF);

		self::assertSame(
			'match ($x) { default => 1 } === null',
			ConditionHelper::getNegativeCondition(
				$phpcsFile,
				$tokens[$ifPointer]['parenthesis_opener'] + 1,
				$tokens[$ifPointer]['parenthesis_closer'] - 1,
			),
		);
	}

}
