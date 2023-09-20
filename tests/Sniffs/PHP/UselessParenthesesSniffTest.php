<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessParenthesesSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParenthesesNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParenthesesErrors.php');

		self::assertSame(41, $report->getErrorCount());

		foreach ([8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 36, 37, 38, 41, 42, 43, 44, 49, 53, 55, 56, 57, 58, 64] as $line) {
			self::assertSniffError($report, $line, UselessParenthesesSniff::CODE_USELESS_PARENTHESES);
		}

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithIgnoredComplexTernaryConditions(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParenthesesNoErrorsWithIgnoredComplexTernaryConditions.php', [
			'ignoreComplexTernaryConditions' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsLiveCoding(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessParenthesesLiveCoding.php');
		self::assertNoSniffErrorInFile($report);
	}

}
