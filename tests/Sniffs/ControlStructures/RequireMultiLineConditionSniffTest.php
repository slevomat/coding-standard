<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireMultiLineConditionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION,
			'Condition of "if" should be split to more lines so each condition part is on its own line.'
		);
		self::assertSniffError(
			$report,
			6,
			RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION,
			'Condition of "elseif" should be split to more lines so each condition part is on its own line.'
		);
		self::assertSniffError(
			$report,
			10,
			RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION,
			'Condition of "while" should be split to more lines so each condition part is on its own line.'
		);
		self::assertSniffError(
			$report,
			18,
			RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION,
			'Condition of "do-while" should be split to more lines so each condition part is on its own line.'
		);
		self::assertSniffError(
			$report,
			20,
			RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION,
			'Condition of "if" should be split to more lines so each condition part is on its own line.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testForAllConditionsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionAllConditionsErrors.php', [
			'minLineLength' => 0,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION);

		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWhenDisabledIfControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionWhenDisabledIfControlStructureNoErrors.php', [
			'checkedControlStructures' => ['while'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWhenDisabledDoControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionWhenDisabledDoControlStructureNoErrors.php', [
			'checkedControlStructures' => ['while'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWhenDisabledWhileControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionWhenDisabledWhileControlStructureNoErrors.php', [
			'checkedControlStructures' => ['do'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testFixerWhenBooleanOperatorOnPreviousLineEnabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionFixerWhenBooleanOperatorOnPreviousLineEnabled.php', [
			'booleanOperatorOnPreviousLine' => true,
		]);

		self::assertAllFixedInFile($report);
	}

	public function testWithAlwaysSplitAllConditionPartsEnabledNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionWithAlwaysSplitAllConditionPartsEnabledNoErrors.php', [
			'alwaysSplitAllConditionParts' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testWithAlwaysSplitAllConditionPartsEnabledErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireMultiLineConditionWithAlwaysSplitAllConditionPartsEnabledErrors.php', [
			'alwaysSplitAllConditionParts' => true,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireMultiLineConditionSniff::CODE_REQUIRED_MULTI_LINE_CONDITION);

		self::assertAllFixedInFile($report);
	}

}
