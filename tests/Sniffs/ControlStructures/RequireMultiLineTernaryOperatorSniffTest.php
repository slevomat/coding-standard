<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireMultiLineTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineTernaryOperatorNoErrors.php', [
			'lineLengthLimit' => 80,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineTernaryOperatorErrors.php', [
			'lineLengthLimit' => 80,
		]);

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 7, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 9, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 11, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 13, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);
		self::assertSniffError($report, 15, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorRecurrence(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineTernaryOperatorCloseTagNoNewline.php', [
			'lineLengthLimit' => 120,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWithExpressionMinLength(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineTernaryOperatorWithExpressionMinLengthNoErrors.php', [
			'lineLengthLimit' => 30,
			'minExpressionsLength' => 10,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithExpressionMinLength(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineTernaryOperatorWithExpressionMinLengthErrors.php', [
			'lineLengthLimit' => 30,
			'minExpressionsLength' => 10,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireMultiLineTernaryOperatorSniff::CODE_MULTI_LINE_TERNARY_OPERATOR_NOT_USED);

		self::assertAllFixedInFile($report);
	}

}
