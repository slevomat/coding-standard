<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

/**
 * Tests for SlevomatCodingStandard\Sniffs\ControlStructures\DisallowTrailingMultiLineTernaryOperatorSniff.
 */
class DisallowTrailingMultiLineTernaryOperatorSniffTest extends TestCase
{

	/**
	 * Verifies that valid single-line and multiline ternary expressions produce no sniff errors.
	 */
	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingMultiLineTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * Verifies that trailing ternary operators and improperly indented leading ternary operators
	 * are reported and automatically fixed.
	 */
	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowTrailingMultiLineTernaryOperatorErrors.php');

		self::assertSame(17, $report->getErrorCount());

		self::assertSniffError($report, 4, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 9, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 13, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 17, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 21, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 25, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 29, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 33, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 41, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 50, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 51, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 54, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 55, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 58, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 62, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 66, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);
		self::assertSniffError($report, 70, DisallowTrailingMultiLineTernaryOperatorSniff::CODE_TRAILING_MULTI_LINE_TERNARY_OPERATOR_USED);

		self::assertAllFixedInFile($report);
	}

}
