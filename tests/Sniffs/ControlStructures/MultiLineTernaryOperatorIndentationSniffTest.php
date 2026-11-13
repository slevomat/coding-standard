<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

/**
 * Tests for SlevomatCodingStandard\Sniffs\ControlStructures\MultiLineTernaryOperatorIndentationSniff.
 */
class MultiLineTernaryOperatorIndentationSniffTest extends TestCase
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

		self::assertSniffError($report, 4, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 9, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 13, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 17, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 21, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 25, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 29, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 33, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 41, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 50, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 51, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 54, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 55, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 58, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 62, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 66, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);
		self::assertSniffError($report, 70, MultiLineTernaryOperatorIndentationSniff::CODE_INVALID_MULTI_LINE_TERNARY_OPERATOR_INDENTATION);

		self::assertAllFixedInFile($report);
	}

}
