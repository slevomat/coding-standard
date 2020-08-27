<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class MethodSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/methodSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/methodSpacingErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 1 blank line after method, found 0.'
		);
		self::assertSniffError(
			$report,
			10,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 1 blank line after method, found 2.'
		);
		self::assertSniffError(
			$report,
			16,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 1 blank line after method, found 0.'
		);
		self::assertSniffError(
			$report,
			24,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 1 blank line after method, found 0.'
		);
		self::assertSniffError(
			$report,
			35,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 1 blank line after method, found 2.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testWithDifferentSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/methodSpacingWithDifferentSettingsNoErrors.php', [
			'minLinesCount' => 2,
			'maxLinesCount' => 4,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithDifferentSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/methodSpacingWithDifferentSettingsErrors.php', [
			'minLinesCount' => 2,
			'maxLinesCount' => 4,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError(
			$report,
			6,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 2 to 4 blank lines after method, found 0.'
		);
		self::assertSniffError(
			$report,
			10,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 2 to 4 blank lines after method, found 5.'
		);
		self::assertSniffError(
			$report,
			19,
			MethodSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
			'Expected 2 to 4 blank lines after method, found 0.'
		);

		self::assertAllFixedInFile($report);
	}

}
