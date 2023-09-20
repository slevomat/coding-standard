<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireSingleLineConditionSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError(
			$report,
			4,
			RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION,
			'Condition of "if" should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			9,
			RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION,
			'Condition of "elseif" should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			16,
			RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION,
			'Condition of "while" should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			25,
			RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION,
			'Condition of "do-while" should be placed on a single line.'
		);
		self::assertSniffError(
			$report,
			30,
			RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION,
			'Condition of "if" should be placed on a single line.'
		);

		self::assertAllFixedInFile($report);
	}

	public function testForAllConditionsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionAllConditionsErrors.php', [
			'maxLineLength' => 0,
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 4, RequireSingleLineConditionSniff::CODE_REQUIRED_SINGLE_LINE_CONDITION);

		self::assertAllFixedInFile($report);
	}

	public function testWhenDisabledSimpleConditionsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionWhenDisabledSimpleConditionsNoErrors.php', [
			'alwaysForSimpleConditions' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWhenDisabledIfControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionWhenDisabledIfControlStructureNoErrors.php', [
			'checkedControlStructures' => ['while'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWhenDisabledDoControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionWhenDisabledDoControlStructureNoErrors.php', [
			'checkedControlStructures' => ['while'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsWhenDisabledWhileControlStructure(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionWhenDisabledWhileControlStructureNoErrors.php', [
			'checkedControlStructures' => ['do'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testNoErrorsLiveCoding(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireSingleLineConditionLiveCoding.php');
		self::assertNoSniffErrorInFile($report);
	}

}
