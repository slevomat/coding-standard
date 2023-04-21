<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

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
				$tokens[$ifPointer]['parenthesis_closer'] - 1
			)
		);
	}

}
