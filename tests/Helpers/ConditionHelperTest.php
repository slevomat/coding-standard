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
			[47, true],
			[135, false],
			[139, true],
			[143, false],
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
			[103, '!($a + ($b ?? 0))'],
			[107, '!($a + $b)'],
			[111, '!($a . $b)'],
			[115, '!$foo[\'bar\']'],
			[119, '!$foo->bar'],
			[123, '!Foo::$bar'],
			[127, '!Foo::bar()'],
			[131, '!\is_string($a)'],
			[135, '!($override ?? [0 => $a] === [0 => 1])'],
			[139, '($override ?? [0 => $a]) !== [0 => 1]'],
			[143, '!($a ? $b === 1 : false)'],
			[147, '!$foo->{$bar}'],
			[151, '!$foo->$bar'],
			[155, '!self::$bar'],
			[159, '!isset($a)'],
			[163, '!(\'str\' . $b)'],
			[167, '!$a || !in_array($x, $list)'],
			[171, '!($a && $b || $c)'],
			[175, '( $a !== 1 )'],
			[179, '!$object->$$name'],
			[183, '!$object->${$name}'],
			[187, '!Foo::$$name'],
			[191, '!$a !== $b'],
			[195, '!($a === 1 | ($b ?? 0))'],
			[199, '!($a === 1 ^ ($b ?? 0))'],
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

		self::assertTrue(
			ConditionHelper::conditionReturnsBoolean(
				$phpcsFile,
				$tokens[$ifPointer]['parenthesis_opener'] + 1,
				$tokens[$ifPointer]['parenthesis_closer'] - 1,
			),
		);

		$ifPointerLogical = $this->findPointerByLineAndType($phpcsFile, 7, T_IF);

		self::assertSame(
			'!$a || (match ($x) { default => 1 } === null)',
			ConditionHelper::getNegativeCondition(
				$phpcsFile,
				$tokens[$ifPointerLogical]['parenthesis_opener'] + 1,
				$tokens[$ifPointerLogical]['parenthesis_closer'] - 1,
			),
		);
	}

	/**
	 * Verifies that ConditionHelper::getNegativeCondition handles unclosed curly brackets gracefully during live coding.
	 */
	public function testUnclosedCurlyBracket(): void
	{
		$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/conditionsWithUnclosedCurly.php');
		$tokens = $phpcsFile->getTokens();

		$ifPointer = $this->findPointerByLineAndType($phpcsFile, 3, T_IF);

		self::assertSame(
			'$a { $b !== 1',
			ConditionHelper::getNegativeCondition(
				$phpcsFile,
				$tokens[$ifPointer]['parenthesis_opener'] + 1,
				$tokens[$ifPointer]['parenthesis_closer'] - 1,
			),
		);

		$ifInvalidMemberPointer = $this->findPointerByLineAndType($phpcsFile, 7, T_IF);

		self::assertSame(
			'!($object->123)',
			ConditionHelper::getNegativeCondition(
				$phpcsFile,
				$tokens[$ifInvalidMemberPointer]['parenthesis_opener'] + 1,
				$tokens[$ifInvalidMemberPointer]['parenthesis_closer'] - 1,
			),
		);
	}

}
