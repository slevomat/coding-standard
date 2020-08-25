<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class TraitUseSpacingSniffTest extends TestCase
{

	public function testNoUses(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingNoUses.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoTraitUses(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingNoTraitUses.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingDefaultSettingsErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 5, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 7, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 18, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 18, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testOneUseDefaultSettingNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingOneUseDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testOneUseDefaultSettingErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingOneUseDefaultSettingsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 5, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testDefaultSettingWithCommentsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingDefaultSettingsWithCommentsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingWithCommentsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingDefaultSettingsWithCommentsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 8, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 14, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsNoErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 2,
			'linesCountAfterLastUseWhenLastInClass' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 2,
			'linesCountAfterLastUseWhenLastInClass' => 2,
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 6, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 7, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 12, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 13, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 13, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingWhenUseIsFirstInClassNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsWhenUseIsLastInClassNoErrors.php', [
			'linesCountBeforeFirstUse' => 2,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 0,
			'linesCountAfterLastUseWhenLastInClass' => 0,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingWhenUseIsFirstInClassErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsWhenUseIsLastInClassErrors.php', [
			'linesCountBeforeFirstUse' => 2,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 0,
			'linesCountAfterLastUseWhenLastInClass' => 0,
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 6, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 7, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 10, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingWhenUseIsLastInClassNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsWhenUseIsLastInClassNoErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 2,
			'linesCountAfterLastUseWhenLastInClass' => 0,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingWhenUseIsLastInClassErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsWhenUseIsLastInClassErrors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBeforeFirstUseWhenFirstInClass' => 0,
			'linesCountBetweenUses' => 1,
			'linesCountAfterLastUse' => 2,
			'linesCountAfterLastUseWhenLastInClass' => 0,
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 6, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);
		self::assertSniffError($report, 7, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 10, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES);
		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingWhenUseIsLastInClass2Errors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/traitUseSpacingModifiedSettingsWhenUseIsLastInClass2Errors.php', [
			'linesCountBeforeFirstUse' => 0,
			'linesCountBeforeFirstUseWhenFirstInClass' => 1,
			'linesCountBetweenUses' => 0,
			'linesCountAfterLastUse' => 1,
			'linesCountAfterLastUseWhenLastInClass' => 0,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 11, TraitUseSpacingSniff::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE);

		self::assertAllFixedInFile($report);
	}

}
