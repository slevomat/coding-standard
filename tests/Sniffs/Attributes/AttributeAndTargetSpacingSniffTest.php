<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;

class AttributeAndTargetSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributeAndTargetSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributeAndTargetSpacingErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);
		self::assertSniffError($report, 12, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);
		self::assertSniffError($report, 18, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributeAndTargetSpacingModifiedSettingsNoErrors.php', [
			'linesCount' => 1,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributeAndTargetSpacingModifiedSettingsErrors.php', [
			'linesCount' => 1,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 6, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);
		self::assertSniffError($report, 11, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);
		self::assertSniffError($report, 18, AttributeAndTargetSpacingSniff::CODE_INCORRECT_LINES_COUNT_BETWEEN_ATTRIBUTE_AND_TARGET);

		self::assertAllFixedInFile($report);
	}

	public function testInvalidAttributePriorPhp80(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributeAndTargetSpacingInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

}
