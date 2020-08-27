<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireSingleLineCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineCallErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of method doAnything() should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			12,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of function sprintf() should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			19,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Constructor call should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			25,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of function printf() should be placed on a single line.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testForAllCallsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineCallAllCallsErrors.php', [
			'maxLineLength' => 0,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 7, RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL);

		self::assertAllFixedInFile($report);
	}

	public function testWithComplexParametersEnabledErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineCallWithComplexParametersEnabledErrors.php', [
			'ignoreWithComplexParameter' => false,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of method doSomething() should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			16,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of method doAnything() should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			20,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Constructor call should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			25,
			RequireSingleLineCallSniff::CODE_REQUIRED_SINGLE_LINE_CALL,
			'Call of method doWhatever() should be placed on a single line.'
		);

		self::assertAllFixedInFile($report);
	}

}
