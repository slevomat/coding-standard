<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class SpreadOperatorSpacingSniffTest extends TestCase
{

	public function testDefaultSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/spreadOperatorSpacingDefaultSettingsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDefaultSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/spreadOperatorSpacingDefaultSettingsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, SpreadOperatorSpacingSniff::CODE_INCORRECT_SPACES_AFTER_OPERATOR);
		self::assertSniffError($report, 5, SpreadOperatorSpacingSniff::CODE_INCORRECT_SPACES_AFTER_OPERATOR);

		self::assertAllFixedInFile($report);
	}

	public function testModifiedSettingsNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/spreadOperatorSpacingModifiedSettingsNoErrors.php', [
			'spacesCountAfterOperator' => 2,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/spreadOperatorSpacingModifiedSettingsErrors.php', [
			'spacesCountAfterOperator' => 2,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 3, SpreadOperatorSpacingSniff::CODE_INCORRECT_SPACES_AFTER_OPERATOR);
		self::assertSniffError($report, 5, SpreadOperatorSpacingSniff::CODE_INCORRECT_SPACES_AFTER_OPERATOR);

		self::assertAllFixedInFile($report);
	}

}
