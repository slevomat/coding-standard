<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Whitespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

/**
 * Tests for SlevomatCodingStandard\Sniffs\Whitespaces\MultiLineOperatorIndentationSniff.
 */
class MultiLineOperatorIndentationSniffTest extends TestCase
{

	/**
	 * Verifies that valid single-line and multiline operator expressions produce no sniff errors.
	 */
	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/../Operators/data/multiLineOperatorIndentationNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	/**
	 * Verifies that trailing multiline operators and improperly indented leading operators are reported
	 * and automatically fixed.
	 */
	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/../Operators/data/multiLineOperatorIndentationErrors.php');

		self::assertSame(17, $report->getErrorCount());

		self::assertSniffError($report, 5, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 6, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 7, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 9, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 10, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 15, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 16, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 23, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 28, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 29, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 30, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 38, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 39, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 46, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 55, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 59, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);
		self::assertSniffError($report, 62, MultiLineOperatorIndentationSniff::CODE_INVALID_OPERATOR_INDENTATION);

		self::assertAllFixedInFile($report);
	}

}
