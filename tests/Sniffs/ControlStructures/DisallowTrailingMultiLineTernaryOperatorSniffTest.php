<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowTrailingMultiLineTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingMultiLineTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingMultiLineTernaryOperatorErrors.php');

		self::assertSame(8, $report->getErrorCount());

		self::assertSniffError($report, 4, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 9, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 13, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 17, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 21, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 25, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 29, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 33, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);

		self::assertAllFixedInFile($report);
	}

}
