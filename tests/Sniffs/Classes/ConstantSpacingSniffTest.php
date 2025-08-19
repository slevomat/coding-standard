<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ConstantSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/constantSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/constantSpacingErrors.php');

		self::assertSame(7, $report->getErrorCount());

		self::assertSniffError($report, 5, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 22, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 26, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 30, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 33, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 36, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 57, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithModifiedLinesCount(): void
	{
		$report = self::checkFile(__DIR__ . '/data/constantSpacingErrors.php', [
			'minLinesCountBeforeWithComment' => 2,
			'maxLinesCountBeforeWithComment' => 2,
			'maxLinesCountBeforeWithoutComment' => 2,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 26, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
	}

	public function testErrorsBasedOnMultiline(): void
	{
		$report = self::checkFile(__DIR__ . '/data/constantSpacingMultilineErrors.php', [
			'minLinesCountBeforeMultiline' => 1,
			'maxLinesCountBeforeMultiline' => 2,
			'minLinesCountBeforeWithComment' => 2,
			'maxLinesCountBeforeWithComment' => 2,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 4, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 9, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($report, 24, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);

		self::assertAllFixedInFile($report);
	}

	public function testInGlobalNamespaceNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/constantSpacingInGlobalNamespaceNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

}
