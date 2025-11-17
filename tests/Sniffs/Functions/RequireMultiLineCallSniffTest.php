<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\TestCase;
use Throwable;

class RequireMultiLineCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineCallErrors.php');

		self::assertSame(15, $report->getErrorCount());

		self::assertSniffError(
			$report,
			7,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of method doAnything() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			9,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function sprintf() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			11,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Constructor call should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			15,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function printf() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			17,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function array_merge() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			21,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function array_map() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			24,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function array_merge() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			29,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function doSomethingElse() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			31,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function in_array() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			33,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function sprintf() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			36,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of method doSomething() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			44,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of method doNowOrAfterCommit() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			45,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of method sendDelayedMessage() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			51,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function sprintf() should be split to more lines.',
		);
		self::assertSniffError(
			$report,
			54,
			RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL,
			'Call of function _() should be split to more lines.',
		);

		self::assertAllFixedInFile($report);
	}

	public function testAllCallsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineCallAllCallsNoErrors.php', [
			'minLineLength' => 0,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testAllCallsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineCallAllCallsErrors.php', [
			'minLineLength' => 0,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 7, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);
		self::assertSniffError($report, 12, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionOnInvalidSetup(): void
	{
		$this->expectException(Throwable::class);

		self::checkFile(
			__DIR__ . '/data/requireMultiLineCallAllCallsNoErrors.php',
			['minLineLength' => 100, 'minParametersCount' => 2],
		);
	}

	public function testErrorsBasedOnParamCount(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/requireMultiLineCallParamCountErrors.php',
			[
				'minParametersCount' => 2,
			],
		);
		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 8, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);
		self::assertSniffError($report, 14, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);

		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForInvalidPattern(): void
	{
		$this->expectException(Throwable::class);

		self::checkFile(
			__DIR__ . '/data/requireMultiLineCallErrors.php',
			['includedCallPatterns' => ['invalidPattern']],
		);

		self::checkFile(
			__DIR__ . '/data/requireMultiLineCallErrors.php',
			['excludedCallPatterns' => ['invalidPattern']],
		);
	}

	public function testExcludedCallPatterns(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineCallExcludedCallsErrors.php', [
			'minLineLength' => 0,
			'excludedCallPatterns' => ['/dontReportError/'],
		], [RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 7, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);
		self::assertSniffError($report, 13, RequireMultiLineCallSniff::CODE_REQUIRED_MULTI_LINE_CALL);

		self::assertAllFixedInFile($report);
	}

}
