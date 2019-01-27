<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class UselessTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/uselessTernaryOperatorErrors.php');

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 4, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 8, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 12, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 16, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 20, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 24, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 30, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 36, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 43, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);
		self::assertSniffError($report, 49, UselessTernaryOperatorSniff::CODE_USELESS_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

}
