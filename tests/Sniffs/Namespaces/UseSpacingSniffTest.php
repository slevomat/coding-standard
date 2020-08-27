<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class UseSpacingSniffTest extends TestCase
{

	public function testNoUseNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingNoUse.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testOneUseNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingOneUse.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsWithCommentsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsWithCommentsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsWithCommentsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsWithCommentsErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 7, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 12, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 18, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE);
		self::assertSniffError($report, 18, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testDefaultSettingsWithInlineCommentsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsWithInlineCommentsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsWithInlineCommentsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsWithInlineCommentsErrors.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 8, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 12, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE);
		self::assertSniffError($report, 12, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithDefaultSettingsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 2, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 4, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testAfterOpenTagNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingAfterOpenTagNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testAfterOpenTagErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingAfterOpenTagErrors.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 2, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithModifiedSettingsNoErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBetweenUseTypes' => 1,
			'linesCountAfterLastUse' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingWithModifiedSettingsErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBetweenUseTypes' => 1,
			'linesCountAfterLastUse' => 2,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 4, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 5, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 8, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 8, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testMultipleUseTypesErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingMultipleUseTypesErrors.php', [
			'linesCountBeforeFirstUse' => 1,
			'linesCountBetweenUseTypes' => 1,
			'linesCountAfterLastUse' => 1,
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 5, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 6, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 8, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 9, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);

		self::assertAllFixedInFile($report);
	}

	public function testNoLineBeforeUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingNoLineBeforeUse.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
	}

	public function testNoLineAfterUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingNoLineAfterUse.php');

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 3, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);
	}

	public function testNoCodeAfterUse(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingNoCodeAfterUse.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testSameUseTypesErrors(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/useSpacingSameUseTypesErrors.php',
			[],
			[UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE]
		);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE);
		self::assertSniffError($report, 12, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE);
		self::assertSniffError($report, 20, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE);

		self::assertAllFixedInFile($report);
	}

	public function testMoreNamespaces(): void
	{
		$report = self::checkFile(__DIR__ . '/data/useSpacingMoreNamespacesErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 4, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 6, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);
		self::assertSniffError($report, 15, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 17, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 19, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE);
		self::assertSniffError($report, 19, UseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

}
